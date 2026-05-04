<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $media = Media::with('user')->latest()->paginate(24);

        if ($request->expectsJson()) {
            return response()->json($media->items());
        }

        return view('admin.media.index', compact('media'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['file', 'max:10240'],
        ]);

        $uploaded = [];

        foreach ($request->file('files', []) as $file) {
            $path = $file->store('media', 'public');

            $uploaded[] = Media::create([
                'user_id' => $request->user()->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return response()->json($uploaded);
    }

    public function destroy(Media $medium)
    {
        if ($medium->file_path && Storage::disk('public')->exists($medium->file_path)) {
            Storage::disk('public')->delete($medium->file_path);
        }

        $medium->delete();

        return back()->with('success', 'Media deleted successfully.');
    }
}
