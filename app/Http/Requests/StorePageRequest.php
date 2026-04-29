<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pageId = $this->route('page')?->id;

        return [
            'title'            => 'required|string|max:255',
            'slug'             => 'required|string|unique:pages,slug,' . $pageId,
            'content'          => 'nullable|string',
            'status'           => 'required|in:draft,published',
            'template'         => 'nullable|string|max:100',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'  => 'Title zaroori hai.',
            'slug.required'   => 'Slug zaroori hai.',
            'slug.unique'     => 'Yeh slug already use ho chuka hai.',
            'status.required' => 'Status select karein.',
            'status.in'       => 'Status sirf draft ya published ho sakta hai.',
        ];
    }
}