<?php

class SystemMessage extends TRecord
{
    const TABLENAME  = 'system_message';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    private SystemUsers $system_user;
    private SystemUsers $system_user_to;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('system_user_to_id');
        parent::addAttribute('subject');
        parent::addAttribute('message');
        parent::addAttribute('dt_message');
        parent::addAttribute('checked');
    
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
    /**
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_user_to(SystemUsers $object)
    {
        $this->system_user_to = $object;
        $this->system_user_to_id = $object->id;
    }

    /**
     * Method get_system_user_to
     * Sample of usage: $var->system_user_to->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_user_to()
    {
    
        // loads the associated object
        if (empty($this->system_user_to))
            $this->system_user_to = new SystemUsers($this->system_user_to_id);
    
        // returns the associated object
        return $this->system_user_to;
    }

    public function get_user_from()
    {
        return SystemUsers::findInTransaction('permission', $this->system_user_id);
    }

    public function get_user_to()
    {
        return SystemUsers::findInTransaction('permission', $this->system_user_to_id);
    }

    public function get_user_mixed()
    {
        if ($this->system_user_id == TSession::getValue('userid'))
        {
            return $this->get_user_to();
        }
        else
        {
            return $this->get_user_from();
        }
    }

}

