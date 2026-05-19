<?php

class MemorandoHelper
{
    public static function getTemplates(): array
    {
        return [
            'padrao' => 'Memorando padrao',
            'solicitacao' => 'Solicitacao interna',
            'resposta' => 'Resposta oficial',
            'encaminhamento' => 'Encaminhamento',
            'urgente' => 'Comunicacao urgente',
        ];
    }

    public static function getTemplateContent(?string $codigo): string
    {
        $templates = [
            'padrao' => '<p><strong>Senhor(a),</strong></p><p>Encaminhamos o presente memorando para conhecimento e providencias.</p><p>Atenciosamente,</p>',
            'solicitacao' => '<p><strong>Prezados,</strong></p><p>Solicitamos a adocao das providencias necessarias sobre o assunto informado.</p><p>Favor retornar com parecer.</p>',
            'resposta' => '<p><strong>Em resposta ao memorando recebido,</strong></p><p>Informamos que as medidas cabiveis estao em andamento.</p>',
            'encaminhamento' => '<p><strong>Encaminha-se</strong> este memorando para analise e manifestacao do setor competente.</p>',
            'urgente' => '<p><strong>Com prioridade alta,</strong></p><p>Solicitamos atendimento imediato ao tema apresentado.</p>',
        ];

        return $templates[$codigo] ?? '';
    }

    public static function getStatusOptions(): array
    {
        return [
            '' => 'Todos',
            'Enviado' => 'Enviado',
            'Recebido' => 'Recebido',
            'Lido' => 'Lido',
            'Respondido' => 'Respondido',
            'Encaminhado' => 'Encaminhado',
            'Arquivado' => 'Arquivado',
            'Recuperado' => 'Recuperado',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            'Enviado' => '#1d4ed8',
            'Recebido' => '#0f766e',
            'Lido' => '#15803d',
            'Respondido' => '#7c3aed',
            'Encaminhado' => '#0f766e',
            'Arquivado' => '#6b7280',
            'Recuperado' => '#b45309',
        ];
    }

    public static function getStatusColor(?string $status): string
    {
        return self::getStatusColors()[$status] ?? '#334155';
    }

    public static function applyStatusColor(Memorando $memorando): void
    {
        // Colors are calculated in the UI; the current memorando schema has no status_cor column.
    }

