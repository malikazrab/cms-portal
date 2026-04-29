<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SlugController extends Controller
{
    public function generate(Request $request)
    {
        $title = $request->input('title');
        $slug = generateSlug($title);  // helper function
        return response()->json(['slug' => $slug]);
    }
}