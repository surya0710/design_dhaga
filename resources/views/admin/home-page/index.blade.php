@extends('layouts.admin')

@php
    $sectionMeta = [

        'desktop_info' => [
            'title' => '1. our-info',
            'where' => 'Shows below the top slider on desktop.',
            'hint' => 'This section only needs three headings with descriptions.',
            'fields' => ['items'],
            'item_count' => 3,
            'item_image' => false,
            'item_description' => true,
            'item_button_text' => false,
            'item_link' => false,
        ],

        'mobile_features' => [
            'title' => '2. Mobile Icon Row',
            'where' => 'Shows only on mobile devices below the slider.',
            'hint' => 'Three items only.',
            'fields' => ['items'],
            'item_count' => 3,
            'item_image' => true,
            'item_description' => false,
            'item_button_text' => false,
            'item_link' => false,
        ],

        'idea_brush' => [
            'title' => '3. your-idea-our-brush',
            'where' => 'Image on left and content on right.',
            'hint' => 'Use first line as paragraph and next lines as bullet points.',
            'fields' => ['title', 'body', 'button', 'image'],
            'heading_label' => 'Right Side Heading',
            'body_label' => 'Right Side Content',
        ],

        'graphics_design' => [
            'title' => '4. graphics',
            'where' => 'Content on left and image on right.',
            'hint' => 'Use first line as paragraph and next lines as bullet points.',
            'fields' => ['title', 'body', 'button', 'image'],
            'heading_label' => 'Left Side Heading',
            'body_label' => 'Left Side Content',
        ],

        'instagram_feed' => [
            'title' => '4b. Instagram Feed',
            'where' => 'Shows below the graphics section on the homepage.',
            'hint' => 'First line of body = @handle. Remaining lines = profile bio. Post caption goes in item description.',
            'fields' => ['title', 'subtitle', 'body', 'button', 'image', 'items'],
            'heading_label' => 'Profile Name',
            'subtitle_label' => 'Section Subtext',
            'body_label' => 'Profile Handle And Bio',
            'item_count' => 12,
            'item_image' => true,
            'item_description' => true,
            'item_button_text' => false,
            'item_link' => true,
        ],

        'who_we_are' => [
            'title' => '5. who-we-are',
            'where' => 'Section with four image cards.',
            'hint' => 'Add images and button text.',
            'fields' => ['title', 'subtitle', 'items'],
            'heading_label' => 'Section Heading',
            'subtitle_label' => 'Section Sub Heading',
            'item_count' => 4,
            'item_image' => true,
            'item_description' => false,
            'item_button_text' => true,
            'item_link' => true,
        ],

        'inspired_art' => [
            'title' => '6. inspired-by-art',
            'where' => 'Heading with three image cards.',
            'hint' => 'Use footer text in body.',
            'fields' => ['title', 'subtitle', 'body', 'items'],
            'heading_label' => 'Main Heading',
            'subtitle_label' => 'Sub Heading',
            'body_label' => 'Footer Text',
            'item_count' => 3,
            'item_image' => true,
            'item_description' => false,
            'item_button_text' => false,
            'item_link' => false,
        ],
    ];
@endphp

@section('content')

<style>

    .home-section-card{
        background:#fff;
        border-radius:22px;
        margin-bottom:28px;
        border:1px solid #e5e7eb;
        overflow:hidden;
        box-shadow:0 4px 18px rgba(15,23,42,.05);
    }

    .home-section-head{
        padding:24px 28px;
        background:#f8fafc;
        border-bottom:1px solid #e5e7eb;
    }

    .home-section-head h4{
        margin-bottom:6px;
        font-size:24px;
        color:#111827;
    }

    .home-help{
        color:#64748b;
        font-size:14px;
        line-height:1.6;
    }

    .section-save-btn{
        border:none;
        background:#111827;
        color:#fff;
        height:46px;
        padding:0 22px;
        border-radius:12px;
        font-size:14px;
        font-weight:600;
        transition:.2s;
        cursor:pointer;
        width:fit-content;
    }

    .section-save-btn:hover{
        background:#000;
    }

    .w-100{
        width:100%;
    }

    .home-section-body{
        padding:28px;
    }

    .form-style-modern{
        display:flex;
        flex-direction:column;
        gap:20px;
    }

    .modern-field label{
        display:block;
        font-size:13px;
        font-weight:600;
        margin-bottom:8px;
        color:#374151;
    }

    .modern-field input,
    .modern-field textarea,
    .modern-field select{
        width:100%;
        border:1px solid #d1d5db;
        border-radius:14px;
        padding:14px 16px;
        font-size:14px;
        background:#fff;
    }

    .home-field-note{
        display:block;
        margin-top:8px;
        color:#6b7280;
        font-size:12px;
    }

    .home-fixed-items{
        margin-top:35px;
        padding-top:30px;
        border-top:1px solid #e5e7eb;
    }

    .home-fixed-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
        gap:22px;
    }

    .home-fixed-card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:20px;
        padding:22px;
        box-shadow:0 4px 14px rgba(0,0,0,.04);
    }

    .home-fixed-number{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        background:#111827;
        color:#fff;
        border-radius:999px;
        padding:8px 16px;
        font-size:13px;
        font-weight:600;
        margin-bottom:18px;
    }

    .home-fixed-form{
        display:flex;
        flex-direction:column;
        gap:18px;
    }

    .home-field-group label{
        display:block;
        font-size:13px;
        font-weight:600;
        margin-bottom:8px;
        color:#374151;
    }

    .home-field-group input,
    .home-field-group textarea{
        width:100%;
        border:1px solid #d1d5db;
        border-radius:14px;
        padding:14px;
        font-size:14px;
    }

    .home-item-preview{
        width:200px;
        object-fit:cover;
        border-radius:16px;
        border:1px solid #e5e7eb;
        margin-bottom:12px;
    }

