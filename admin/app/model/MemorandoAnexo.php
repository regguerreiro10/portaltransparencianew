<?php

class MemorandoAnexo extends TRecord
{
    const TABLENAME  = 'memorando_anexo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    public function __construct($id = null, $callObjectLoad = true)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('memorando_id');
        parent::addAttribute('nome');
        parent::addAttribute('arquivo');
        parent::addAttribute('ordem');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }
}
