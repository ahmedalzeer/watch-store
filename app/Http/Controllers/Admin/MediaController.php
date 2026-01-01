<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function uploadTemp(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('file')) {

            $path = $request->file('file')->store('temp', 'public');

            return response()->json([
                'path' => $path,
                'url'  => Storage::url($path),
            ]);
        }

        return response()->json(['error' => 'File not uploaded'], 400);
    }
}
