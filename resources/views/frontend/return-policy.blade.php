@extends('frontend.layouts.app')
@section('title', $pageContent->meta_title  ?? 'Terms & Conditions')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', $pageContent->meta_title ?? 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset($pageContent->meta_image ?? 'og-home.jpg'))

    @section('content')
        <section class="policy-page py-5">
            <div class="container">
                <h1>{{ $pageContent->title }}</h1>

                {!! $pageContent->content !!}
            </div>
        </section>
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