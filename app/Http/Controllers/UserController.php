<?php

namespace App\Http\Controllers;

use App\Events\OurExampleEvent;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserController extends Controller
{

    // GET view
    public function showCorrectHomepage() {
        if(auth()->check()) {
            
            return view('homepage-feed', [
                // 'posts' => auth()->user()->feedPostsFollowedByMeUsers()->latest()->get()
                'posts' => auth()->user()->feedPostsFollowedByMeUsers()->latest()->paginate(4)
            ]);
        }else {
            return view('homepage');
        };
    }

    // POST Register
    public function register(Request $request) {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $user = User::create($incomingFields);
        auth()->login($user); // sends the cokkie session value
        return redirect('/')->with('success', 'Thank you for registering.');
    }

    // POST Login
    public function login(Request $request) {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required',
        ]);

        if(auth()->attempt([
                'username' => $incomingFields['loginusername'],
                'password' => $incomingFields['loginpassword'],
            ])) 
        {
            $request->session()->regenerate();
            event(new OurExampleEvent([
                'username'=> auth()->user()->username,
                'action' => 'LogIn'
            ]));
            return redirect('/')->with('success', 'You have successfully logged in');
        } else {
            return redirect('/')->with('failure', 'Invalid login.');

        }
    }


    // POST Logout
    public function logout() {
        event(new OurExampleEvent([
            'username'=> auth()->user()->username,
            'action' => 'LogOut'
        ]));
        auth()->logout();
        
        return redirect('/')->with('success', 'You are logged out');;
    }


    // PROFILE//
    public function profile(User $user) {   
        $this->getShareProfileData($user);     
        return view('profile-posts', [
            'posts' => $user->posts()->latest()->get(),
        ]);
    }
    // PROFILE JSON Raw//
    public function profileRaw(User $user) {           
        return response()->json([
            'theHTML' => view(
                        'profile-posts-only', 
                        ['posts' => $user->posts()->latest()->get()])
                         ->render(),
            'docTitle' => $user->username . "'s Profile",
        ]);
    }
    // PROFILE Followers
    public function profileFollowers(User $user) {
        $this->getShareProfileData($user);  
        // return $user->followersOfMe()->latest()->get();
        return view('profile-followers', [
            'followers' => $user->followersOfMe()->latest()->get(),
        ]);
    }
    // PROFILE Followers JSON Raw
    public function profileFollowersRaw(User $user) {
        return response()->json([
            'theHTML' => view(
                        'profile-followers-only', 
                        ['followers' => $user->followersOfMe()->latest()->get()])
                         ->render(),
            'docTitle' => $user->username . "'s Followers",
        ]);
    }
    // PROFILE FollowING
    public function profileFollowing(User $user) {  
        $this->getShareProfileData($user);           
        return view('profile-following', [
            'following' => $user->followingTheseUsers()->latest()->get(),
        ]);
    }
    // PROFILE FollowING JSON Raw
    public function profileFollowingRaw(User $user) {  
        return response()->json([
            'theHTML' => view(
                        'profile-following-only', 
                        ['following' => $user->followingTheseUsers()->latest()->get()])
                         ->render(),
            'docTitle' => 'Who ' .$user->username .' follows',
        ]);
    }

    // Shared Profile function
    private function getShareProfileData($user) {
        $currentlyFollowing = 0; 
        if(auth()->check()) {
            $currentlyFollowing = Follow::where([
                ['user_id', '=', auth()->user()->id],
                ['followeduser', '=', $user->id]
            ])->count(); // BOOLEAN
        }
        View::share('sharedProfileData', [
            'username' => $user->username,            
            'postCount' => $user->posts()->count(),
            'followersCount' => $user->followersOfMe()->count(),
            'followingCount' => $user->followingTheseUsers()->count(),
            'avatar' => $user->avatar,
            'currentlyFollowing' => $currentlyFollowing
        ]);
    }

    // SHOW AVATAR Form
    public function showAvatarForm() {
        return view('avatar-form');
    }

    // STORE Avatar
    public function storeAvatar(Request $request) {
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);
        
        $user = auth()->user();
        $filename = $user->id . "-" . uniqid() . ".jpg";

        // RESIZE IMAGE
        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('avatar'));
        // crop the image
        $imgData = $image->cover(120,120)->toJpeg();
        // folder + filename
        Storage::put("public/avatars/". $filename , $imgData);

        // OLD avatar delete
        $oldAvatar = $user->avatar;
        // Update Database
        $user->avatar = $filename;
        $user->save();
        // delete old avatar if New Avatar
        if($oldAvatar != "/fallback-avatar.jpg") {
            // Transform 
            // /storage/avatars/12345.jpg
            // public/avatars/12345.jpg
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success', 'Avatar updated succesfully.');
    }

    //////////////////////////////////////////////////////////////////
    ///// API ////////////////////////////////////////////////////////

    // LOGIN /api/login
    public function loginApi(Request $request) {
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        if(auth()->attempt($incomingFields)){
            $user = User::where('username', $incomingFields['username'])->first();
            $token = $user->createToken('ourapptoken')->plainTextToken;
            return $token;
        }
        return 'Invalid credentials';
    }
}
