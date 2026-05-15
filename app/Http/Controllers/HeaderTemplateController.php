<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\HeaderTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HeaderTemplateController extends Controller
{
    public function index(): View
    {
        $templates = HeaderTemplate::query()
            ->latest()
            ->get();

        return view('admin.headers.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.headers.create', [
            'header' => null,
            'availableMenus' => Menu::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validateTemplate($request);

        $template = HeaderTemplate::create([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'is_default' => $validated['is_default'] ?? false,
            'created_by' => Auth::id(),
        ]);

        if (!empty($validated['is_default'])) {
            $template->setAsDefault();
        }

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Header template created successfully.',
                'redirect' => route('admin.headers.index'),
                'id' => $template->id,
            ]);
        }

        return redirect()->route('admin.headers.index')
            ->with('success', 'Header template created successfully.');
    }

    public function edit(HeaderTemplate $headerTemplate): View
    {
        return view('admin.headers.edit', [
            'header' => $headerTemplate,
            'availableMenus' => Menu::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, HeaderTemplate $headerTemplate): JsonResponse|RedirectResponse
    {
        $validated = $this->validateTemplate($request);

        $headerTemplate->update([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        if (!empty($validated['is_default'])) {
            $headerTemplate->setAsDefault();
        }

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Header template updated successfully.',
                'redirect' => route('admin.headers.index'),
            ]);
        }

        return redirect()->route('admin.headers.index')
            ->with('success', 'Header template updated successfully.');
    }

    public function destroy(Request $request, HeaderTemplate $headerTemplate): JsonResponse|RedirectResponse
    {
        $headerTemplate->delete();

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Header template deleted successfully.',
            ]);
        }

        return back()
            ->with('success', 'Header template deleted successfully.');
    }

    public function setDefault(Request $request, HeaderTemplate $headerTemplate): JsonResponse|RedirectResponse
    {
        $headerTemplate->setAsDefault();

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Header template set as default.',
            ]);
        }

        return back()->with('success', 'Header template set as default.');
    }

    private function validateTemplate(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'content' => ['required', 'array'],
            'is_default' => ['sometimes', 'boolean'],
        ]);
    }
}
