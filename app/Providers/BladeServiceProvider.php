<?php

declare(strict_types=1);

namespace App\Providers;

use App\View\Components\TableSkeleton;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

final class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('table-skeleton', TableSkeleton::class);
        Vite::macro('image', fn (string $asset) => Vite::asset("resources/images/{$asset}"));

        $this->registerFoundationBlogComponents();
    }

    /**
     * Foundation 20.x only registers these aliases in its blog provider, which
     * isn't loaded here, but `php artisan optimize` still compiles the blog
     * views that use them. Foundation 22.3 registers them globally, so this
     * can go once develop is on that version.
     */
    private function registerFoundationBlogComponents(): void
    {
        Blade::component('ark::components.blog.article-content', 'ark-blog.article-content');
        Blade::component('ark::components.blog.author-header', 'ark-blog.author-header');
        Blade::component('ark::components.blog.category-badge', 'ark-blog.category-badge');
        Blade::component('ark::components.blog.blog-entry', 'ark-blog.blog-entry');
        Blade::component('ark::components.blog.header', 'ark-blog.header');
        Blade::component('ark::components.blog.placeholder-article-entry', 'ark-blog.placeholder-article-entry');
        Blade::component('ark::components.blog.related-article-entry', 'ark-blog.related-article-entry');
        Blade::component('ark::components.blog.related-articles', 'ark-blog.related-articles');
        Blade::component('ark::components.blog.sort', 'ark-blog.sort');
        Blade::component('ark::components.blog.search-input', 'ark-blog.search-input');
        Blade::component('ark::components.blog.filter-dropdown', 'ark-blog.filter-dropdown');
    }
}
