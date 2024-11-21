> composer install
> npm install

### Add column to table in Database

> New Migration: Schema::table('users', function());

### Form errors

> save typed data: <input value="{{old('username')}}" />

### Markdown

> strip_tags(<>), {!! accepted html !!}

### Middleware

> ->middleware('auth')

> Default redirect route: /middleware/RedirectIfAuthenticated

> Create app/Http/Middleware/MustBeLoggedIn
> Add to Kernel.php -> protected $middlewareAliases = []

### Policy - condition for user wrote a post

> php artisan make:policy PostPolicy --model=Post
> AuthServiceProviders -> protected $policies = [ Post::class => PostPolicy::class ];
> Policy on Blade: single-post.blade.php -> @can('update', $post)
> Policy on Controller: PostController -> auth()->user()->cannot('delete', $post)

### /////////////////////////////////

### ADMIN

### Change existing table in DBB with migration

> php artisan make:migration add_isadmin_to_users_table --table=users
> php artisan migrate

### Admin Gate

> AuthServiceProvider.php -> Gate::define('visitAdminPages')

# Routes way (Gate)

> if(Gate::allows(('visitAdminPages')))

# Controller way (Gate)

> Route('/admins-only') ... -> ->middleware('can:visitAdminPages')

### Avatars

> Folder avatars -> UserController -> storeAvatar()
> /storage/app/public
> Access folder shortcut: php artisan storage:link

## Resize images

> composer require intervention/image

### Default avatar

> User model -> protected function avatar():Attribute{}

### //////////////////////

### Follows

> php artisan make:migration create_follows_table
> php artisan make:controller FollowController
> php artisan make:model Follow

## Check if Following exists already, combination of 2 columns

> FollowController createFollow AND deleteFollow -> $existCheck = Follow::where()

## Check the 'active' link /one/two/three

> profile.blade.php -> {{Request::segment(3)}}

### Followers & Following

