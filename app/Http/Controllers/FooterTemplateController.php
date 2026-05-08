<?php

namespace App\Http\Controllers;

use App\Models\FooterTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FooterTemplateController extends Controller
{
    public function index()
    {
        $templates = FooterTemplate::all();
        return view('admin.footers.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.footers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'content' => 'required|string',
            'is_default' => 'sometimes|boolean',
        ]);

        $template = FooterTemplate::create([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'is_default' => $request->boolean('is_default', false),
            'created_by' => Auth::id(),
        ]);

        if ($request->boolean('is_default')) {
            $template->setAsDefault();
        }

        return redirect()->route('admin.footers.index')
            ->with('success', 'Footer template created successfully.');
    }

    public function edit($id)
    {
        $template = FooterTemplate::findOrFail($id);
        return view('admin.footers.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = FooterTemplate::findOrFail($id);

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

        return redirect()->route('admin.footers.index')
            ->with('success', 'Footer template updated successfully.');
    }

    public function destroy($id)
    {
        $template = FooterTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.footers.index')
            ->with('success', 'Footer template deleted successfully.');
    }

    public function setDefault($id)
    {
        $template = FooterTemplate::findOrFail($id);
        $template->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Footer template set as default.',
        ]);
    }
}