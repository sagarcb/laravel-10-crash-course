<?php

use App\Http\Controllers\Profile\AvatarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use OpenAI\Laravel\Facades\OpenAI;

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

Route::get('/', function () {
    //Fetch all users
//    $users = DB::select("select * from users where email = ?", ['sagar.cb.009@gmail.com']); //using raw query
//    $users = DB::table('users')->get();  //using Query Builder
//    $users = User::where('id', 1)->first();
//    $user = User::find(9);

    //create new user
//    $user = DB::insert("insert into users(name, email, password) values (?, ?, ?)", [   //using raw query
//        'Sagar Chakraborty',
//        'sagar.cb.009@gmail.com',
//        '12345678'
//    ]);
//    $user = DB::table('users')->insert([      //using query builder
//        'name' => 'Sagar Chakraborty',
//        'email' => 'sagar.cb.009@gmail.com',
//        'password' => '12345678'
//    ]);
//    $user = User::create([
//        'name' => 'Swadhin',
//        'email' => 'swadhin@gmail.com',
//        'password' => '12345678'
//    ]);

    //update user
//    $user = DB::update("update users set name = ? where email = ?", [     //using raw query
//        'Swadhin Chakraborty',
//        'swadhin@gmail.com'
//    ]);
//    $user = DB::table('users')->where('id', 3)->update(['name' => 'Swadhin Chakraborty']);    //using Query Builder

//    $user = User::find(4);
//    $user->update([
//        'name' => 'Swadhin Chakraborty'
//    ]);

    //delete user
//    $user = DB::delete("delete from users where id = 2");  //using raw query
//    $user = DB::table('users')->where('id', 3)->delete();     //using Query Builder

    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar');
    Route::patch('/profile/avatar/ai', [AvatarController::class, 'generateAvatar'])->name('profile.avatar.ai');
});

require __DIR__.'/auth.php';

Route::post('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
})->name('login.github');

Route::get('/auth/callback', function () {
    $user = Socialite::driver('github')->user();
    $user = User::firstOrCreate(['email' => $user->email],[
        'name' => $user->name,
        'password' => bcrypt('password')
    ]);

    Auth::login($user);
    return redirect('/dashboard');
});

Route::middleware('auth')->group(function (){
    Route::resource('/ticket', TicketController::class);
    Route::patch('/ticket/update/status/{ticket}', [TicketController::class, 'updateStatus'])->name('ticket.update.status');
    Route::post('/ticket/reply/{ticket}', [TicketController::class, 'replyTicket'])->name('ticket.reply');
});
