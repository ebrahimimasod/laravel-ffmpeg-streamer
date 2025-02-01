<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertAudioForStreaming;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function index()
    {
        $mediaFiles = Media::latest()->paginate(10); // Paginate for better UX
        return view('dashboard', compact('mediaFiles'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // Max 100MB
            'visibility' => 'required|in:public,private',
        ]);
        $uuid = (string)Str::uuid();
        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store("files/$uuid/map3", 'local'); // Store in 'public/uploads'

        $media = Media::create([
            'name' => $uploadedFile->getClientOriginalName(),
            'uuid' => $uuid,
            'path' => $path,
            'visibility' => $request->visibility,
        ]);

        dispatch(new ConvertAudioForStreaming($media->id));

        return redirect()->route('dashboard')->with('success', 'File uploaded successfully.');
    }

    public function download($uuid)
    {
        $media = Media::where('uuid', $uuid)->firstOrFail();

        return Storage::disk('local')->download($media->path, $media->name);
    }

    public function destroy($uuid)
    {
        $media = Media::where('uuid', $uuid)->firstOrFail();

        // Delete the file from storage
        Storage::disk('public')->delete($media->path);

        // Delete the database record
        $media->delete();

        return redirect()->route('dashboard')->with('success', 'File deleted successfully.');
    }


    /**
     * Streams an HLS file (playlist or TS segment) from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $folder The folder ID (e.g. ecf309c2-39a4-46ce-80a9-58213195f847)
     * @param string $file The file name (playlist.m3u8, or a TS segment)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function stream(Request $request, $folder, $file='playlist.m3u8')
    {
        // Build the full path.
        // Adjust the path if your folder structure is different.

        $path = Storage::disk('local')->path("/files/{$folder}/hls/{$file}");

        // Check if the file exists
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        // Determine the correct MIME type based on file extension.
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeType = 'application/octet-stream';

        if ($extension === 'm3u8') {
            // For HLS playlists (m3u8 files)
            $mimeType = 'application/vnd.apple.mpegurl';
        } elseif ($extension === 'ts') {
            // For TS segments
            $mimeType = 'video/MP2T';
        }

        // Create a BinaryFileResponse which automatically handles streaming the file.
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'no-cache, must-revalidate', // Optional: disable caching if desired
        ]);
    }


    public
    function play($uuid)
    {
            return view('play',['uuid'=>$uuid]);
    }
}
