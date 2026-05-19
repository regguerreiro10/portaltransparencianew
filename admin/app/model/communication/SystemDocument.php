<?php

class SystemDocument extends TRecord
{
    const TABLENAME  = 'system_document';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    private SystemDocumentCategory $category;
    private SystemUsers $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('category_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('title');
        parent::addAttribute('description');
        parent::addAttribute('submission_date');
        parent::addAttribute('archive_date');
        parent::addAttribute('filename');
    
    }

    /**
     * Method set_system_document_category
     * Sample of usage: $var->system_document_category = $object;
     * @param $object Instance of SystemDocumentCategory
     */
    public function set_category(SystemDocumentCategory $object)
    {
        $this->category = $object;
        $this->category_id = $object->id;
    }

    /**
     * Method get_category
     * Sample of usage: $var->category->attribute;
     * @returns SystemDocumentCategory instance
     */
    public function get_category()
    {
    
        // loads the associated object
        if (empty($this->category))
            $this->category = new SystemDocumentCategory($this->category_id);
    
        // returns the associated object
        return $this->category;
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
     * Reset aggregates
     */
    public function clearParts()
    {
        if ($this->id)
        {
            // delete the related System_userSystem_user_group objects
            $criteria = new TCriteria;
            $criteria->add(new TFilter('document_id', '=', $this->id));
        
            $repository = new TRepository('SystemDocumentUser');
            $repository->delete($criteria);
        
            $repository = new TRepository('SystemDocumentGroup');
            $repository->delete($criteria);
        }   
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        // delete the related System_groupSystem_program objects
        $id = isset($id) ? $id : $this->id;
    
        $criteria = new TCriteria;
        $criteria->add(new TFilter('document_id', '=', $id));
    
        $repository = new TRepository('SystemDocumentUser');
        $repository->delete($criteria);
    
        $repository = new TRepository('SystemDocumentGroup');
        $repository->delete($criteria);  
    
        // delete the object itself
        parent::delete($id);
    }

    /**
     * Add a SystemGroup
     * @param $object Instance of SystemGroup
     */
    public function addSystemGroup(SystemGroup $systemgroup)
    {
        $object = new SystemDocumentGroup;
        $object->system_group_id = $systemgroup->id;
        $object->document_id = $this->id;
        $object->store();
    }

    /**
     * Add a SystemUsers
     * @param $object Instance of SystemUsers
     */
    public function addSystemUser(SystemUsers $systemuser)
    {
        $object = new SystemDocumentUser;
        $object->system_user_id = $systemuser->id;
        $object->document_id = $this->id;
        $object->store();
    }

    /**
     * @return Collection of SystemGroup
     */
    public function getSystemGroups()
    {
        $groups = array();
        $document_groups = SystemDocumentGroup::where('document_id', '=', $this->id)->load();
        if ($document_groups)
        {
            TTransaction::open('permission');
            foreach ($document_groups as $document_group)
            {
                $groups[] = new SystemGroup( $document_group->system_group_id );
            }
            TTransaction::close();
        }
        return $groups;
    }

    /**
     * @return Collection of SystemUsers' ids
     */
    public function getSystemUsersIds()
    {
        $users = $this->getSystemUsers();
        $user_ids = array();
        if ($users)
        {
            foreach ($users as $user)
            {
                $user_ids[] = $user->id;
            }
        }
        return $user_ids;
    }

    /**
     * @return Collection of SystemGroup' ids
     */
    public function getSystemGroupsIds()
    {
        $groups = $this->getSystemGroups();
        $group_ids = array();
        if ($groups)
        {
            foreach ($groups as $group)
            {
                $group_ids[] = $group->id;
            }
        }
        return $group_ids;
    }

    /**
     * @return Collection of SystemUserGroup
     */
    public function getSystemUsers()
    {
        $users = array();
        $document_users = SystemDocumentUser::where('document_id', '=', $this->id)->load();
        if ($document_users)
        {
            TTransaction::open('permission');
            foreach ($document_users as $document_user)
            {
                $users[] = new SystemUsers( $document_user->system_user_id );
            }
            TTransaction::close();
        }
        return $users;
    }

    /**
     * Check if the user has access to the document
     */
    public function hasUserAccess($userid)
    {
        return (SystemDocumentUser::where('system_user_id','=', $userid)
                                  ->where('document_id', '=', $this->id)->count() >0);
    }

    /**
     * Check if the group has access to the document
     */
    public function hasGroupAccess($usergroupids)
    {
        return (SystemDocumentGroup::where('system_group_id','IN', $usergroupids)
                                   ->where('document_id', '=', $this->id)->count() >0);
    }
    
}

