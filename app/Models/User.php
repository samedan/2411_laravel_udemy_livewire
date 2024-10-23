<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function posts() {
        return $this->hasMany(Post::class,'user_id' );
    }

    public function followersOfMe() {
        // how many users a User is followed by
        return $this->hasMany(Follow::class, 'followeduser', 'id'); 
    }
    public function followingTheseUsers() {
        // how many users a User is followed by
        return $this->hasMany(Follow::class, 'user_id', 'id'); 
    }

    // Blog Posts of Users that I follow
    public function feedPostsFollowedByMeUsers() {
        return $this->hasManyThrough(
            Post::class, // get blog posts, the stuff we want
            Follow::class, // the table that has my interested data,
            'user_id', // column on the intermediate table (Follow)
            'user_id',  // column on the final model we want (Post)
            'id', // local key on User.php(this file) 
            'followeduser'// local key on teh intermediate table (Follow)
        );
    }

    public function admin() {
        return auth()->user()->isAdmin;
    }

    // Return default Avatar image
    protected function avatar():Attribute {
        return Attribute::make(get: function($value) {
            return $value ? '/storage/avatars/'.$value  : '/fallback-avatar.jpg';
        });
    }
}
