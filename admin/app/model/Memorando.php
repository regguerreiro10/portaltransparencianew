<?php

class Memorando extends TRecord
{
    const TABLENAME  = 'memorando';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';
    const DELETEDAT  = 'deleted_at';

    public function __construct($id = null, $callObjectLoad = true)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('numero_memorando');
        parent::addAttribute('assunto');
        parent::addAttribute('texto_memorando');
        parent::addAttribute('status');
        parent::addAttribute('tipo');
        parent::addAttribute('template_codigo');
        parent::addAttribute('template_nome');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('entidade_id');
        parent::addAttribute('system_users_remetente_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('data_memorando');
        parent::addAttribute('memorando_pai_id');
        parent::addAttribute('pode_virar_processo');
        parent::addAttribute('processo_referencia');
        parent::addAttribute('downloads');
        parent::addAttribute('lido_em');
        parent::addAttribute('arquivado_em');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public function get_system_users_remetente()
    {
        return !empty($this->system_users_remetente_id) ? new SystemUsers($this->system_users_remetente_id) : null;
    }

    public function get_departamento_unit()
    {
        return !empty($this->departamento_unit_id) ? new DepartamentoUnit($this->departamento_unit_id) : null;
    }

    public function get_remetente_nome()
    {
        $user = $this->get_system_users_remetente();
        return $user instanceof SystemUsers ? (string) $user->name : '';
    }

    public function get_departamento_origem_nome()
    {
        $department = $this->get_departamento_unit();
        return $department instanceof DepartamentoUnit ? (string) $department->name : '';
    }

    public function get_memorando_pai()
    {
        return !empty($this->memorando_pai_id) ? new Memorando($this->memorando_pai_id) : null;
    }

    public function get_memorando_pai_numero()
    {
        $parent = $this->get_memorando_pai();
        return $parent instanceof Memorando ? (string) $parent->numero_memorando : '';
    }

    public function get_origem_vinculo()
    {
        if (empty($this->memorando_pai_id)) {
            return 'Novo';
        }

        $numeroPai = $this->memorando_pai_numero ?: '#' . $this->memorando_pai_id;

        if ($this->tipo === 'Resposta') {
            return 'Resposta de ' . $numeroPai;
        }

        if ($this->tipo === 'Encaminhamento') {
            return 'Encaminhado de ' . $numeroPai;
        }

        return ($this->tipo ?: 'Vinculado') . ' de ' . $numeroPai;
    }

    public function getMemorandoAnexos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('memorando_id', '=', $this->id));
        $criteria->setProperty('order', 'ordem');
        $criteria->setProperty('direction', 'asc');
        return MemorandoAnexo::getObjects($criteria);
    }

    public function getMemorandoDestinatarios()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('memorando_id', '=', $this->id));
        $criteria->setProperty('order', 'id');
        $criteria->setProperty('direction', 'asc');
        return MemorandoDestinatario::getObjects($criteria);
    }

    public function getMemorandoTramitacoes()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('memorando_id', '=', $this->id));
        $criteria->setProperty('order', 'data_evento');
        $criteria->setProperty('direction', 'desc');
        return MemorandoTramitacao::getObjects($criteria);
    }

    public function get_destinatarios_resumo()
    {
        $nomes = [];
        foreach ($this->getMemorandoDestinatarios() as $destinatario) {
            $nomes[] = $destinatario->destinatario_nome;
        }

        return implode(', ', array_unique(array_filter($nomes)));
    }

    public function get_departamentos_destino_resumo()
    {
        $departamentos = [];
        foreach ($this->getMemorandoDestinatarios() as $destinatario) {
            $departamentos[] = $destinatario->departamento_destino_nome;
        }

        return implode(', ', array_unique(array_filter($departamentos)));
    }
}
