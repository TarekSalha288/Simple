<?php

namespace App\Http\Controllers;
use App\UploadImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use  Illuminate\Support\Facades\Auth;
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
  public function editupdate(){
    return view ('update');
  }
  public function update()
  {
      $validator = Validator::make(request()->all(), [
          'name' => 'required|unique:users,name,' . Auth::id(),
          'email' => 'required|email|unique:users,email,' . Auth::id(),
          'password' => 'required|confirmed|min:8',
          'image_path' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      ]);

      if ($validator->fails()) {
          return response()->json($validator->errors()->toJson(), 400);
      }

      $user = User::find(Auth::id());
      $user->name = request()->name;
      $user->email = request()->email;
      $user->password = bcrypt(request()->password);

      if (request()->hasFile('image')){
             $destenation='public/imgs/users/'.$user->image_path;
             if (file_exists($destenation)){
            File::delete($destenation);
            }
            $path=$this->uploadImage(request(),'users');
            $user->image_path = $path;
          }



      $user->save();


       return response()->json(['success' => 'User updated successfully']);

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
