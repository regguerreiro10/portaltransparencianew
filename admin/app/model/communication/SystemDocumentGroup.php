<?php

class SystemDocumentGroup extends TRecord
{
    const TABLENAME  = 'system_document_group';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    private SystemDocument $document;
    private SystemGroup $system_group;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('document_id');
        parent::addAttribute('system_group_id');
            
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
     * Method set_system_group
     * Sample of usage: $var->system_group = $object;
     * @param $object Instance of SystemGroup
     */
    public function set_system_group(SystemGroup $object)
    {
        $this->system_group = $object;
        $this->system_group_id = $object->id;
    }

    /**
     * Method get_system_group
     * Sample of usage: $var->system_group->attribute;
     * @returns SystemGroup instance
     */
    public function get_system_group()
    {
    
        // loads the associated object
        if (empty($this->system_group))
            $this->system_group = new SystemGroup($this->system_group_id);
    
        // returns the associated object
        return $this->system_group;
    }

    
}

