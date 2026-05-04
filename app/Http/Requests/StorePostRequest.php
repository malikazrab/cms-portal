<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    private const MAX_FEATURED_IMAGE_KB = 102400;

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
            'featured_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:' . self::MAX_FEATURED_IMAGE_KB,
            'status'           => 'required|in:draft,published,archived',
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
            'featured_image.image' => 'Please upload a valid image file.',
            'featured_image.mimes' => 'Featured image must be a JPG, PNG, WEBP, GIF, or AVIF file.',
            'featured_image.uploaded' => 'Image upload failed. Please use a JPG, PNG, or WEBP image under 100MB.',
            'featured_image.max'   => 'Featured image must be smaller than 100MB.',
            'status.required'      => 'Status select karein.',
            'status.in'            => 'Status draft, published, ya archived ho sakta hai.',
            'category_id.required' => 'Category select karein.',
            'category_id.exists'   => 'Selected category exist nahi karti.',
        ];
    }
}