    public static function getCurrentUserContext(): array
    {
        $openedTransaction = false;
        if (!TTransaction::get()) {
            TTransaction::open('minierp');
            MemorandoSchemaHelper::ensureSchema();
            $openedTransaction = true;
        }

        $userId = (int) TSession::getValue('userid');
        $login = (string) TSession::getValue('login');
        $name = $login ?: 'Usuario';
        $unitId = (int) TSession::getValue('idunit');
        $entityId = (int) TSession::getValue('entidade');
        $departmentIds = [];
        $departmentNames = [];

        if ($userId > 0) {
            $user = SystemUsers::find($userId);
            if ($user instanceof SystemUsers && !empty($user->name)) {
                $name = $user->name;
            }

            $conn = TTransaction::get();
            $sql = "SELECT sdu.departamento_unit_id, du.name
                    FROM system_user_departamento_unit sdu
                    INNER JOIN departamento_unit du ON du.id = sdu.departamento_unit_id
                    WHERE sdu.system_users_id = ?";
            if ($unitId > 0) {
                $sql .= " AND du.system_unit_id = " . $unitId;
            }
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userId]);

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $departmentIds[] = (int) $row['departamento_unit_id'];
                $departmentNames[] = $row['name'];
            }
        }

        if (TSession::getValue('depunitid') && !in_array((int) TSession::getValue('depunitid'), $departmentIds, true)) {
            $departmentIds[] = (int) TSession::getValue('depunitid');
            $departmentNames[] = self::getDepartmentNameById(TSession::getValue('depunitid'));
        }

        $context = [
            'user_id' => $userId,
            'login' => $login,
            'name' => $name,
            'system_unit_id' => $unitId ?: null,
            'entidade_id' => $entityId ?: null,
            'department_ids' => array_values(array_unique(array_filter($departmentIds))),
            'department_names' => array_values(array_unique(array_filter($departmentNames))),
            'primary_department_id' => TSession::getValue('depunitid') ?: ($departmentIds[0] ?? null),
            'primary_department_name' => $departmentNames[0] ?? (self::getDepartmentNameById(TSession::getValue('depunitid')) ?: 'Departamento nao vinculado'),
        ];

        if ($openedTransaction) {
            TTransaction::close();
        }

        return $context;
    }

    public static function isAdmin(): bool
    {
        return TSession::getValue('login') === 'admin';
    }

    public static function canAccessMemorando(Memorando $memorando): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        $context = self::getCurrentUserContext();

        if ((int) $memorando->system_users_remetente_id === (int) $context['user_id']) {
            return true;
        }

        if (!empty($context['department_ids']) && in_array((int) $memorando->departamento_unit_id, $context['department_ids'])) {
            return true;
        }

        foreach ($memorando->getMemorandoDestinatarios() as $destinatario) {
            if ((int) $destinatario->system_users_id === (int) $context['user_id']) {
                return true;
            }

            if (!empty($context['department_ids']) && in_array((int) $destinatario->departamento_unit_id, $context['department_ids'])) {
                return true;
            }
        }

        return false;
    }

    public static function canEditMemorando(Memorando $memorando): bool
    {
        return self::isAdmin() || (int) $memorando->system_users_remetente_id === (int) TSession::getValue('userid');
    }

    public static function buildAccessFilterSql(): ?string
    {
        if (self::isAdmin()) {
            return null;
        }

        $context = self::getCurrentUserContext();
        $conditions = [];
        $userId = (int) $context['user_id'];

        if ($userId > 0) {
            $conditions[] = "m.system_users_remetente_id = {$userId}";
            $conditions[] = "md.system_users_id = {$userId}";
        }

        if (!empty($context['department_ids'])) {
            $ids = implode(',', array_map('intval', $context['department_ids']));
            $conditions[] = "m.departamento_unit_id IN ({$ids})";
            $conditions[] = "md.departamento_unit_id IN ({$ids})";
        }

        if (!$conditions) {
            return '(SELECT 0)';
        }

        return "(SELECT DISTINCT m.id
                 FROM memorando m
                 LEFT JOIN memorando_destinatario md ON md.memorando_id = m.id
                 WHERE " . implode(' OR ', $conditions) . ')';
    }

    public static function getUserNameById($userId): string
    {
        if (empty($userId)) {
            return '';
        }

        $openedTransaction = false;
        if (!TTransaction::get()) {
            TTransaction::open('minierp');
            $openedTransaction = true;
        }

        $user = SystemUsers::find((int) $userId);
        $name = $user instanceof SystemUsers ? (string) $user->name : '';

        if ($openedTransaction) {
            TTransaction::close();
        }

        return $name;
    }

    public static function getDepartmentNameById($departmentId): string
    {
        if (empty($departmentId)) {
            return '';
        }

        $openedTransaction = false;
        if (!TTransaction::get()) {
            TTransaction::open('minierp');
            $openedTransaction = true;
        }

        $department = DepartamentoUnit::find((int) $departmentId);
        $name = $department instanceof DepartamentoUnit ? (string) $department->name : '';

        if ($openedTransaction) {
            TTransaction::close();
        }

        return $name;
    }

    public static function updateOverallStatus(Memorando $memorando): void
    {
        $statuses = [];
        foreach ($memorando->getMemorandoDestinatarios() as $destinatario) {
            $statuses[] = $destinatario->status;
        }

        $status = 'Enviado';
        if (in_array('Respondido', $statuses, true)) {
            $status = 'Respondido';
        } elseif (in_array('Lido', $statuses, true)) {
            $status = 'Lido';
        } elseif (in_array('Recebido', $statuses, true)) {
            $status = 'Recebido';
        }

        if ($statuses && count(array_unique($statuses)) === 1 && $statuses[0] === 'Arquivado') {
            $status = 'Arquivado';
            $memorando->arquivado_em = date('Y-m-d H:i:s');
        }

        if ($memorando->status === 'Recuperado') {
            $status = 'Recuperado';
        }

        $memorando->status = $status;
        self::applyStatusColor($memorando);
        if ($status === 'Lido' && empty($memorando->lido_em)) {
            $memorando->lido_em = date('Y-m-d H:i:s');
        }
        $memorando->updated_at = date('Y-m-d H:i:s');
        $memorando->store();
    }

    public static function createTramitacao(
        int $memorandoId,
        string $acao,
        ?string $status,
        ?string $descricao = null,
        ?int $memorandoDestinatarioId = null
    ): void {
        $context = self::getCurrentUserContext();
        $tramite = new MemorandoTramitacao();
        $tramite->memorando_id = $memorandoId;
        $tramite->memorando_destinatario_id = $memorandoDestinatarioId;
        $tramite->system_users_id = $context['user_id'];
        $tramite->departamento_unit_id = $context['primary_department_id'];
        $tramite->acao = $acao;
        $tramite->status_resultante = $status;
        $tramite->descricao = $descricao;
        $tramite->data_evento = date('Y-m-d H:i:s');
        $tramite->created_at = date('Y-m-d H:i:s');
        $tramite->store();
    }
}
