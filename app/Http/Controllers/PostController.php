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

class PostController extends Controller
{
    use UploadImageTrait;
    public function create_post(Request $request){
      $request->validate([
        'body'=>'required',
      ]);
      $path=null;
      if($request->hasFile('image')){
        $user=User::find(auth()->user()->id);
       $path=$this->uploadImage($request,'posts',$user->user_name);
      }
        Post::create(['body'=>$request->body,
        'user_id'=>auth()->user()->id,]);

        return response()->json('Post Created ');
    }
    public function update_post(Request $request,$id){
        $request->validate(['body'=>'required',]);
        $post=Post::find($id);
        $post->body=$request->body;
       if($request->hasFile('image')){
        $destenation='public/imgs/'.$post->path;
        if (file_exists($destenation)){
       File::delete($destenation);
       }
        $user=User::find(auth()->user()->id);
       $path=$this->uploadImage($request,'posts',$user->user_name);
       $post->path= $path;
       }
       $post->save();
        return response()->json('Updated Done');
    }
    public function delete_post($id){
        $user=User::find(auth()->user()->id);
    if(file_exists('public/imgs/posts/'.$user->user_name)){
        File::delete('public/imgs/posts/'.$user->user_name);
    }
    Post::destroy($id);
    return response()->json('Deleted Done');
    }
    public function show_followers_posts(){

    $follwers=User::find(auth()->user()->id)->followers;
$posts=$follwers->pluck('posts');

    return response()->json(['follwers'=>$follwers]);
}
public function addComment( Request $request,$id){
    Comment::create(['post_id'=>$id,'user_id'=>auth()->user()->id,'body'=>$request->body,'created_at'=>now(),'updated_at'=>now()]);
}
public function addLike( Request $request,$id){
    Like::create(['post_id'=>$id,'user_id'=>auth()->user()->id,'active'=>1]);
    return response()->json('Liked Added');
}
public function edit($id){
    $post=Post::findOrFail($id);
    return response()->json($post);
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
    $flag=DB::table('user_favourite')->where('user_id',auth()->user()->id)->where('post_id',$id)->get();
    if($flag->count()== 0){
DB::table('user_favourite')->insert([
    'user_id'=>auth()->user()->id,
    'post_id'=>$id
]);
return response()->json('Add To Favourite');
}
return response()->json("You Can't Add To favourite again");
}
public function ShowFavouritePosts(){
$posts=auth()->user()->fposts;
$users=$posts->pluck('user');
return response()->json(['favpost'=>$posts]);

}
public function deleteFavouritePost($id){
DB::table('user_favourite')->where('user_id',auth()->user()->id)->where('post_id',$id)->delete();
return response()->json('Deleted Favourite');
}
public function dislikePost($id){
    Like::where('user_id',auth()->user()->id)->where('post_id',$id)->delete();
    return response()->json('Dislike Done');
}
public function usersFavotitePost($id){
$users=Post::find($id)->fusers;
return response()->json(['Number Of Users Favourite This Post'=>count($users)]);
}
}


