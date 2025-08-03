<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShutdownController extends Controller
{
    public function shutdown(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $storedPasswordHash = env('PASSWORD_HASH');
        $storedShutdownCodeHash = DB::table('settings')->where('key', 'SHUTDOWN_CODE_HASH')->value('value');

        if (! Hash::check($request->password, $storedPasswordHash)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        DB::table('settings')->where('key', 'SHUTDOWN_CODE_HASH')->delete();

        return response()->json(['message' => 'SHUTDOWN_CODE deleted successfully']);
    }

    public function getShutdownCode()
    {
        $shutdownCode = DB::table('settings')->where('key', 'SHUTDOWN_CODE_HASH')->value('value');

        return response()->json(['shutdown_code' => $shutdownCode]);
    }

    public function generateShutdownCode()
    {
        $shutdownCode = Str::random(32);
        $shutdownCodeHash = Hash::make($shutdownCode);

        DB::table('settings')->updateOrInsert(
            ['key' => 'SHUTDOWN_CODE_HASH'],
            ['value' => $shutdownCodeHash]
        );

        return response()->json(['shutdown_code' => $shutdownCode]);
    }
}
