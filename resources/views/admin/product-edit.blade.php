@extends('layouts.admin')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush


@section('content')

<div class="main-content-inner">
<div class="main-content-wrap">


{{-- PAGE HEADER --}}
<div class="page-header">
    <div style="display:flex;align-items:center;gap:14px">
        <h3>Edit Product</h3>
        <span class="edit-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Editing
        </span>
    </div>
    <ul class="breadcrumbs">
        <li><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="sep">›</li>
        <li><a href="{{ route('admin.products') }}">Products</a></li>
        <li class="sep">›</li>
        <li>Edit Product</li>
    </ul>
</div>


{{-- VALIDATION ERRORS --}}
@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $err)
        <div>· {{ $err }}</div>
    @endforeach
</div>
@endif


<form method="POST" enctype="multipart/form-data" action="{{ route('admin.product.update', $product->id) }}">
@csrf
@method('PUT')

<input type="hidden" name="id" value="{{ $product->id }}">

<div class="form-grid">


    {{-- ═══════════ LEFT COLUMN ═══════════ --}}
    <div class="form-left">


        {{-- BASIC INFO --}}
        <div class="card">
            <div class="card-title">Basic Information</div>

            <div class="field">
                <label>Product Name <span class="req">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" placeholder="e.g. Hand-Painted Ceramic Vase" required>
                @error('name')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="divider"></div>

            <div class="field slug-field">
                <label>Slug <span class="req">*</span></label>
                <input type="text" name="slug" id="slugField" value="{{ old('slug', $product->slug) }}" placeholder="auto-generated-slug" required>
                @error('slug')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="divider"></div>

            <div class="cols-2">
                <div class="field">
                    <label>Purchase Type</label>
                    <select name="purchase_type">
                        <option value="1" {{ old('purchase_type', $product->type) == 1 ? 'selected' : '' }}>Add To Cart</option>
                        <option value="2" {{ old('purchase_type', $product->type) == 2 ? 'selected' : '' }}>Request To Purchase</option>
                    </select>
                </div>

                <div class="field">
                    <label>Category <span class="req">*</span></label>
                    <select name="category_id">
                        <option value="">Choose Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="field">
                <label>Sub Title <span class="req">*</span></label>
                <input type="text" name="short_description" id="" value="{{ old('short_description', $product->short_description) }}" placeholder="Discover exquisite Kantha hand painted dupattas on our store with tassles." required>
                @error('short_description')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Description <span class="req">*</span></label>
                <textarea name="description" class="ht-sm editor" placeholder="Describe the product…">{{ old('description', $product->description) }}</textarea>
                @error('description')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>


        {{-- EXTRA DETAILS --}}
        <div class="card">
            <div class="card-title">Product Details</div>

            <div class="field">
                <label>Hand Painted Details</label>
                <textarea name="hand_painted_details" class="ht-sm editor" placeholder="Describe the hand-painted elements…">{{ old('hand_painted_details', $product->hand_painted_details) }}</textarea>
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Care Instructions</label>
                <textarea name="care_instructions" class="ht-sm editor" placeholder="How to care for this product…">{{ old('care_instructions', $product->care_instructions) }}</textarea>
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Manufacturing Details</label>
                <textarea name="manufacturing_details" class="ht-sm editor" placeholder="Where and how it's made…">{{ old('manufacturing_details', $product->manufacturing_details) }}</textarea>
            </div>
        </div>


        {{-- PRODUCT IMAGES --}}
        <div class="card">
            <div class="card-title">Product Images</div>

            {{-- MAIN IMAGE --}}
            <div class="field">
                <label>Main Image</label>
                <input type="hidden" name="image" id="product_image" value="{{ old('image', $product->image) }}">

                @php
                    $hasMain = !empty($product->image);
                    $mainSrc = $hasMain ? asset('storage/' . $product->image) : '';
                @endphp

                <div class="media-picker {{ $hasMain ? 'has-image' : '' }}"
                     id="picker_product_image"
                     onclick="openMediaUploader('product_image','preview_product_image','picker_product_image')">
                    <div class="pick-icon" {{ $hasMain ? 'style=display:none' : '' }}>🖼</div>
                    <div class="pick-label" {{ $hasMain ? 'style=display:none' : '' }}>Click to choose main product image</div>
                    <span class="pick-btn" {{ $hasMain ? 'style=display:none' : '' }}>Browse Media</span>
                    <img id="preview_product_image" src="{{ $mainSrc }}" style="{{ $hasMain ? '' : 'display:none' }}"> 
                    <div class="overlay">
                        <button type="button" onclick="event.stopPropagation(); openMediaUploader('product_image','preview_product_image','picker_product_image')">Change</button>
                        <button type="button" class="del-btn" onclick="event.stopPropagation(); clearImage('product_image','preview_product_image','picker_product_image')">Remove</button>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- GALLERY --}}
            <div class="field">
                <label>Gallery Images</label>
                <input type="hidden" name="gallery" id="gallery_images" value="{{ old('gallery', $product->galleryImages->pluck('image')->implode(',')) }}">

                <div id="gallery_preview" class="gallery-grid">
                    @foreach($product->galleryImages as $img)
                        @php
                            $gPath = $img->image;
                            $gSrc  = asset('storage/' . $gPath);
                        @endphp
                        <div class="gallery-thumb">
                            <img src="{{ $gSrc }}" alt="">
                            <button type="button" class="remove-thumb" onclick="removeGalleryThumb(this,'{{ $gPath }}')">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="gallery-add-btn" onclick="openMediaUploaderMultiple('gallery_images','gallery_preview')">+</button>
                </div>
            </div>
        </div>


        {{-- ARTISAN GALLERY --}}
        <div class="card">
            <div class="card-title">Artisan Gallery</div>

            <div class="field">
                <label>Artisan Heading</label>
                <input type="text" name="artisan_heading" class="ht-sm" placeholder="Section Heading" value="{{ old('artisan_heading', $product->artisan_heading) }}">
            </div>

            <div class="divider"></div>

            <div class="artisan-grid">
                @for($i = 1; $i <= 3; $i++)
                @php
                    $artisan = $product->artisanImages->values()->get($i - 1);
                    $aHasImg = $artisan && !empty($artisan->image);
                    $aSrc    = '';
                    if ($aHasImg) {
                        $aSrc = asset('storage/' . $artisan->image);
                    }
                @endphp

                <div class="artisan-slot">
                    <div class="slot-num">Slot {{ $i }}</div>

                    <input type="hidden" name="artisan_gallery[{{ $i }}][image]" id="artisan_image_{{ $i }}" value="{{ old('artisan_gallery.'.$i.'.image', $artisan->image ?? '') }}">

                    @if($artisan)
                    <input type="hidden" name="artisan_gallery[{{ $i }}][id]" value="{{ $artisan->id }}">
                    @endif

                    <div class="media-picker {{ $aHasImg ? 'has-image' : '' }}" id="picker_artisan_image_{{ $i }}" style="min-height:100px" onclick="openMediaUploader('artisan_image_{{ $i }}','preview_artisan_image_{{ $i }}','picker_artisan_image_{{ $i }}')">
                        <div class="pick-icon" style="font-size:20px;{{ $aHasImg ? 'display:none' : '' }}">🖼</div>
                        <span class="pick-btn" style="font-size:11px;padding:5px 10px;{{ $aHasImg ? 'display:none' : '' }}">Select</span>
                        <img id="preview_artisan_image_{{ $i }}" src="{{ $aSrc }}" style="{{ $aHasImg ? '' : 'display:none' }}">
                        <div class="overlay">
                            <button type="button" onclick="event.stopPropagation(); openMediaUploader('artisan_image_{{ $i }}','preview_artisan_image_{{ $i }}','picker_artisan_image_{{ $i }}')">Change</button>
                            <button type="button" class="del-btn" onclick="event.stopPropagation(); clearImage('artisan_image_{{ $i }}','preview_artisan_image_{{ $i }}','picker_artisan_image_{{ $i }}')">✕</button>
                        </div>
                    </div>

                    <input type="text" name="artisan_gallery[{{ $i }}][title]" placeholder="Title / Heading" value="{{ old('artisan_gallery.'.$i.'.title', $artisan->title ?? '') }}">

                    <textarea name="artisan_gallery[{{ $i }}][description]" placeholder="Short description…">{{ old('artisan_gallery.'.$i.'.description', $artisan->description ?? '') }}</textarea>
                </div>
                @endfor
            </div>
        </div>

        {{-- Square Banner --}}
        <div class="card">
            <div class="card-title">Square Banner</div>
            <div class="artisan-slot">

                @php
                    $hasBanner = !empty($product->square_banner);
                    $bannerSrc = $hasBanner ? asset('storage/' . $product->square_banner) : '';
                @endphp

                <input type="hidden" name="square_banner" id="square_banner" value="{{ old('square_banner', $product->square_banner) }}">

                <div class="media-picker {{ $hasBanner ? 'has-image' : '' }}" id="picker_square_banner" style="min-height:100px"
                    onclick="openMediaUploader('square_banner','preview_square_banner','picker_square_banner')">
                    <div class="pick-icon" style="font-size:20px;{{ $hasBanner ? 'display:none' : '' }}">🖼</div>
                    <span class="pick-btn" style="font-size:11px;padding:5px 10px;{{ $hasBanner ? 'display:none' : '' }}">Select</span>
                    <img id="preview_square_banner" src="{{ $bannerSrc }}" style="{{ $hasBanner ? '' : 'display:none' }}">
                    <div class="overlay">
                        <button type="button" onclick="event.stopPropagation(); openMediaUploader('square_banner','preview_square_banner','picker_square_banner')">Change</button>
                        <button type="button" class="del-btn" onclick="event.stopPropagation(); clearImage('square_banner','preview_square_banner','picker_square_banner')">✕</button>
                    </div>
                </div>

                <input type="text" name="square_banner_title" placeholder="Title / Heading"
                    value="{{ old('square_banner_title', $product->square_banner_title) }}">

                <textarea name="square_banner_description" placeholder="Short description…">{{ old('square_banner_description', $product->square_banner_description) }}</textarea>
            </div>
        </div>


        {{-- PRODUCT ATTRIBUTES --}}
        <div class="card">
            <div class="card-title">Attributes</div>

            <div id="attribute-wrapper" style="display:flex;flex-direction:column;gap:10px">
                @forelse($product->productAttributes as $attr)
                <div class="attr-row">
                    <input type="text" name="attributes[key][]"
                           placeholder="e.g. Material" value="{{ $attr->key }}">
                    <input type="text" name="attributes[value][]"
                           placeholder="e.g. Ceramic" value="{{ $attr->value }}">
                    <button type="button" class="attr-remove remove-attribute" title="Remove">✕</button>
                </div>
                @empty
                <div class="attr-row">
                    <input type="text" name="attributes[key][]" placeholder="e.g. Material">
                    <input type="text" name="attributes[value][]" placeholder="e.g. Ceramic">
                    <button type="button" class="attr-remove remove-attribute" title="Remove">✕</button>
                </div>
                @endforelse
            </div>

            <div class="divider"></div>

            <button type="button" id="add-attribute" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Add Attribute
            </button>
        </div>

        {{--  Meta Details --}}
        <div class="card">
            <div class="card-title">Meta Details</div>

            <div class="field">
                <label for="meta_title">Meta Title</label>
                <input type="text" name="meta_title" placeholder="Meta Title" value="{{ old('meta_title', $product->meta_title) }}">
            </div>

            <div class="field">
                <label for="meta_description">Meta Description</label>
                <textarea name="meta_description" placeholder="Meta Description">{{ old('meta_description', $product->meta_description) }}</textarea>
            </div>
            
            <div class="field">
                <label for="meta_keywords">Meta Keywords</label>
                <input type="text" name="meta_keywords" placeholder="Meta Keywords" value="{{ old('meta_keywords', $product->meta_keywords) }}">
            </div>
        </div>

        {{-- Upload Icons --}}
        <div class="card">
            <div class="card-title">Upload Product Icons</div>

            <div class="artisan-grid">
                @for($i = 1; $i <= 6; $i++)
                @php
                    $icon           = $product->icons->firstWhere('position', $i);
                    $defaultIcon    = ["1776047104_1.svg", "1776047104_2.svg", "1776047104_3.svg", "1776047104_4.svg", "1776047104_5.svg", "1776047104_6.svg"];
                    $defaultText    = ["Natural Fibre", "Hand Painted", "Made In India", "Limited Edition", "Timeless Appeal", "Pack of 1"]
                    $icon           = $icon ? $icon : (object)['image' => $defaultIcon[$i-1]];  
                    $src            = $icon ? asset('storage/'.$icon->image) : asset('frontend_assets/images/icons/'.$defaultIcon[$i-1]);
                @endphp
                <div class="row">
                    <div class="image-picker {{ $icon->image ? 'has-image' : '' }}" id="picker_productIcons_{{ $i }}"
                        onclick="openMediaUploader('productIcons_{{ $i }}','preview_productIcons_{{ $i }}','picker_productIcons_{{ $i }}')">

                        {{-- Hidden image --}}
                        <input type="hidden" name="product_icons[{{ $i }}][image]" id="productIcons_{{ $i }}" value="{{ $icon->image ?? $defaultIcon[$i-1] }}">

                        <div class="pick-icon">🖼</div>
                        <span class="pick-btn">Select</span>

                        <img id="preview_productIcons_{{ $i }}" src="{{ $src }}">

                        <div class="overlay">
                            <button type="button" onclick="event.stopPropagation(); openMediaUploader('productIcons_{{ $i }}','preview_productIcons_{{ $i }}','picker_productIcons_{{ $i }}')">
                                Change
                            </button>

                            <button type="button" class="del-btn" onclick="event.stopPropagation(); clearImage('productIcons_{{ $i }}','preview_productIcons_{{ $i }}','picker_productIcons_{{ $i }}')">
                                ✕
                            </button>
                        </div>
                    </div>

                    {{-- Text --}}
                    <input type="text" name="product_icons[{{ $i }}][text]" placeholder="Text" value="{{ $icon->text ?? '' }}">
                </div>
                @endfor
            </div>
        </div>
    </div>
    {{-- END LEFT --}}


    {{-- ═══════════ RIGHT COLUMN ═══════════ --}}
    <div class="form-right">


        {{-- UPDATE BUTTON --}}
        <div class="card">
            <div class="card-title">Update</div>

            <button class="btn btn-primary btn-full" type="submit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Save Changes
            </button>

            <div class="divider"></div>

            <div class="field">
                <label>Stock Status</label>
                <div class="stock-toggle">
                    <input type="radio" name="stock_status" id="in_stock" value="1"
                           {{ old('stock_status', $product->stock_status) == 1 ? 'checked' : '' }}>
                    <label for="in_stock">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        In Stock
                    </label>
                    <input type="radio" name="stock_status" id="out_stock" value="0"
                           {{ old('stock_status', $product->stock_status) == 0 ? 'checked' : '' }}>
                    <label for="out_stock">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                        Out of Stock
                    </label>
                </div>
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Featured</label>
                <select name="featured">
                    <option value="0" {{ old('featured', $product->featured) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('featured', $product->featured) == 1 ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
        </div>


        {{-- PRICING --}}
        <div class="card">
            <div class="card-title">Pricing</div>

            <div class="field">
                <label>Regular Price <span class="req">*</span></label>
                <div class="price-prefix">
                    <span>₹</span>
                    <input type="number" name="regular_price" step="0.01" value="{{ old('regular_price', $product->regular_price) }}" placeholder="0.00" required>
                </div>
                @error('regular_price')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Sale Price</label>
                <div class="price-prefix">
                    <span>₹</span>
                    <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price', $product->sale_price) }}" placeholder="0.00">
                </div>
            </div>
        </div>


        {{-- INVENTORY --}}
        <div class="card">
            <div class="card-title">Inventory</div>

            <div class="field">
                <label>SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="e.g. ART-001">
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Quantity</label>
                <input type="number" name="quantity" min="0" value="{{ old('quantity', $product->quantity) }}" placeholder="0">
            </div>
        </div>


        {{-- SHIPPING --}}
        <div class="card">
            <div class="card-title">Shipping</div>

            <div class="field">
                <label>Weight</label>
                <input type="text" name="weight" value="{{ old('weight', $product->weight) }}" placeholder="e.g. 500g">
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Dimensions</label>
                <input type="text" name="dimension" value="{{ old('dimension', $product->dimension) }}" placeholder="e.g. 10×10×20 cm">
            </div>
        </div>


        {{-- META --}}
        <div class="card">
            <div class="card-title">Meta</div>

            <div class="field">
                <label>Color</label>
                <input type="text" name="color" value="{{ old('color', $product->color) }}" placeholder="e.g. Cobalt Blue">
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Tags</label>
                <input type="text" name="tags" value="{{ old('tags', $product->tags) }}" placeholder="handmade, ceramic, artisan">
            </div>
        </div>


    </div>
    {{-- END RIGHT --}}


</div>
{{-- /form-grid --}}

</form>


{{-- ══════════════════════════════════════════════════════════════
     MEDIA MANAGER MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="mm-overlay" id="mediaModal" onclick="handleOverlayClick(event)">
    <div class="mm-shell" onclick="event.stopPropagation()">

        <div class="mm-header">
            <h4>Media Library</h4>
            <div class="mm-header-actions">
                <input type="text" class="mm-search" id="mmSearch"
                       placeholder="Search media…" oninput="filterMedia(this.value)">
                <button class="mm-close" onclick="closeMediaModal()" title="Close">✕</button>
            </div>
        </div>

        <div class="mm-body">

            <div class="mm-sidebar">
                <div class="mm-sidebar-label">Library</div>
                <div class="mm-nav-item active" onclick="setFilter('all',this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
                    </svg>
                    All Files
                </div>
                <div class="mm-nav-item" onclick="setFilter('image',this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="M21 15l-5-5L5 21"/>
                    </svg>
                    Images
                </div>
                <div class="mm-sidebar-label">Sort</div>
                <div class="mm-nav-item active" onclick="setSort('newest',this)">Newest First</div>
                <div class="mm-nav-item"        onclick="setSort('oldest',this)">Oldest First</div>
            </div>

            <div class="mm-main">
                <div class="mm-toolbar">
                    <span class="mm-count" id="mmCount">0 items</span>
                    <label class="mm-upload-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>
                        Upload File
                        <input type="file" id="mmUploadInput" accept="image/*" multiple>
                    </label>
                </div>
                <div class="mm-grid" id="mmGrid"></div>
            </div>

        </div>

        <div class="mm-footer">
            <div class="mm-selected-info" id="mmSelectedInfo">No file selected</div>
            <button class="mm-confirm" id="mmConfirmBtn" onclick="confirmSelection()">Insert Selected</button>
        </div>

    </div>
</div>


</div>
</div>

@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '.editor',
    height: 250,
    menubar: false,
    plugins: 'lists link image code',
    toolbar: 'undo redo | bold italic underline | bullist numlist | link | code',
});
</script>

<script>
/* ─────────────────────────────────────────────────────────────
   STATE
───────────────────────────────────────────────────────────── */
let mmInputId      = null;
let mmPreviewId    = null;
let mmPickerId     = null;
let mmMultiple     = false;
let mmSelected     = [];
let mmAllMedia     = [];
let mmCurrentSort  = 'newest';
let mmCurrentFilter= 'all';

/* ─────────────────────────────────────────────────────────────
   AUTO SLUG
───────────────────────────────────────────────────────────── */
$("#productName").on("change", function () {
    const slug = this.value.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
    $("#slugField").val(slug);
});

/* ─────────────────────────────────────────────────────────────
   ATTRIBUTE ROWS
───────────────────────────────────────────────────────────── */
document.getElementById("add-attribute").addEventListener("click", function () {
    document.getElementById("attribute-wrapper").insertAdjacentHTML("beforeend", `
        <div class="attr-row">
            <input type="text" name="attributes[key][]" placeholder="e.g. Material">
            <input type="text" name="attributes[value][]" placeholder="e.g. Ceramic">
            <button type="button" class="attr-remove remove-attribute" title="Remove">✕</button>
        </div>
    `);
});

document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-attribute")) {
        e.target.closest(".attr-row").remove();
    }
});

