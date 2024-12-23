<?php

use App\Events\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [UserController::class, "showCorrectHomepage"])->name('login');

// ADMIn-Only
Route::get('/admins-only', function() {
   return 'Admin Only Access';
   // if(Gate::allows(('visitAdminPages'))) {
   //  return 'Admin Only Access';
   // }
   // return 'You cannot view this page';
})->middleware('can:visitAdminPages');


// AUTH
Route::post('/register', [UserController::class, "register"])
    ->middleware('guest');
Route::post('/login', [UserController::class, "login"])
    ->middleware('guest');
Route::post('/logout', [UserController::class, "logout"])
    ->middleware('mustBeLoggedIn');

// BLOG Posts
Route::get('/create-post', [PostController::class, 'showCreateForm'])
  ->middleware('mustBeLoggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])
  ->middleware('mustBeLoggedIn');

Route::get('/post/{post}', [PostController::class, 'viewSinglePost'])->middleware('mustBeLoggedIn');
Route::delete('/post/{post}', [PostController::class, 'delete'])->middleware('can:delete,post');

Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'actuallyUpdate'])->middleware('can:update,post');

// Search
Route::get('/search/{term}', [PostController::class, 'search']);

// PROFILE 
// search 'user' based on 'username' {user:username}
Route::get('/profile/{user:username}', [UserController::class, 'profile']);
Route::get('/profile/{user:username}/followers', [UserController::class, 'profileFollowers']);
Route::get('/profile/{user:username}/following', [UserController::class, 'profileFollowing']);

// PROFILE JSON Raw
// search 'user' based on 'username' {user:username}
Route::middleware('cache.headers:public;max_age=20;etag')->group(
  function () {
    Route::get('/profile/{user:username}/raw', [UserController::class, 'profileRaw']);
    Route::get('/profile/{user:username}/followers/raw', [UserController::class, 'profileFollowersRaw']);
    Route::get('/profile/{user:username}/following/raw', [UserController::class, 'profileFollowingRaw']);
    
  }
);

// AVATAR
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar', [UserController::class, 'storeAvatar'])->middleware('mustBeLoggedIn');


// FOLLOW
Route::post('/create-follow/{user:username}', [FollowController::class, 'createFollow'])->middleware('mustBeLoggedIn');
Route::post('/remove-follow/{user:username}', [FollowController::class, 'removeFollow'])->middleware('mustBeLoggedIn');


//  CHAT
Route::post('/send-chat-message', function(Request $request) {
  $formFields = $request->validate([
    'textvalue' => 'required'
  ]);
  // filter empty spaces & tags
  if(!trim(strip_tags($formFields['textvalue']))){
    return response()->noContent();
  }
  broadcast(new ChatMessage([
    'username' => auth()->user()->username,
    'textvalue' => strip_tags($request->textvalue),
    'avatar' => auth()->user()->avatar,
  ]))->toOthers();
  return response()->noContent();

})->middleware("mustBeLoggedIn");