> on Follow model -> public function userDoingTheFollowing() & userBeingFollowed()
> on User model -> public function followersOfMe() & followingTheseUsers()
> User -> hasManyThrough()
> ![hasManyThrough](https://github.com/samedan/2410_udemy_laravel_revisited/blob/main/public/printscreen1.jpg)

### Pagination

> AppServiceProvider -> Paginator::useBootstrapFive();

### Search with Scout on BACKend

> composer require laravel/scout
> php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
> Post model -> use Searchable;
> Post -> public function toSearchableArray()
> .env -> SCOUT_DRIVER=database
> PostController -> public function search($term)

### ### Search on FRONTend

> npm i dompurify
> app.js -> import Search from "./live-search";

### Events & Logs

> php artisan event:generate
> OurExampleEvent & OurExampleListener
> EventServiceProvider -> protected $listen = []
> UserController -> event(new OurExampleEvent())
> storage/logs/laravel.logs

### Broadcast messages with Pusher

> composer require pusher/pusher-php-server
> pusher.com ->account -> keys -> .env
> .env -> BROADCAST_DRIVER=pusher

## Laravel pusher & chat on Frontend

> npm i laravel-echo pusher-js

### Broadcasting _ TO DO _

> web.php -> Route::post('/send-chat-message')

# ChatMessage

> php artisan make:event ChatMessage
> app/events/ChatMessage -> implements ShouldBroadcastNow
> CharMessage -> \_\_construct() ->broadcastOn() -> new PrivateChannel('chatchannel')
> /routes/channels.php -> Broadcast::channel('chatchannel')
> chat.js in app.js
> resources.js/bootstrap.js -> import Echo...
> layout.blade.php -> add div for chat -> id="chat-wrapper"
> /config/app.php -> App\Providers\BroadcastServiceProvider::class,

### Single Page Application SPA

> /resources/js/profile.js -> load on app.js
> UserController -> profileFollowingRaw -> return response()->json(HTML)
> cache page web.php -> Route::middleware('cache.headers:public;max_age=20;etag')

### EMAILing

> php artisan make:mail NewPostEmail
> Blade -> resources/views/new-post-email.blade.php
> /app/Mail/NewPostEmail.php -> public function content()
> PostController -> storeNewPost() -> Mail::to('test@google.com')->send(new NewPostEmail());
> Pass data to email: NewPostEmail -> public function \_\_construct(public $data), public function content()

### JOBS

> php artisan make:job SendNewPostEmail
> dotenv -> QUEUE_CONNECTION=sync -> QUEUE_CONNECTION=database
> php artisan queue:table -> add migration for Jobs table
> php artisan migrate
> Run jobs -> php artisan queue:work

### Schedule Task

> Email blade: recapemail.blade.php
> php artisan make:mail RecapEmail

## Schedule

> app/console/Kernel.php
> Kernel.php -> protected function schedule(Schedule $schedule)()->everyMinute();
> Needs to run to work: php artisan schedule:work

### ///////////////////////////////////////////////////////////////

> ![Docker](https://github.com/samedan/2410_udemy_laravel_revisited/blob/main/public/printscreen2.jpg)

### Docker Install

> docker run -dit -p 80:80 ubuntu:22.04

# Update packages

> apt update

# Install packages

> apt install nginx

# Start nginx

> /etc/init.d/nginx start
> TEST -> localhost

# Install stuff

> apt install curl nano php-cli unzip php8.1-fpm php-mysql php-mbstring php-xml php-bcmath php-curl php8.1-gd

# Go to 'root folder'

> pwd or 'cd ~'

# Install/Copy composer

> curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php

# Install composer in '/usr/local/bin/composer' folder

> php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# test composer isntallement globally

> composer --version

## Install MySQL

> apt install mysql-server

# start Mysql server

> /etc/init.d/mysql start

# Launch 'mysql' command

> mysql -> 'mysql>'

# Change root password

> ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password by '12345678';
> exit

# MYSQL Installation secure

> mysql_secure_installation

# Login into mysql with the new password

> mysql -u root -p

# Create DBB for laravel

> mysql> CREATE DATABASE ourlaravelapp;

# Create user for DBB

> mysql> CREATE USER 'ourappuser'@'%' IDENTIFIED WITH mysql_native_password BY '12345678';

# Grant Powers to user over this dbb

> mysql> GRANT ALL ON ourlaravelapp.\* TO 'ourappuser'@'%';
> mysql> exit

### Command Line on HOST Computer

> Find Folder in VSC: pwd
> D:\_apps_2024_Laravel_Juillet_Plus\2410_Laravel_Udemy_RealBeginners\beginnerapp

### Docker command

> cd /var/www
> ls
> html -> default files for nginx

### Create folder for app files

# VSC command fro the 'naughty_carson' docker container

> docker cp D:\_apps_2024_Laravel_Juillet_Plus\2410_Laravel_Udemy_RealBeginners\beginnerapp naughty_carson:/var/www/ourapp

# Confirm files copied

# Docker command

> ls
> cd ourapp
> ls

### NGINX serve our site

# docker command

> cd /etc/nginx
> cd sites-available

# default

> nano default

# Remode default file

> rm default

# Create default file

> touch default
> nano default

### Copy content from docker-nginx.txt into default

## Restart nginx

> /etc/init.d/nginx restart

## Restart php

> /etc/init.d/php8.1-fpm start

## See website files

> localhost -> website gives errors about access rights
> cd /var/www/ourapp
> ls

# Give acces to 'storage' folder

> chown -R www-data:www-data storage

# Create link from 'storage' to 'public'

> cd /var/www/ourapp
> php artisan storage:link
> localhost (dbb error)

# Connect DBB

> nano .env

# Migrate DBB

> php artisan migrate

## run website ->new user

# Avatar size

> nano /etc/php/8.1/fpm/php.ini
> search 'upload_max_filesize' -> 10M

# Restart nginx

> /etc/init.d/nginx restart

## Restart php

> /etc/init.d/php8.1-fpm start

# production

> nano .env
> APP_ENV=production
> APP_DEBUG=false

### Docker Stop/Start

> /etc/init.d/mysql start
> /etc/init.d/php8.1-fpm start
> /etc/init.d/nginx start

## Startup script

> touch /ourstartup
> nano ourstartup

# make file executable

> chmod u+x /ourstartupfile

### Docker Stop/Start with file

> /ourstartupfile

# ////////////////////////////////

### CRON jobs

> ![CROn Jobs](https://github.com/samedan/2410_udemy_laravel_revisited/blob/main/public/printscreen3.jpg)

# Install Cron

> apt install cron

# Add/edit a jCron ob

> crontab -e

# \* \* \* \* \* /usr/bin/php /var/www/ourapp/artisan queue:work --max-time=60

# \* \* \* \* \* /usr/bin/php /var/www/ourapp/artisan schedule:run

# Run in the background

> /etc/init.d/cron start

### CRON in Docker without re-init

> nano /ourstartupfile
> /etc/init.d/cron start

### REDIS

> apt install redis-server

# Install

> cd /var/www/ourapp
> ls
> composer require predis/predis
> nano .env
> CACHE_DRIVER=redis
> QUEUE_CONNECTION=redis
> \+ REDIS_CLIENT=predis

# Restart redis server

> /etc/init.d/redis-server start
> nano/ourstartupfile
> \+ /etc/init.d/redis-server start

### test docker

> Stop -> start
> /ourstartupfile

###### VPS

> ssh root@ip.ip
> sudo apt update
> sudo apt install nginx
> visit browser -> ip (nginx)

### Sites Available

> cd /etc/nginx
> ls -> 'default' file

## Multiple domains

> touch mysite
> nano mysite

### SSH Keys

## PC

> cd c:/users/USERName/.ssh folder
> .ssh ssh-keygen -t ed25519 -C "email@gmail.com"
> Windows -> ssh-keygen -t ed25519 -C "email@gmail.com"

## VPN

> Login -> ssh root@ip.ip
> pwd
> cd ~
> cd .ssh
> ls -> authorized_keys
> copy content from file.pub on PC
> exit

## Login with ssh key

> Login -> ssh root@ip.ip

## Install stuff

> apt install php-cli unzip php8.1-fpm php-mysql php-mbstring php-xml php-bcmath php-curl php8.1-gd
> Error: https://laracasts.com/discuss/channels/laravel/installing-php-81-on-ubuntu-1804

## Install cumposer

> curl -sS http://getcomposer.org/installer -o /tmp/composer-setup.php
> php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
> test composer -> composer --version

## Install mysql

> apt install mysql-server

## Add Psssword

> mysql
> mysql> ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password by 'XpasswordX';
> mysql> exit
> mysql_secure_installation

## Create Database & User

> mysql -u root -p
> mysql> CREATE DATABASE ourlaravelapp;
> mysql> CREATE USER 'ourappuser'@'%' IDENTIFIED WITH mysql_native_password BY '12345678';
> mysql> GRANT ALL ON ourlaravelapp.\* TO 'ourappuser'@'%';
> mysql> exit

### ////////////////////////////////////////////////////////////

### Git,

> Login -> ssh root@ip.ip
> cd /var/www
> ls -> html
> mkdir ourapp
> ls -> ourapp
> mkdir ourrepos
> ls ->ourrepos
> cd ourrepos
> pwd -> root@localhost:/var/www/ourrepos/ourapp -> folder for repoGitFiles
> Be in -> root@localhost:/var/www/ourrepos/ourapp#
> git config --global init.defaultBranch main

# Initialized empty Git repository in /var/www/ourrepos2/ourapp2/

> git init --bare

# Files commands for server -> root@localhost:/var/www/ourrepos2/ourapp2/hooks#

> root@localhost:/var/www/ourrepos/ourapp# -> cd hooks
> ls

# Edit file for post receveing files

> root@localhost:/var/www/ourrepos/ourapp/hooks# -> touch post-receive
> nano post-receive

# Change permissions

> chmod +x post-receive

## Push Git to VPS

> Declare a remote in Git
> git remote add production ssh://root@MyIP/var/www/ourrepos/ourapp
> git push production main

## Vendor folder

> composer install

## NGINX config file

> cd /etc/nginx
> cd sites-available

# Use file vps-nginx.txt

> sites-available -> nano mysite OR nano default

# reload NGINX

> sudo systemctl reload nginx

# DOT ENV -> .env

> cd /var/www/ourapp
> ls
> touch .env
> nano .env

### Create rights for 'storage' folder

> cd /var/www/ourapp
> chown -R www-data:www-data storage
> php artisan storage:link
#   2 4 1 1 _ l a r a v e l _ u d e m y _ l i v e w i r e  
 