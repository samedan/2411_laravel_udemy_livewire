<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user) {

        // dd($user);
        // you cannot follow yourself
        if($user->id == auth()->user()->id) {
            return back()->with('failure', 'You cannot follow yourself');
        }
        // you cannot already followed user
        $existCheck = Follow::where([
            ['user_id', '=', auth()->user()->id], // 'user_id' equals your user
            ['followeduser', '=', $user->id] // followeduser already exists
        ])->count(); // if condition is 1 is TRUE
        if($existCheck) {
            return back()->with('failure', 'You are already following that user');
        }
        
        
        // $newFollow = Follow::create([
        //     'user_id' => auth()->user()->id,
        //     'followeduser' => $user->id
        // ]);

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id; // Me, I create the follow
        $newFollow->followeduser = $user->id; // followed user

        
        $newFollow->save();

        return back()->with('success', "You are now following $user->username");

    }


    public function removeFollow(User $user) {
        // check to see the combination exists already
        Follow::where([
            ['user_id', '=', auth()->user()->id], // me, logged in user
            ['followeduser', '=', $user->id] // user I check if I follow already
        ])->delete();
        return back()
            ->with('success', 'User successfully unfollowed');
    }
}
