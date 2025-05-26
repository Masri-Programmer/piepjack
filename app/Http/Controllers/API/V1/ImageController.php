<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image',
        ]);

        $image = $validated['image'];
        if ($image) {
            $relativePath = $this->saveImage($image);
            $validated['image'] = URL::to(Storage::url($relativePath));
            $validated['image_mime'] = $image->getClientMimeType();
            $validated['image_size'] = $image->getSize();
        }

        return response()->json($validated);
    }

    private function saveImage(UploadedFile $image)
    {
        $path = 'images/' . Str::random();

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path, 0755, true);
        }

        // Generate a clean, unique file name
        $extension = $image->getClientOriginalExtension();
        $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(8) . '.' . $extension;

        if (! Storage::disk('public')->putFileAs($path, $image, $filename)) {
            throw new Exception("Unable to save file \"{$filename}\"");
        }

        return $path . '/' . $filename;
    }
}
