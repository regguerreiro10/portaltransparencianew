<?php

class SystemNotification extends TRecord
{
    const TABLENAME  = 'system_notification';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

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
        parent::addAttribute('action_url');
        parent::addAttribute('action_label');
        parent::addAttribute('icon');
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

    /**
     * Register notification
     */
    public static function register( $user_to, $subject, $message, $action, $label, $icon = null, $date = null)
    {
        if ($action instanceof TAction)
        {
            $action = $action->serialize(false);
        }
    
        TTransaction::open('communication');
        $object = new self;
        $object->system_user_id    = TSession::getValue('userid');
        $object->system_user_to_id = $user_to;
        $object->subject           = $subject;
        $object->message           = $message;
        $object->dt_message        = empty($date) ? date("Y-m-d H:i:s") : $date;
        $object->action_url        = $action;
        $object->action_label      = $label;
        $object->icon              = $icon;
        $object->checked           = 'N';
        $object->store();
        TTransaction::close();
    }
      /**
     * Register notification
     */
    public static function registerpedidofrotas( $user_to, $subject, $message, $action, $label, $icon = null, $date = null)
    {
        if ($action instanceof TAction)
        {
            $action = $action->serialize(false);
        }
    
        $object = new self;
        $object->system_user_id    = TSession::getValue('userid');
        $object->system_user_to_id = $user_to;
        $object->subject           = $subject;
        $object->message           = $message;
        $object->dt_message        = empty($date) ? date("Y-m-d H:i:s") : $date;
        $object->action_url        = $action;
        $object->action_label      = $label;
        $object->icon              = $icon;
        $object->checked           = 'N';
        $object->store();
    }

}

