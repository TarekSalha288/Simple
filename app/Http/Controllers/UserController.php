<?php

namespace App\Http\Controllers;
use App\UploadImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;

class UserController extends Controller
{
    use UploadImageTrait;
    public function follow($id){
        DB::table('user_folowers')->insert([
            'user_id'=> auth()->user()->id,
            'follower_id'=>$id,
        ]);
    }

    public function unfollow($id){
        DB::table('user_folowers')->where('user_id',auth()->user()->id)->where('follower_id',$id)->delete();
    }
    public function showFollowers(){
        $followers=User::find(auth()->user()->id)->followers;
        return response()->json(['followers'=>$followers]);
    }
    public function showFollowings(){
        $followings=User::find(auth()->user()->id)->followings;
        return response()->json(['Followings'=>$followings]);
    }
    public function myPosts(){
       $posts=User::find(auth()->user()->id)->posts;
       return response()->json(['My Posts'=>$posts]);
    }
    public function edit($id){
        $user=User::where('id',$id)->first();
        return response()->json(['user_info'=>$user]);
    }
    public function update(Request $request, $id){
        $request->validate(['email'=>'required|email|unique:users','name'=>'required|unique:users','password'=>'required|min:8']);
        $path=null;
        if($request->path !=null){
        $path=$this->uploadImage($request->path,"users");}
        User::where('id',auth()->user()->id)->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'image_path'=>$path,
        ]);
    }
    public function deleteAccount(){
        User::destroy(auth()->user()->id);
    }
    public function suggestedUsers(){
        $follwers[]=User::find(auth()->user()->id)->followers;

        $users[]=User::find(auth()->user()->id)->followers->pluck('followers');
        return response()->json(['followers'=>$follwers,'followersOfFollowers'=>$users]);
    }
    ///////////Admin Functions/////////////////
    public function deleteUser($id){
    User::destroy($id);
    return response()->json("Deleted User Done");
    }
    public function deletePost($id){
        Post::destroy($id);
        return response()->json("Deleted Post Done");
    }
    public function allUsers(){
        $users=User::all();
        return response()->json($users);
    }
    public function allPosts(){
        $posts=Post::all();
        return response()->json($posts);
    }
    public function makeAdmin($id){
     User::findOrFail($id)->update(['status'=>1]);
     return response()->json('User Become Admin');
    }

}
