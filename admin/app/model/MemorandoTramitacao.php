<?php

class MemorandoTramitacao extends TRecord
{
    const TABLENAME  = 'memorando_tramitacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    public function __construct($id = null, $callObjectLoad = true)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('memorando_id');
        parent::addAttribute('memorando_destinatario_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('status_resultante');
        parent::addAttribute('acao');
        parent::addAttribute('descricao');
        parent::addAttribute('data_evento');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public function get_system_users()
    {
        return !empty($this->system_users_id) ? new SystemUsers($this->system_users_id) : null;
    }

    public function get_departamento_unit()
    {
        return !empty($this->departamento_unit_id) ? new DepartamentoUnit($this->departamento_unit_id) : null;
    }

    public function get_usuario_nome()
    {
        $user = $this->get_system_users();
        return $user instanceof SystemUsers ? (string) $user->name : '';
    }

    public function get_departamento_nome()
    {
        $department = $this->get_departamento_unit();
        return $department instanceof DepartamentoUnit ? (string) $department->name : '';
    }
}
