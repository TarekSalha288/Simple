<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\File;
use App\Models\Like;
use App\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class PostController extends Controller
{
    use UploadImageTrait;
    public function create_post(Request $request){
      $request->validate([
        'body'=>'required',
      ]);
      $user=User::find(Auth::user()->id);
      $post=new Post;
      $post->body=$request->body;
      $post->user_id=$user->id;
      if($request->hasFile('image')){
       $path=$this->uploadImage($request,'posts',$user->user_name);
       $post->path=$path;
      }
      $post->save();


        return response()->json('Post Created ');
    }
    public function update_post(Request $request, $id) {
        $post = Post::find($id);
        if (Auth::user()->id==$post->user_id) {
            $request->validate([
                'body' => 'required',
                'image'=>'required|image|mimes:jpeg,png,jpg',
            ]);
            $post->body = $request->body;
            if ($request->hasFile('image')) {
                $destination = public_path('imgs/' . $post->path);
                if (File::exists($destination)) {
                    File::delete($destination);
                }
                $user = User::find(Auth::user()->id);
                $path = $this->uploadImage($request, 'posts', $user->user_name);
                $post->path = $path;
            }
            $post->save();
            return response()->json(
                'Updated Done'
            );
        }

        return response()->json('You Can\'t Update This Post');
    }


    public function delete_post($id){
        $user=User::find(Auth::user()->id);
        $post=Post::where('id',$id)->first();

    if(file_exists('public/imgs/posts/'.$user->user_name.'/'.$post->path)){
        File::delete('public/imgs/posts/'.$user->user_name.'/'.$post->path);
    }
    Post::destroy($id);
    return response()->json('Deleted Done');
    }
    public function show_followings_posts() {
        $user = Auth::user();
        $followings = $user->followings;
        $allPosts = [];
       foreach ($followings as $following) {
            foreach ($following->posts as $post) {
                $post->user;
                $postData=$post->toArray();
                unset($postData['likes']);
                $num_likes= $post->Likes()->count();
                $num_comments=$post->comments()->count();
                $num_fusers=$post->fusers()->count();
                $likedByUser = $post->Likes->contains('user_id', $user->id);
                $allPosts[] = [
                    'post' => $post,
                    'liked_by_user' => $likedByUser,
                    'Likes'=>$num_likes,
                    'Commments'=>$num_comments,
                    'Favourites'=>$num_fusers,
                ];
            }
    }
        return response()->json([ 'posts' => $allPosts]);
    }
public function post($id){
    $post=Post::where('id',$id)->first();
    $user=Post::find($id)->user;
    $likes=Post::find($id)->Likes;
    $flag=false;
    $num_likes=$post->Likes()->count();
    $num_comments=$post->comments()->count();
    $num_fusers=$post->fusers()->count();
    foreach($likes as $like){
        if($like->user_id==Auth::user()->id){
            $flag=true;
  }
}
  return response()->json(['post'=>$post,
  'user'=>$user,
  'Likes'=>$num_likes,
  'Comments'=>$num_comments,
  'Favourites'=>$num_fusers,
  'like'=>$flag]);
}
public function addComment( Request $request,$id){
    Comment::create(['post_id'=>$id,'user_id'=>Auth::user()->id,'body'=>$request->body,'created_at'=>now(),'updated_at'=>now()]);
}
public function addLike( $id){
    $like=Like::where('user_id',Auth::user()->id)->where('post_id',$id)->first();
    if($like){
        return response()->json('You Have Already Liked This Post');
    }
    Like::create(['post_id'=>$id,'user_id'=>Auth::user()->id,'active'=>1]);
    return response()->json('Liked Added');
}

public function showComments($id){
    $comments=Post::find($id)->comments;
    foreach($comments as $comment){
        $users[]=User::where('id',$comment->user_id)->get();
    }
    return response()->json(['comments'=>$comments,'users'=>$users]);
}
public function showLikes($id){
    $likes=Post::find($id)->likes;
    foreach($likes as $like){
        $users[]=User::where('id',$like->user_id)->get();
    }
    return response()->json(['likes'=>$likes,'users'=>$users]);
}
public function addFavourite($id){
    $flag=DB::table('user_favourite')->where('user_id',Auth::user()->id)->where('post_id',$id)->get();
    if($flag->count()== 0){
DB::table('user_favourite')->insert([
    'user_id'=>Auth::user()->id,
    'post_id'=>$id
]);
return response()->json('Add To Favourite');
}
return response()->json("You Can't Add To favourite again");
}
public function ShowFavouritePosts(){
$posts=Auth::user()->fposts;
$users=$posts->pluck('user');
return response()->json(['favpost'=>$posts]);

}
public function deleteFavouritePost($id){
DB::table('user_favourite')->where('user_id',Auth::user()->id)->where('post_id',$id)->delete();
return response()->json('Deleted Favourite');
}
public function dislikePost($id){
    $like=Like::where('user_id',Auth::user()->id)->where('post_id',$id)->first();
    if($like){
    Like::where('user_id',Auth::user()->id)->where('post_id',$id)->delete();
    return response()->json('Dislike Done');}
    return response()->json('You Don\'t Liked This Post');
}
}


