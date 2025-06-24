<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        // Log::info('Image upload request received', [
        //     'ip' => $request->ip(),
        //     'user_agent' => $request->header('User-Agent'),
        // ]);

        $validated = $request->validate([
            'image' => 'required|image',
        ]);

        $image = $validated['image'];

        if ($image instanceof UploadedFile && $image->isValid()) {
            try {
                $relativePath = $this->saveImage($image);
                $imageUrl = URL::to(Storage::url($relativePath));

                return response()->json([
                    'image_url' => $imageUrl,
                    'image_mime' => $image->getClientMimeType(),
                    'image_size' => $image->getSize(),
                    'message' => 'Image uploaded successfully.'
                ]);
            } catch (Exception $e) {
                Log::error('Failed to save image after validation: ' . $e->getMessage(), [
                    'file' => $image->getClientOriginalName(),
                    'exception' => $e
                ]);
                return response()->json(['message' => 'Could not save the uploaded image.', 'error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Invalid image file provided.'], 422);
        }
    }

    private function saveImage(UploadedFile $image)
    {
        $path = 'images/' . Str::random(10);

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path, 0755, true, true);
        }

        $extension = $image->getClientOriginalExtension();
        $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(8) . '.' . $extension;

        $storedPath = Storage::disk('public')->putFileAs($path, $image, $filename);

        if (! $storedPath) {
            Log::error("Unable to save file to disk.", ['path' => $path . '/' . $filename]);
            throw new Exception("Unable to save file \"{$filename}\" to path \"{$path}\".");
        }

        return $storedPath;
    }
}
