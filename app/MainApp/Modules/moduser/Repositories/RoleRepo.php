<?php

namespace App\MainApp\Modules\moduser\Repositories;

use App\MainApp\Modules\moduser\Models\Role;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;

use App\Base\BaseRepository;

class RoleRepo extends BaseRepository
{
    public $error = '';
    
    public function __construct(Role $model)
    {        
        $this->model = $model;        
    }
    
    public function listRole($filter=false, $offset=0,$limit=0,$orderBy=false)
    {
        if (!$filter) $filter = [];
        $filter['searchField'] = ['name'];
        $filter['hiddenColumn'] = ['created_at','updated_at'];
        $model = Role::with(['tenantGroup','tenant']);

        if (isset($filter['level']) && $filter['level']) {
            $model = $model->where('level', $filter['level']);
            unset($filter['level']);
        }

        if (isset($filter['user_level']) && $filter['user_level']) {
            $model = $model->where('level','not like', $filter['user_level']);
            unset($filter['user_level']);
        }

        if (isset($filter['max_level']) && $filter['max_level']) {
            $model = $model->where('level','>', $filter['max_level']);
            unset($filter['max_level']);
        } 

        $data = $this->_list(
            $model, $filter, $offset, $limit, $orderBy
        );  
        return $data;
    }

    public function getRole($where)
    {
        return $this->_getOne(new Role,$where);
    }

    public function createRole($data)
    {
        return $this->_create(new Role, $data);
    }
    public function updateRole($where, $data)
    {
        return $this->_update(new Role, $where, $data);
    }
    public function deleteRole($id)
    {
        $this->_delete(new Role, ['id', $id]);
        return true;
    }
   
    
}