/* ─────────────────────────────────────────────────────────────
   OPEN MEDIA MODAL
───────────────────────────────────────────────────────────── */
function openMediaUploader(inputId, previewId, pickerId) {
    mmInputId   = inputId;
    mmPreviewId = previewId;
    mmPickerId  = pickerId;
    mmMultiple  = false;
    mmSelected  = [];
    document.getElementById("mediaModal").classList.add("open");
    loadMedia();
}

function openMediaUploaderMultiple(inputId, previewId) {
    mmInputId   = inputId;
    mmPreviewId = previewId;
    mmPickerId  = null;
    mmMultiple  = true;
    mmSelected  = [];
    document.getElementById("mediaModal").classList.add("open");
    loadMedia();
}

function closeMediaModal() {
    document.getElementById("mediaModal").classList.remove("open");
    mmSelected = [];
    document.getElementById("mmConfirmBtn").classList.remove("ready");
    document.getElementById("mmSelectedInfo").textContent = "No file selected";
}

function handleOverlayClick(e) {
    if (e.target === document.getElementById("mediaModal")) closeMediaModal();
}

/* ─────────────────────────────────────────────────────────────
   LOAD & RENDER
───────────────────────────────────────────────────────────── */
function loadMedia() {
    document.getElementById("mmGrid").innerHTML =
        `<div class="mm-uploading"><div class="mm-spinner"></div> Loading media…</div>`;

    fetch('/admin/media')
        .then(r => r.json())
        .then(data => { mmAllMedia = data; renderGrid(data); })
        .catch(() => {
            document.getElementById("mmGrid").innerHTML =
                `<div class="mm-empty"><p>Could not load media.</p></div>`;
        });
}

