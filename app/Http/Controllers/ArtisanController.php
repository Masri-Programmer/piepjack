<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class ArtisanController extends Controller
{
    public function call(Request $request)
    {
        $correctPassword = env('ARTISAN_PASSWORD', 'secretkey123'); // Set password in .env

        if ($request->isMethod('post')) {
            if ($request->has('password')) {
                if ($request->password === $correctPassword) {
                    Session::put('artisan_authenticated', true);
                } else {
                    return "Incorrect password! <a href='api/V1/shop/m-artisan'>Try again</a>";
                }
            }

            if ($request->has('command') && Session::get('artisan_authenticated')) {
                try {
                    Artisan::call($request->command);
                    return nl2br(Artisan::output()) . "<br><br><a href='api/V1/shop/m-artisan'>Run another command</a>";
                } catch (\Throwable $th) {
                    return "Error: " . $th->getMessage();
                }
            }
        }

        if (!Session::get('artisan_authenticated')) {
            return "
            <form method='post'>
                <input type='hidden' name='_token' value='" . csrf_token() . "' />
                <input type='password' name='password' placeholder='Enter password' required />
                <button>Submit</button>
            </form>
        ";
        }

        return "
        <form method='post'>
            <input type='hidden' name='_token' value='" . csrf_token() . "' />
            <input type='text' name='command' placeholder='Write your Artisan command' required />
            <button>Execute</button>
        </form>
        <br>
        <a href='/m-artisan-logout'>Logout</a>
    ";
    }

    public function logout()
    {
        Session::forget('artisan_authenticated');
        return "Logged out. <a href='api/V1/shop/m-artisan'>Login again</a>";
    }
}
