<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserStatus;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});
Route::post('/createpost',[PostController::class,'create_post']);
Route::put('updatepost/{id}',[PostController::class,'update_post']);
Route::delete('deletepost/{id}',[PostController::class,'delete_post']);
Route::post('/showfollowersposts',[PostController::class,'show_followers_posts']);
Route::post('/addcomment/{id}',[PostController::class,'addComment']);
Route::post('/addlike/{id}',[PostController::class,'addLike']);
Route::delete('/dislikepost/{id}',[PostController::class,'dislikePost']);
Route::post('/usersfavouritepost/{id}',[PostController::class,'usersFavotitePost']);
Route::post('/showcomments/{id}',[PostController::class,'showComments']);
Route::post('/showlikes/{id}',[PostController::class,'showLikes']);
Route::get('/AddFavourite/{id}',[PostController::class,'addFavourite']);
Route::delete('/deletefavouritepost/{id}',[PostController::class,'deleteFavouritePost']);
Route::post('/showfavourites',[PostController::class,'ShowFavouritePosts']);
Route::get('/edit/{id}',[PostController::class,'edit']);
//////////////////////////////////////////////////////////////////////////////////////
Route::post('replay/{id}',[CommentController::class,'replay']);
Route::post('likecomment/{id}',[CommentController::class,'like']);
Route::post('dislikecomment/{id}',[CommentController::class,'dislike']);
Route::put('updatecomment/{id}',[CommentController::class,'update']);
Route::delete('deletecomment/{id}',[CommentController::class,'delete']);
Route::post('likes/{id}',[CommentController::class,'showLikes']);
Route::post('/replays/{id}',[CommentController::class,'showReplays']);
//////////////////////////////////////////////////////////////////////////////////////////
Route::get('/follow/{id}',[UserController::class,'follow']);
Route::get('/unfollow/{id}',[UserController::class,'unfollow']);
Route::post('/showfollowers',[UserController::class,'showFollowers']);
Route::post('/showfollowings',[UserController::class,'showFollowings']);
Route::post('myposts',[UserController::class,'myPosts']);
Route::get('/edituser/{id}',[UserController::class,'edit']);
Route::delete('deleteaccount',[UserController::class,'deleteAccount']);
Route::post('/suggestedUsers',[UserController::class,'suggestedUsers']);
////////////////////////////////////////////////////////////////////////////////////////
Route::middleware([UserStatus::class])->group(function (){
Route::post('allposts',[UserController::class,'allPosts']);
Route::post('allusers',[UserController::class,'allUsers']);
Route::delete('deleteuser/{id}',[UserController::class,'deleteUser']);
Route::delete('deletePost/{id}',[UserController::class,'deletePost']);
Route::post('makeadmin/{id}',[UserController::class,'makeAdmin']);
});