function renderGrid(data) {
    let items = [...data];
    if (mmCurrentSort === 'oldest') items.reverse();
    const q = document.getElementById("mmSearch").value.toLowerCase();
    if (q) items = items.filter(i => i.file_path.toLowerCase().includes(q));

    document.getElementById("mmCount").textContent =
        `${items.length} item${items.length !== 1 ? 's' : ''}`;

    if (!items.length) {
        document.getElementById("mmGrid").innerHTML = `
            <div class="mm-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <path d="M21 15l-5-5L5 21"/>
                </svg>
                <p>No media found</p>
            </div>`;
        return;
    }

    document.getElementById("mmGrid").innerHTML = items.map(item => {
        const sel  = mmSelected.includes(item.file_path) ? 'selected' : '';
        const name = item.file_path.split('/').pop();
        return `
            <div class="mm-item ${sel}" data-path="${item.file_path}"
                 onclick="toggleItem(this,'${item.file_path}')">
                <img src="/storage/${item.file_path}" alt="${name}" loading="lazy">
                <div class="mm-item-name">${name}</div>
            </div>`;
    }).join('');
}

/* ─────────────────────────────────────────────────────────────
   SELECT / TOGGLE
───────────────────────────────────────────────────────────── */
function toggleItem(el, path) {
    if (mmMultiple) {
        const idx = mmSelected.indexOf(path);
        if (idx > -1) { mmSelected.splice(idx, 1); el.classList.remove("selected"); }
        else          { mmSelected.push(path);      el.classList.add("selected"); }
    } else {
        document.querySelectorAll(".mm-item").forEach(i => i.classList.remove("selected"));
        mmSelected = [path];
        el.classList.add("selected");
    }
    updateFooter();
}

