<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CustomerController extends Controller
{
    public function sendLink(Request $request): Response
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = Customer::where('email', $request->email)->first();

        if ($user != null && ! $user->active) {
            response(['message' => 'You are banned from using this website.']);
        }

        if ($user == null) {
            $user = Customer::create(['email' => $request->email]);
        }

        $url = URL::temporarySignedRoute(
            'authenticate',
            now()->addMinutes(30),
            ['user' => $user->id]
        );

        Mail::to($request->email)->send(new SendMail($url));

        return response(['message' => 'Check your inbox.']);
    }

    public function authenticateUser(Request $request): RedirectResponse
    {
        $user = Customer::findOrFail($request->user);

        $frontendUrl = env('FRONTEND_URL');

        if (! URL::hasValidSignature($request)) {
            return redirect()->away($frontendUrl . '/invalid-link');
        }

        Auth::login($user);

        $user->tokens()->delete();
        $token = $user->createToken('main')->plainTextToken;

        return redirect()->away($frontendUrl . '/verify/?token=' . $token);
    }

    public function destroy(Customer $Customer)
    {
        $Customer->delete();

        return response('');
    }
}
