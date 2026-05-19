<?php

class SystemDocumentUser extends TRecord
{
    const TABLENAME  = 'system_document_user';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    private SystemDocument $document;
    private SystemUsers $system_user;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('document_id');
        parent::addAttribute('system_user_id');
            
    }

    /**
     * Method set_system_document
     * Sample of usage: $var->system_document = $object;
     * @param $object Instance of SystemDocument
     */
    public function set_document(SystemDocument $object)
    {
        $this->document = $object;
        $this->document_id = $object->id;
    }

    /**
     * Method get_document
     * Sample of usage: $var->document->attribute;
     * @returns SystemDocument instance
     */
    public function get_document()
    {
    
        // loads the associated object
        if (empty($this->document))
            $this->document = new SystemDocument($this->document_id);
    
        // returns the associated object
        return $this->document;
    }
    /**
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_user(SystemUsers $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }

    /**
     * Method get_system_user
     * Sample of usage: $var->system_user->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_user()
    {
    
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUsers($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
    }

    
}

