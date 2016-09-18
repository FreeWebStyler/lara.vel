<?php
//namespace app\Seed;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call('PostSeeder');
    }
}

class PostSeeder extends Seeder {
    
    /*public function run(){
        DB::table('Posts')->delete();
        Post::create([
            'title'=>'First Post',
            'slug'=>'first-post',
            'excerpt'=>'<b>First Post body</b>',
            'content'=>'<b>Content First Post body</b>',
            'published'=>true,
            'published_at'=>DB::raw('CURRENT_TIMESTAMP'),
        ]);
        
        Post::create([
            'title'=>'Second Post',
            'slug'=>'second-post',
            'excerpt'=>'<b>Second Post body</b>',
            'content'=>'<b>Content Second Post body</b>',
            'published'=>true,
            'published_at'=>DB::raw('CURRENT_TIMESTAMP'),
        ]);

    }*/
    
     public function run(){
        DB::table('Posts')->delete();
        Post::create([
            'title'=>'First Post',
            'slug'=>'first-post',
            'excerpt'=>'<b>First Post body</b>',
            'content'=>'<b>Content First Post body</b>',
            'published'=>true,
            'published_at'=>DB::raw('CURRENT_TIMESTAMP'),
        ]);
        
        Post::create([
            'title'=>'Second Post',
            'slug'=>'second-post',
            'excerpt'=>'<b>Second Post body</b>',
            'content'=>'<b>Content Second Post body</b>',
            'published'=>true,
            'published_at'=>DB::raw('CURRENT_TIMESTAMP'),
        ]);
        
            //DB::table('Users')->delete();        
        User::create([
            //'id'=>5,
            'name'=>'ad',
            'email'=>'nevidlovskyi@yandex.ru',
            'password'=>'$2y$10$uDWKugcJQV94a2t.rd6cyeCeNKWsOOU7nMPXbLD6teM.cOxvu9tWG',
            'remember_token'=>null,
            //'created_at'=>"'".time()."'",//DB::raw('CURRENT_TIMESTAMP'),
            'created_at'=>time(),//DB::raw('CURRENT_TIMESTAMP'),
            //'updated_at'=>"'".time()."'"//DB::raw('CURRENT_TIMESTAMP'),
            'updated_at'=>time()//DB::raw('CURRENT_TIMESTAMP'),
    ]);

    }
    

}