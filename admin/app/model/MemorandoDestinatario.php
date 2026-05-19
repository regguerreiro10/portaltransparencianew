<?php

class MemorandoDestinatario extends TRecord
{
    const TABLENAME  = 'memorando_destinatario';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    public function __construct($id = null, $callObjectLoad = true)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('memorando_id');
        parent::addAttribute('tipo_destino');
        parent::addAttribute('departamento_unit_id');
        parent::addAttribute('system_users_id');
        parent::addAttribute('status');
        parent::addAttribute('recebido_em');
        parent::addAttribute('lido_em');
        parent::addAttribute('respondido_em');
        parent::addAttribute('arquivado_em');
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

    public function get_destinatario_nome()
    {
        $user = $this->get_system_users();
        return $user instanceof SystemUsers ? (string) $user->name : '';
    }

    public function get_departamento_destino_nome()
    {
        $department = $this->get_departamento_unit();
        return $department instanceof DepartamentoUnit ? (string) $department->name : '';
    }
}
