<?php namespace App\Models;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {
    static function getRole($id){
        
        $userRoles = new Role;
        $userRoles->table='role_user';
        //$userRoles = $userRoles->find($id)->first();
        $userRoles = $userRoles->where('user_id','=',$id)->get();
        //echo '<pre>';
        //print_r($userRoles); dd();
        foreach($userRoles as $userRole){
            //$roles[]=$userRole->role_id;
            //$role = new Role;
            $roles[]=Role::find($userRole->role_id)->first()->name;
        }
        return $roles;
        //::find($id)->roles;
       // return $userRoles = Role::find($id)->roles;
    }
}
