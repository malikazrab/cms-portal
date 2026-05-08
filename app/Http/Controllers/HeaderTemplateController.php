<?php

namespace App\Http\Controllers;

use App\Models\HeaderTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeaderTemplateController extends Controller
{
    public function index()
    {
        $templates = HeaderTemplate::all();
        return view('admin.headers.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.headers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'content' => 'required|string',
            'is_default' => 'sometimes|boolean',
        ]);

        $template = HeaderTemplate::create([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'is_default' => $request->boolean('is_default', false),
            'created_by' => Auth::id(),
        ]);

        if ($request->boolean('is_default')) {
            $template->setAsDefault();
        }

        return redirect()->route('admin.headers.index')
            ->with('success', 'Header template created successfully.');
    }

    public function edit($id)
    {
        $template = HeaderTemplate::findOrFail($id);
        return view('admin.headers.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = HeaderTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'content' => 'required|string',
            'is_default' => 'sometimes|boolean',
        ]);

        $template->update([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'is_default' => $request->boolean('is_default', false),
        ]);

        if ($request->boolean('is_default')) {
            $template->setAsDefault();
        }

        return redirect()->route('admin.headers.index')
            ->with('success', 'Header template updated successfully.');
    }

    public function destroy($id)
    {
        $template = HeaderTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.headers.index')
            ->with('success', 'Header template deleted successfully.');
    }

    public function setDefault($id)
    {
        $template = HeaderTemplate::findOrFail($id);
        $template->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Header template set as default.',
        ]);
    }
}