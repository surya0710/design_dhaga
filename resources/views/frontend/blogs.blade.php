@extends('frontend.layouts.app')
@section('title', $pageContent->meta_title ?? 'Our Latest Blogs')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', $pageContent->meta_title ?? 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset($pageContent->meta_image ?? 'og-home.jpg'))

@section('content')

<div class="px-3 px-md-5 py-lg-3">
    <!-- Header -->
    @if(!empty($pageContent?->heading))
    <div class="mb-5 pb-4 border-bottom">
        <div class="row align-items-center">
            <!-- Left Content -->
            <div class="col-md-12">
                <h1 class="fw-bold mb-0">{{ $pageContent->heading }}</h1>
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
                    <img src="{{ asset('uploads/blogs/'.$blog->image) }}" class="card-img-top object-fit-cover rounded-top-4" alt="{{ $blog->title }}" />
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