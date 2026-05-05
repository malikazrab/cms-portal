<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicController extends Controller
{
    public function home()
    {
        $homePage = $this->resolveHomePage();

        if ($homePage) {
            return view('public.page', $this->publicViewData([
                'page' => $homePage,
            ]));
        }

        return view('public.home', $this->publicViewData());
    }

    public function blog()
    {
        $postsPerPage = (int) Setting::getValue('posts_per_page', 10);

        $posts = Post::with('user', 'category')
            ->where('status', 'published')
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', '%'.$search.'%')
                        ->orWhere('excerpt', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('published_at')
            ->paginate(max($postsPerPage, 1));

        return view('public.blog', $this->publicViewData([
            'posts' => $posts,
        ]));
    }

    public function showPost($slug)
    {
        $post = Post::with('user', 'category', 'tags')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('public.post', $this->publicViewData([
            'post' => $post,
        ]));
    }

    public function showPage($slug)
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.page', $this->publicViewData([
            'page' => $page,
        ]));
    }

    public function serveMedia(string $path): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }

    protected function resolveHomePage(): ?Page
    {
        $homePageId = Setting::getValue('home_page_id');

        if ($homePageId) {
            $homePage = Page::published()->find($homePageId);
            if ($homePage) {
                return $homePage;
            }
        }

        return Page::published()
            ->where('slug', 'home')
            ->first()
            ?? Page::published()->orderBy('id')->first();
    }

    protected function publicViewData(array $data = []): array
    {
        $settings = Setting::pluck('value', 'key');
        $homePageId = $settings->get('home_page_id') ?: $this->resolveHomePage()?->id;
        $headerPageId = $settings->get('header_page_id');
        $footerPageId = $settings->get('footer_page_id');

        $excludedIds = array_filter([$homePageId, $headerPageId, $footerPageId]);

        return array_merge($data, [
            'siteName' => $settings->get('site_name', config('app.name', 'CMS Portal')),
            'siteDescription' => $settings->get('site_description', ''),
            'navigationPages' => Page::published()
                ->when($excludedIds, fn ($query) => $query->whereNotIn('id', $excludedIds))
                ->orderBy('title')
                ->get(),
            'headerPage' => $headerPageId ? Page::published()->find($headerPageId) : null,
            'footerPage' => $footerPageId ? Page::published()->find($footerPageId) : null,
        ]);
    }
}
