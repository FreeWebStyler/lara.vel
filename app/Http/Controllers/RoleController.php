<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Role;
use App\Models\Permission;
use App\User;
use Validator;

class RoleController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Role $roleModel){
        echo 333;
    }
    
    function creating(Role $roleModel){    
        /*$owner = new Role();
        $owner->name         = 'owner';
        $owner->display_name = 'Project Owner'; // optional
        $owner->description  = 'User is the owner of a given project'; // optional
        $owner->save();*/        
        $admin = new Role();
        $admin->name = 'admin';
        $admin->display_name = 'User Administrator'; // optional
        $admin->description = 'User is allowed to manage and edit other users'; // optional
        $admin=$admin->firstOrCreate(array('name'=>$admin->name));       
        
        /*$val = Validator::make(['name'=>$admin->name],['name' => 'unique:roles',]); if($val->fails()){
        //$admin->where('name','=','admin')->first()->get()->all(); //$admin::where('name','=','admin')->first();
        //$admin = $admin::whereName($admin->name)->first(); dd($admin);  //$admin=$admin->where('name','=',$admin->name)->first()->get();
        //echo $admin->toSql();  //$admin=$admin->where('name','=','admin')->first()->get();
        //$admin=$admin->where('name','=','admin'); echo '<br>'; echo $admin->toSql(); $admin=$admin->first();
        $admin=$admin->where('name','=','admin')->first(); // dd($admin);
        $admin->update();
        } else $admin->save();*/
        // dd($val); //$admin->update();

        
       /* $val = Validator::make(['name'=>'`ad`'], [
            'name' => 'unique:users,name|min:10',
       ]);*/     
       
        //dd($val);
        
        
       /* try {  $admin->save();  } catch ( \Illuminate\Database\QueryException $e) { var_dump($e->errorInfo ); 
       return $admin; 
        }*/

        /*try{ $admin->save(); }
            catch (Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            echo 222;
            if($errorCode == 1062){
                // houston, we have a duplicate entry problem
            }
            }
        
        echo 444; die;*/

        $user = User::where('name', '=', 'ad')->first();  
        //$user->attachRole($admin); // role attach alias // parameter can be an Role object, array, or id
        try { $user->attachRole($admin); } catch (\Illuminate\Database\QueryException $e) { }
        
        //$user->roles()->attach($admin->id); // id only // or eloquent's original technique
       
        $createPost = new Permission();
        $createPost->name         = 'create-post';
        $createPost->display_name = 'Create Posts'; // optional
        $createPost->description  = 'create new blog posts'; // optional
        //try { $createPost->save(); } catch (\Illuminate\Database\QueryException $e) { $createPost->update(); } //$createPost->save(); $createPost->update();
        $createPost=$createPost->firstOrCreate(array('name'=>$createPost->name));

        $editUser = new Permission();
        $editUser->name         = 'edit-user';
        $editUser->display_name = 'Edit Users'; // optional
        $editUser->description  = 'edit existing users'; // optional       
        //try { $editUser->save(); } catch (\Illuminate\Database\QueryException $e) { $editUser->update(); dd($editUser); }
        $editUser=$editUser->firstOrCreate(array('name'=>$editUser->name));

        $DeleteAllPosts['name']='deleteAll-posts';
        $DeleteAllPosts=Permission::firstOrCreate($DeleteAllPosts);
        try { $admin->attachPermissions(array($DeleteAllPosts, $createPost, $editUser)); } catch
        (\Illuminate\Database\QueryException $e) { }
        //$admin->attachPermission($createPost); $admin->attachPermission($editUser);

        //$admin->attachPermissions(array($createPost, $editUser));
        // equivalent to $admin->perms()->sync(array($createPost->id));
        
        //$owner->attachPermissions(array($createPost, $editUser));
        // equivalent to $owner->perms()->sync(array($createPost->id, $editUser->id));
       
        //$perm->display_name = 'Edit all posts'; // optional
        //$perm->description  = 'Edit all posts'; // optional
        $perm['name']='editall-posts';
        $perm=Permission::firstOrCreate($perm);
        try { $admin->attachPermissions(array($perm)); } catch (\Illuminate\Database\QueryException $e) { }
        
        echo 'Done!';
    }
    
/*$owner = new Role();
$owner->name         = 'owner';
$owner->display_name = 'Project Owner'; // optional
$owner->description  = 'User is the owner of a given project'; // optional
$owner->save();

$admin = new Role();
$admin->name         = 'admin';
$admin->display_name = 'User Administrator'; // optional
$admin->description  = 'User is allowed to manage and edit other users'; // optional
$admin->save();*/
}