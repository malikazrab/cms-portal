<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Update ke case mein current post ID lo (slug conflict avoid karne ke liye)
        $postId = $this->route('post')?->id;

        return [
            'title'            => 'required|string|max:255',
            'slug'             => 'required|string|unique:posts,slug,' . $postId,
            'content'          => 'required|string',
            'excerpt'          => 'nullable|string|max:500',
            'featured_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'           => 'required|in:draft,published',
            'category_id'      => 'required|exists:categories,id',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Title zaroori hai.',
            'slug.required'        => 'Slug zaroori hai.',
            'slug.unique'          => 'Yeh slug already use ho chuka hai.',
            'content.required'     => 'Content zaroori hai.',
            'featured_image.image' => 'Sirf image file upload karein.',
            'featured_image.max'   => 'Image 2MB se badi nahi honi chahiye.',
            'status.required'      => 'Status select karein.',
            'status.in'            => 'Status sirf draft ya published ho sakta hai.',
            'category_id.required' => 'Category select karein.',
            'category_id.exists'   => 'Selected category exist nahi karti.',
        ];
    }
}