function updateFooter() {
    const n   = mmSelected.length;
    const btn = document.getElementById("mmConfirmBtn");
    document.getElementById("mmSelectedInfo").innerHTML =
        n ? `<strong>${n}</strong> file${n > 1 ? 's' : ''} selected` : "No file selected";
    btn.classList.toggle("ready", n > 0);
}

/* ─────────────────────────────────────────────────────────────
   CONFIRM SELECTION
───────────────────────────────────────────────────────────── */
function confirmSelection() {
    if (!mmSelected.length) return;

    if (mmMultiple) {
        const input   = document.getElementById(mmInputId);
        const current = input.value ? input.value.split(',') : [];
        mmSelected.forEach(path => { if (!current.includes(path)) current.push(path); });
        input.value = current.join(',');

        const preview = document.getElementById(mmPreviewId);
        // remove existing thumbs, keep the + button
        preview.querySelectorAll('.gallery-thumb').forEach(t => t.remove());
        // prepend thumbs before the + button
        const addBtn = preview.querySelector('.gallery-add-btn');
        current.forEach(path => {
            const div = document.createElement('div');
            div.className = 'gallery-thumb';
            div.innerHTML = `
                <img src="/storage/${path}" alt="">
                <button type="button" class="remove-thumb" onclick="removeGalleryThumb(this,'${path}')">✕</button>`;
            preview.insertBefore(div, addBtn);
        });

    } else {
        const path = mmSelected[0];
        document.getElementById(mmInputId).value = path;

        if (mmPreviewId) {
            const img    = document.getElementById(mmPreviewId);
            const picker = document.getElementById(mmPickerId);
            img.src             = "/storage/" + path;
            img.style.display   = "block";
            if (picker) picker.classList.add("has-image");
        }
    }

    closeMediaModal();
}

