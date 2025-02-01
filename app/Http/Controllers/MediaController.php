<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertAudioForStreaming;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

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

        $media = Media::create([
            'name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'visibility' => $request->visibility,
        ]);


       dispatch(new ConvertAudioForStreaming($media->id));


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

    public function stream(Request $request, $uuid, $segment = null)
    {

        $media = Media::where('uuid', $uuid)->firstOrFail();

        // اگر segment خالی باشد یعنی خود فایل m3u8 می‌خواهیم
        if (!$segment) {

            $m3u8Path =  storage_path("/app/private/hls/{$media->uuid}.m3u8");

            $content = Storage::disk('local')->get($m3u8Path);

            // اگر نیاز است لینک‌های داخل m3u8 را داینامیک کنید، می‌توانید با str_replace آدرس فایل‌های .ts را اصلاح کنید
            // مثلا:
            // $content = str_replace("segment0.ts", route('stream.segment', ['uuid' => $uuid, 'segment' => 'segment0.ts']), $content);

            return response($content, 200, [
                'Content-Type' => 'application/vnd.apple.mpegurl',
            ]);
        } else {
            // اگر segment ست شده یعنی درخواست فایل TS است
//            $tsPath = "hls/{$media->uuid}/{$segment}"; // بسته به ساختار
            $tsPath =  storage_path("/app/private/hls/{$media->uuid}/$segment");

            if (!Storage::disk('local')->exists($tsPath)) {
                abort(404, 'Segment not found');
            }

            $content = Storage::disk('local')->get($tsPath);

            return response($content, 200, [
                'Content-Type' => 'video/mp2t', // پسوند TS
            ]);
        }
    }
}
