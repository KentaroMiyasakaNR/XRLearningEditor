<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MediaController extends Controller
{
    public function index()
    {
        $videos = $this->getMediaFiles('videos');
        $images = $this->getMediaFiles('images');

        return response()->json([
            'videos' => $videos,
            'images' => $images
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:mp4,webm,ogg,mov,avi,flv,wmv,mkv,m4v,3gp,quicktime|max:102400', // 100MB max、各種動画ファイルに対応
            'type' => 'required|in:videos,images'
        ]);

        $file = $request->file('file');
        $type = $request->input('type');
        $filename = $file->getClientOriginalName();

        // ファイル名が重複する場合は、タイムスタンプを付加
        if (Storage::disk('media')->exists($type . '/' . $filename)) {
            $filename = time() . '_' . $filename;
        }

        $path = $file->storeAs($type, $filename, 'media');

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'path' => $path
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'type' => 'required|in:videos,images'
        ]);

        $path = $request->input('type') . '/' . $request->input('filename');

        if (Storage::disk('media')->exists($path)) {
            Storage::disk('media')->delete($path);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }

    private function getMediaFiles($type)
    {
        $files = Storage::disk('media')->files($type);
        return collect($files)->map(function ($file) {
            return [
                'filename' => basename($file),
                'path' => $file,
                'url' => Storage::disk('media')->url($file)
            ];
        })->values();
    }
} 