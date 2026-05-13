<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\FooterTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FooterTemplateController extends Controller
{
    public function index(): View
    {
        $templates = FooterTemplate::query()
            ->latest()
            ->get();

        return view('admin.footers.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.footers.edit', [
            'footer' => null,
            'availableMenus' => Menu::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validateTemplate($request);

        $template = FooterTemplate::create([
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
                'message' => 'Footer template created successfully.',
                'redirect' => route('admin.footers.index'),
                'id' => $template->id,
            ]);
        }

        return redirect()->route('admin.footers.index')
            ->with('success', 'Footer template created successfully.');
    }

    public function edit(FooterTemplate $footerTemplate): View
    {
        return view('admin.footers.edit', [
            'footer' => $footerTemplate,
            'availableMenus' => Menu::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, FooterTemplate $footerTemplate): JsonResponse|RedirectResponse
    {
        $validated = $this->validateTemplate($request);

        $footerTemplate->update([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        if (!empty($validated['is_default'])) {
            $footerTemplate->setAsDefault();
        }

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Footer template updated successfully.',
                'redirect' => route('admin.footers.index'),
            ]);
        }

        return redirect()->route('admin.footers.index')
            ->with('success', 'Footer template updated successfully.');
    }

    public function destroy(Request $request, FooterTemplate $footerTemplate): JsonResponse|RedirectResponse
    {
        $footerTemplate->delete();

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Footer template deleted successfully.',
            ]);
        }

        return back()
            ->with('success', 'Footer template deleted successfully.');
    }

    public function setDefault(Request $request, FooterTemplate $footerTemplate): JsonResponse|RedirectResponse
    {
        $footerTemplate->setAsDefault();

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Footer template set as default.',
            ]);
        }

        return back()->with('success', 'Footer template set as default.');
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