</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        @if(Session::has('status'))
            <p class="alert alert-success">
                {{ Session::get('status') }}
            </p>
        @endif

        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        @foreach($sections as $section)

            @php
                $meta = $sectionMeta[$section->key] ?? [
                    'title' => ucwords(str_replace('_',' ',$section->key)),
                    'where' => 'Homepage section.',
                    'hint' => 'Update section content.',
                    'fields' => ['title','subtitle','body','button','items'],
                ];

                $fields = collect($meta['fields']);
            @endphp

            <div class="home-section-card">

                <div class="home-section-head">

                    <h4>{{ $meta['title'] }}</h4>

                    <div class="home-help">
                        {{ $meta['where'] }}
                    </div>

                    <div class="home-help">
                        {{ $meta['hint'] }}
                    </div>

                </div>

                <div class="home-section-body">

                    <form id="section-form-{{ $section->id }}" class="form-style-modern" action="{{ route('admin.home-page.sections.update', $section) }}"
                    method="POST" enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        @if($fields->contains('title'))
                            <div class="modern-field">
                                <label>{{ $meta['heading_label'] ?? 'Section Heading' }}</label>

                                <input type="text" name="title" value="{{ old('title',$section->title) }}">
                            </div>
                        @endif

                        @if($fields->contains('subtitle'))
                            <div class="modern-field">
                                <label>{{ $meta['subtitle_label'] ?? 'Sub Heading' }}</label>

                                <input type="text" name="subtitle" value="{{ old('subtitle',$section->subtitle) }}">
                            </div>
                        @endif

                        @if($fields->contains('body'))
                            <div class="modern-field">
                                <label>{{ $meta['body_label'] ?? 'Content' }}</label>

                                <textarea name="body" rows="8" class="rich-editor">{{ old('body',$section->body) }}</textarea>

                                <span class="home-field-note">
                                    {{ $meta['hint'] }}
                                </span>
                            </div>
                        @endif

                        @if($fields->contains('image'))

                            <div class="modern-field">

                                <label>Section Image</label>

                                @if($section->image)
                                    <img src="{{ asset($section->image) }}?v={{ time() }}" class="home-item-preview" alt="{{ $section->alt_tag ?: ($section->title ?? 'Section image') }}">
                                @endif

                                <input type="file"
                                    name="image"
                                    accept="image/*">

                            </div>

                            <div class="modern-field">
                                <label>Image Alt Tag Content</label>
                                <input type="text" name="alt_tag" value="{{ old('alt_tag', $section->alt_tag) }}" placeholder="Describe the section image">
                            </div>

                        @endif

                        @if($fields->contains('button'))

                            <div class="modern-field">
                                <label>Button Text</label>

                                <input type="text" name="button_text" value="{{ old('button_text',$section->button_text) }}">
                            </div>

                            <div class="modern-field">
                                <label>Button URL</label>

                                <input type="text" name="button_url" value="{{ old('button_url',$section->button_url) }}">
                            </div>

                            <div class="modern-field">
                                <label>Open Button In</label>

                                <select name="button_target">
                                    <option value="_self"
                                        @selected($section->button_target === '_self')>
                                        Same Tab
                                    </option>

                                    <option value="_blank"
                                        @selected($section->button_target === '_blank')>
                                        New Tab
                                    </option>
                                </select>
                            </div>

                        @endif

                        <input type="hidden" name="bg_class" value="{{ $section->bg_class }}">

                        @if(
                            $fields->contains('title') ||
                            $fields->contains('subtitle') ||
                            $fields->contains('body') ||
                            $fields->contains('button')
                        )

                            <button type="submit" class="section-save-btn">
                                Save Section Content
                            </button>

                        @endif

                    </form>

                    @if($fields->contains('items'))

                        @php
                            $itemCount = $meta['item_count'] ?? 3;
                            $items = $section->items->take($itemCount);

                            $showDescription = $meta['item_description'] ?? true;
                            $showButtonText = $meta['item_button_text'] ?? false;
                            $showLink = $meta['item_link'] ?? false;
                        @endphp

                        <div class="home-fixed-items">

                            <div class="home-fixed-grid">

                                @foreach($items as $index => $item)

                                    <div class="home-fixed-card">

                                        <div class="home-fixed-number">
                                            Item {{ $index + 1 }}
                                        </div>

                                        <form action="{{ route('admin.home-page.items.update',$item) }}"
                                              method="POST"
                                              enctype="multipart/form-data"
                                              class="home-fixed-form">

                                            @csrf
                                            @method('PUT')

                                            @if($meta['item_image'] ?? false)

                                                <div class="home-image-upload">

                                                    @if($item->image)
                                                        <img src="{{ asset($item->image) }}?v={{ time() }}"
                                                             class="home-item-preview"
                                                             alt="{{ $item->alt_tag ?: ($item->title ?? 'Item image') }}">
                                                    @endif

                                                    <input type="file"
                                                           name="image"
                                                           accept="image/*">
                                                </div>

                                                <div class="home-field-group">
                                                    <label>Image Alt Tag Content</label>
                                                    <input type="text"
                                                           name="alt_tag"
                                                           value="{{ old('alt_tag', $item->alt_tag) }}"
                                                           placeholder="Defaults to item heading">
                                                </div>

                                            @endif

                                            <div class="home-field-group">
                                                <label>
                                                    {{ $showButtonText ? 'Button Text' : 'Heading' }}
                                                </label>

                                                <input type="text"
                                                       name="title"
                                                       value="{{ $item->title }}">
                                            </div>

                                            @if($showDescription)

                                                <div class="home-field-group">
                                                    <label>Description</label>

                                                    <textarea name="subtitle"
                                                              rows="3">{{ $item->subtitle }}</textarea>
                                                </div>

                                            @else

                                                <input type="hidden"
                                                       name="subtitle"
                                                       value="{{ $item->subtitle }}">

                                            @endif

                                            @if($showLink)

                                                <div class="home-field-group">
                                                    <label>Button Link URL</label>

                                                    <input type="text"
                                                           name="link_url"
                                                           value="{{ $item->link_url }}"
                                                           placeholder="/about-us">
                                                </div>

                                            @else

                                                <input type="hidden"
                                                       name="link_url"
                                                       value="{{ $item->link_url }}">

                                            @endif

                                            <input type="hidden"
                                                   name="status"
                                                   value="1">

                                            <input type="hidden"
                                                   name="sort_order"
                                                   value="{{ $index }}">

                                            <input type="hidden"
                                                   name="body"
                                                   value="{{ $item->body }}">

                                            <input type="hidden"
                                                   name="link_text"
                                                   value="{{ $item->link_text }}">

                                            <input type="hidden"
                                                   name="icon"
                                                   value="{{ $item->icon }}">

                                            <button type="submit"
                                                    class="section-save-btn w-100">
                                                Update Item
                                            </button>

                                        </form>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        @endforeach

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>

<script>
    tinymce.init({
        selector: '.rich-editor',

        height: 350,

        menubar: false,

        branding: false,

        plugins: [
            'advlist',
            'autolink',
            'lists',
            'link',
            'image',
            'preview',
            'searchreplace',
            'visualblocks',
            'code',
            'fullscreen',
            'table',
            'wordcount'
        ],

        toolbar:
            'undo redo | blocks | ' +
            'bold italic underline | ' +
            'alignleft aligncenter alignright | ' +
            'bullist numlist | ' +
            'link image table | code preview',

        content_style: `
            body {
                font-family: Inter, sans-serif;
                font-size: 14px;
                line-height: 1.7;
                padding: 10px;
            }
        `
    });
</script>

@endsection