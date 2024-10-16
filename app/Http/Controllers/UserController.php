<?php

namespace App\Http\Controllers;
use App\UploadImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

use  Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    use UploadImageTrait;
    public function follow($id){
        DB::table('user_folowers')->insert([
            'user_id'=> Auth::user()->id,
            'follower_id'=>$id,
        ]);
    }
    public function unfollow($id){
        DB::table('user_folowers')->where('user_id',Auth::user()->id)->where('follower_id',$id)->delete();
    }
    public function showFollowers(){
        $followers=User::find(Auth::user()->id)->followers;
        return response()->json(['followers'=>$followers]);
    }
    public function showFollowings(){
        $followings=User::find(Auth::user()->id)->followings;
        return response()->json(['Followings'=>$followings]);
    }
    public function myPosts(){
       $posts=User::find(Auth::user()->id)->posts;
       return response()->json(['My Posts'=>$posts]);
    }
    public function edit($id){
        $user=User::where('id',$id)->first();
        $flag='follow';
        $following=DB::table('user_folowers')->where('user_id',Auth::user()->id)->where('follower_id',$id)->first();
        $follower=DB::table('user_folowers')->where('follower_id',Auth::user()->id)->where('user_id',$id)->first();
         if($following){
            $flag='following';
         }
         if($follower){
            $flag= 'followback';
         }

        return response()->json(['user_info'=>$user,'follow_status'=>$flag]);
        //return $flag;
    }
public function userPosts($id){
            $posts = User::find($id)->posts;
            $allposts = [];
            $user = Auth::user();

            foreach ($posts as $post) {
                $post->user;
                $postData = $post->toArray();
                unset($postData['likes']); // Ensure 'likes' is removed
                $num_fusers = $post->fusers()->count();
                $num_likes = $post->likes()->count();
                $num_comments = $post->comments()->count();
                $likedByUser = $post->likes->contains('user_id', $user->id);

                $allposts[] = [
                    'post' => $postData,
                    'Likes' => $num_likes,
                    'Comments' => $num_comments,
                    'Favourites' => $num_fusers,
                    'Liked' => $likedByUser,
                ];
            }

            return response()->json(['Posts' => $allposts]);
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
          'image_path' => 'image|mimes:jpeg,png,jpg',
      ]);

      if ($validator->fails()) {
          return response()->json($validator->errors()->toJson(), 400);
      }

      $user = User::find(Auth::id());
      $user->name = request()->name;
      $user->email = request()->email;
      $user->password = bcrypt(request()->password);

      if (request()->hasFile('image')){
             $destenation='public/imgs/'.$user->image_path;
             if (file_exists($destenation)){
            File::delete($destenation);
            }
            $user=User::find(Auth::id());
            $path=$this->uploadImage(request(),'users',$user->user_name);
            $user->image_path = $path;
          }



      $user->save();


       return response()->json(['success' => 'User updated successfully']);

  }


    public function deleteAccount(){
        $user = User::find(Auth::id());
        if(file_exists('public/imgs/users/'.$user->user_name)){
            File::delete('public/imgs/users/'.$user->user_name);}
        if(file_exists('public/imgs/posts/'.$user->user_name)){
        File::delete('public/imgs/posts/'.$user->user_name);}
        User::destroy(Auth::user()->id);
    }
    public function suggestedUsers(){
        $follwers[]=User::find(Auth::user()->id)->followers;

        $users[]=User::find(Auth::user()->id)->followers->pluck('followers');
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
