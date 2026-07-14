@extends('frontend.layouts.app')
@section('title', $pageContent->meta_title ?? 'Our Latest Blogs')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', $pageContent->meta_title ?? 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset($pageContent->meta_image ?? 'og-home.jpg'))

@push('extras')
<style>
    .blog-pagination {
        margin-top: 3rem;
    }

    .blog-pagination-list {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .blog-pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        height: 42px;
        padding: 0 0.85rem;
        border: 1px solid #e6ddd6;
        border-radius: 999px;
        background: #fff;
        color: #333;
        font-size: 0.95rem;
        font-weight: 500;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease;
    }

    .blog-pagination-btn:hover:not(.is-disabled):not(.is-active):not(.is-ellipsis) {
        border-color: var(--brand-color);
        color: var(--brand-color);
        box-shadow: var(--brand-shadow-sm);
        transform: translateY(-1px);
    }

    .blog-pagination-btn.is-active {
        background: var(--brand-color);
        border-color: var(--brand-color);
        color: #fff;
        box-shadow: var(--brand-shadow-sm);
    }

    .blog-pagination-btn.is-disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }

    .blog-pagination-btn.is-ellipsis {
        border-color: transparent;
        background: transparent;
        min-width: 28px;
        padding: 0;
        cursor: default;
    }
</style>
@endpush

@section('content')

<div class="px-3 px-md-5 py-lg-3">
    <!-- Header -->
    @if(!empty($pageContent?->heading))
    <div class="mb-5 pb-4 border-bottom">
        <div class="row align-items-center">
            <!-- Left Content -->
            <div class="col-md-12 text-center">
                <h1 class="fw-bold my-0">{{ $pageContent->heading }}</h1>
            </div>
        </div>
    </div>
    @endif

    <!-- Blog Cards -->
    <div class="row g-4">
        @foreach($blogs as $blog)
        <!-- Card 1 -->
        <div class="col-md-4 d-flex">
            <div class="card rounded-4 border-1 card-hover">
                <div class="card-image-container">
                    <img src="{{ asset('uploads/blogs/'.$blog->image) }}" class="card-img-top object-fit-cover rounded-top-4" alt="{{ $blog->alt_tag ?? $blog->title }}" />
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <small class="text-muted">{{ date('M d, Y', strtotime($blog->created_at)) }}</small>
                    <h5 class="card-title mt-2">{{ $blog->title }}</h5>
                    <p class="text-muted small">
                        {!! Str::limit(strip_tags($blog->content), 200) !!}
                    </p>
                    <a href="{{ route('blog.show', ['slug' => $blog->slug]) }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
    <div class="d-flex justify-content-center">
        {{ $blogs->links('vendor.pagination.frontend-blogs') }}
    </div>
</div>
</div>
<script>
    const textData = [];
    @foreach($highlights as $highlight)
        textData.push(`<span>{{ $highlight->title }}</span>
             <img src="{{ Storage::url($highlight->emoji) }}"
                  class="emoji"
                  alt="{{ $highlight->alt_text ?? $highlight->title }}">`);
    @endforeach
</script>
@endsection