/* ─────────────────────────────────────────────────────────────
   GALLERY THUMB HELPERS
───────────────────────────────────────────────────────────── */
function removeGalleryThumb(btn, path) {
    btn.closest(".gallery-thumb").remove();
    const input   = document.getElementById("gallery_images");
    const current = input.value ? input.value.split(',') : [];
    const idx     = current.indexOf(path);
    if (idx > -1) current.splice(idx, 1);
    input.value = current.join(',');
}

/* ─────────────────────────────────────────────────────────────
   CLEAR SINGLE IMAGE
───────────────────────────────────────────────────────────── */
function clearImage(inputId, previewId, pickerId) {
    document.getElementById(inputId).value = '';
    const img    = document.getElementById(previewId);
    const picker = document.getElementById(pickerId);
    img.src             = '';
    img.style.display   = 'none';
    if (picker) picker.classList.remove("has-image");
}

/* ─────────────────────────────────────────────────────────────
   FILTER / SORT
───────────────────────────────────────────────────────────── */
function filterMedia(q) { renderGrid(mmAllMedia); }

function setFilter(type, el) {
    mmCurrentFilter = type;
    document.querySelectorAll(".mm-sidebar .mm-nav-item").forEach(i => i.classList.remove("active"));
    el.classList.add("active");
    renderGrid(mmAllMedia);
}

