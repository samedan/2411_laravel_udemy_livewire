<?php

namespace App\Http\Controllers;

use App\Jobs\SendNewPostEmail;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class PostController extends Controller
{

    // GET View New Post Form
    public function showCreateForm() {
        return view('create-post');
    }


    // POST New Post
    public function storeNewPost(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $incomingFields['user_id'] = auth()->id();
        $newPost = Post::create($incomingFields);
        // Emailing JOB
        dispatch(new SendNewPostEmail([
            'sendTo' => auth()->user()->email,
            'name' => auth()->user()->username,
            'title' => $newPost->title,
        ]));      
        return redirect("/post/{$newPost->id}")->with('success', 'New post added');
    }

    // GET Single Post
    public function viewSinglePost(Post $post) {
        $ourHtml = strip_tags(Str::markdown($post->body),
            '<p><ul><li><strong><bold><em><h3><h2><h1>' 
        );
        $post['body'] = $ourHtml;
        return view('single-post', [
            'post' => $post
        ]);
    }

    // DELETE Post
    public function delete(Post $post) {
        // if( auth()->user()->cannot('delete', $post) ) {
        //     return 'You cannot delete the post';
        // }
        $post->delete();
        return redirect('/profile/'.auth()->user()->username)
            ->with('success', 'Post deleted');
        
    }
    
    // SHOW Edit form
    public function showEditForm(Post $post) {
        return view('edit-post', [
            'post' => $post
        ]);
    }

    // PUT edit form
    public function actuallyUpdate(Post $post, Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $post->update($incomingFields);
        return back()->with('success', 'Post scuccessfully updated');
    }

    // SEARCH
    public function search($term) {
        $posts = Post::search($term)->get();
        // load the user_id (id) data: username, avatar
        $posts->load('user:id,username,avatar');
        return $posts;
    }


    ///////////////////////////////////////////////
    //// API //////////////////////////////////////
    // POST New Post /api/create-post
    public function storeNewPostApi(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $incomingFields['user_id'] = auth()->id();
        $newPost = Post::create($incomingFields);
        // Emailing JOB
        dispatch(new SendNewPostEmail([
            'sendTo' => auth()->user()->email,
            'name' => auth()->user()->username,
            'title' => $newPost->title,
        ]));      
        return $newPost->id;
    }
    // DELETE post /api/deleteApi
    public function deleteApi(Post $post) {
        // if( auth()->user()->cannot('delete', $post) ) {
        //     return 'You cannot delete the post';
        // }
        $post->delete();
        return 'post deleted';
    }
    
}
