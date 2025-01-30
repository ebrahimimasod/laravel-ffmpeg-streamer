<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display the dashboard with the list of media files.
     */
    public function index()
    {
        $mediaFiles = Media::latest()->paginate(10); // Paginate for better UX
        return view('dashboard', compact('mediaFiles'));
    }

    /**
     * Handle the file upload.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // Max 100MB
            'visibility' => 'required|in:public,private',
        ]);

        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('uploads', 'public'); // Store in 'public/uploads'

        Media::create([
            'name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'visibility' => $request->visibility,
        ]);

        return redirect()->route('dashboard')->with('success', 'File uploaded successfully.');
    }

    /**
     * Download the specified file.
     */
    public function download($uuid)
    {
        $media = Media::where('uuid', $uuid)->firstOrFail();

        // Optionally, check for visibility or permissions here

        return Storage::disk('public')->download($media->path, $media->name);
    }

    /**
     * Delete the specified file.
     */
    public function destroy($uuid)
    {
        $media = Media::where('uuid', $uuid)->firstOrFail();

        // Delete the file from storage
        Storage::disk('public')->delete($media->path);

        // Delete the database record
        $media->delete();

        return redirect()->route('dashboard')->with('success', 'File deleted successfully.');
    }

    public function stream($uuid)
    {

        // Retrieve the media file
        $file = Media::where('uuid', $uuid)->firstOrFail();

        // Access Control
        if ($file->visibility === 'private') {
            // Ensure the authenticated user is the owner of the file
//            if ($file->user_id !== Auth::id()) {
//                abort(403, 'Unauthorized access to this file.');
//            }
        }

        // Check if the file exists in storage
        if (!Storage::disk('public')->exists($file->path)) {
            abort(404, 'File not found.');
        }

        // Open the file as a stream
        $stream = Storage::disk('public')->readStream($file->path);

        // Determine the MIME type
        $mime = Storage::disk('public')->mimeType($file->path);

        // Create the streamed response
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($file->name) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