function setSort(order, el) {
    mmCurrentSort = order;
    document.querySelectorAll(".mm-sidebar .mm-nav-item").forEach(i => i.classList.remove("active"));
    el.classList.add("active");
    renderGrid(mmAllMedia);
}

/* ─────────────────────────────────────────────────────────────
   FILE UPLOAD
───────────────────────────────────────────────────────────── */
document.getElementById("mmUploadInput").addEventListener("change", function () {
    const files = Array.from(this.files);
    if (!files.length) return;

    document.getElementById("mmGrid").insertAdjacentHTML("afterbegin",
        `<div class="mm-uploading" id="mmUploading">
            <div class="mm-spinner"></div>
            Uploading ${files.length} file${files.length > 1 ? 's' : ''}…
         </div>`);

    Promise.allSettled(files.map(file => {
        const fd = new FormData();
        fd.append("file", file);
        return fetch('/admin/media/upload', {
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body   : fd,
        });
    })).then(() => {
        document.getElementById("mmUploading")?.remove();
        loadMedia();
        this.value = '';
    });
});

/* ─────────────────────────────────────────────────────────────
   ESCAPE KEY
───────────────────────────────────────────────────────────── */
document.addEventListener("keydown", e => {
    if (e.key === "Escape") closeMediaModal();
});
</script>
@endpush
