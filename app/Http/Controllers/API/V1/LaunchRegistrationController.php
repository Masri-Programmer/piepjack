<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\LaunchRegistration;
use App\Mail\LaunchRegistrationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class LaunchRegistrationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:launch_registrations,email',
        ], [
            'email.unique' => 'Diese E-Mail-Adresse ist bereits registriert.',
            'email.required' => 'Bitte geben Sie eine E-Mail-Adresse ein.',
            'name.required' => 'Bitte geben Sie Ihren Namen ein.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $registration = LaunchRegistration::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        try {
            Mail::to($registration->email)->send(new LaunchRegistrationMail($registration));
        } catch (\Exception $e) {
            // Log error but don't fail the registration
            report($e);
        }

        return response()->json([
            'message' => 'Erfolgreich registriert! Wir werden dich benachrichtigen.'
        ], 201);
    }

    /**
     * Trigger the "Shop is Online" notification to all registrants.
     */
    public function triggerOnlineNotification(Request $request)
    {
        $launchDate = config('app.launch_date');
        $now = Carbon::now();
        $target = Carbon::parse($launchDate);

        \Illuminate\Support\Facades\Log::info("triggerOnlineNotification called. Now: {$now}, Target: {$target}");
        
        if ($now->lt($target)) {
            \Illuminate\Support\Facades\Log::warning("Launch date not reached yet. Difference: " . $now->diffForHumans($target));
            return response()->json([
                'message' => 'Launch date has not been reached yet.',
                'now' => $now->toIso8601String(),
                'target' => $target->toIso8601String()
            ], 403);
        }

        // Check if there are any registrants who haven't been notified yet
        $pendingCount = LaunchRegistration::whereNull('sent_online_notification_at')->count();
        \Illuminate\Support\Facades\Log::info("Pending registrants: {$pendingCount}");

        if ($pendingCount === 0) {
            return response()->json([
                'message' => 'All registrants have already been notified.'
            ]);
        }

        // Run the artisan command to send emails
        \Illuminate\Support\Facades\Log::info("Calling shop:notify-online command...");
        Artisan::call('shop:notify-online');
        $output = Artisan::output();
        \Illuminate\Support\Facades\Log::info('Artisan Command Output: ' . $output);

        return response()->json([
            'message' => 'Notification process triggered successfully.',
            'count' => $pendingCount,
            'details' => $output
        ]);
    }
}
