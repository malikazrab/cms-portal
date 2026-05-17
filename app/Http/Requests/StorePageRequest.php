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
            'title'            => 'required|string|max:255|unique:pages,title,' . $pageId,
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
            'title.required' => 'A page title is required.',
            'title.unique' => 'A page with this title already exists. Please choose a different title.',
            'slug.required' => 'A page slug is required.',
            'slug.unique' => 'A page with this slug already exists. Please choose a different slug.',
            'status.required' => 'Please select a page status.',
            'status.in' => 'The page status must be either draft or published.',
        ];
    }
}
