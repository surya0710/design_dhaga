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
    <h3>Add Product</h3>
    <ul class="breadcrumbs">
        <li><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="sep">›</li>
        <li><a href="{{ route('admin.products') }}">Products</a></li>
        <li class="sep">›</li>
        <li>Add Product</li>
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


<form method="POST" enctype="multipart/form-data" action="{{ route('admin.product.store') }}">
@csrf

<div class="form-grid">


    {{-- ═══════════ LEFT COLUMN ═══════════ --}}
    <div class="form-left">


        {{-- BASIC INFO --}}
        <div class="card">
            <div class="card-title">Basic Information</div>

            <div class="field">
                <label>Product Name <span class="req">*</span></label>
                <input type="text" name="name" id="productName" value="{{ old('name') }}" placeholder="e.g. Hand-Painted Ceramic Vase" required>
            </div>

            <div class="divider"></div>

            <div class="field slug-field">
                <label>Slug <span class="req">*</span></label>
                <input type="text" name="slug" id="slugField" value="{{ old('slug') }}" placeholder="auto-generated-slug" required>
            </div>

            <div class="divider"></div>

            <div class="cols-2">
                <div class="field">
                    <label>Purchase Type</label>
                    <select name="purchase_type">
                        <option value="1" {{ old('purchase_type')=='1'?'selected':'' }}>Add To Cart</option>
                        <option value="2" {{ old('purchase_type')=='2'?'selected':'' }}>Request To Purchase</option>
                    </select>
                </div>

                <div class="field">
                    <label>Category <span class="req">*</span></label>
                    <select name="category_id">
                        <option value="">Choose Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id')==$category->id?'selected':'' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="field">
                <label>Sub Title <span class="req">*</span></label>
                <input type="text" name="short_description" id="" value="{{ old('short_description') }}" placeholder="Discover exquisite Kantha hand painted dupattas on our store with tassles." required>
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Description <span class="req">*</span></label>
                <textarea name="description" class="editor" placeholder="Describe the product…">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- EXTRA DETAILS --}}
        <div class="card">
            <div class="card-title">Product Details</div>

            <div class="field">
                <label>Hand Painted Details</label>
                <textarea name="hand_painted_details" class="ht-sm editor" placeholder="Describe the hand-painted elements…">{{ old('hand_painted_details') }}</textarea>
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Care Instructions</label>
                <textarea name="care_instructions" class="ht-sm editor" placeholder="How to care for this product…">{{ old('care_instructions') }}</textarea>
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Manufacturing Details</label>
                <textarea name="manufacturing_details" class="ht-sm editor" placeholder="Where and how it's made…">{{ old('manufacturing_details') }}</textarea>
            </div>
        </div>


        {{-- PRODUCT IMAGES --}}
        <div class="card">
            <div class="card-title">Product Images</div>

            {{-- MAIN IMAGE --}}
            <div class="field">
                <label>Main Image</label>
                <input type="hidden" name="image" id="product_image" value="{{ old('image') }}">

                <div class="media-picker" id="picker_product_image"
                     onclick="openMediaUploader('product_image','preview_product_image')">
                    <div class="pick-icon">🖼</div>
                    <div class="pick-label">Click to choose main product image</div>
                    <span class="pick-btn">Browse Media</span>
                    <img id="preview_product_image" style="display:none">
                    <div class="overlay">
                        <button type="button" onclick="event.stopPropagation(); openMediaUploader('product_image','preview_product_image')">Change</button>
                        <button type="button" class="del-btn" onclick="event.stopPropagation(); clearImage('product_image','preview_product_image','picker_product_image')">Remove</button>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- GALLERY --}}
            <div class="field">
                <label>Gallery Images</label>
                <input type="hidden" name="gallery" id="gallery_images" value="{{ old('gallery') }}">

                <div id="gallery_preview" class="gallery-grid">
                    <button type="button" class="gallery-add-btn" onclick="openMediaUploaderMultiple('gallery_images','gallery_preview')">+</button>
                </div>
            </div>
        </div>


        {{-- ARTISAN GALLERY --}}
        <div class="card">
            <div class="card-title">Artisan Gallery</div>

            <div class="field">
                <label>Artisan Heading</label>
                <input type="text" name="artisan_heading" class="ht-sm" placeholder="Section Heading" value="{{ old('artisan_heading') }}">
            </div>

            <div class="divider"></div>

            <div class="artisan-grid">
                @for($i = 1; $i <= 3; $i++)
                <div class="artisan-slot">
                    <div class="slot-num">Slot {{ $i }}</div>

                    <input type="hidden" name="artisan_gallery[{{ $i }}][image]" id="artisan_image_{{ $i }}">

                    <div class="media-picker" id="picker_artisan_image_{{ $i }}" style="min-height:100px" onclick="openMediaUploader('artisan_image_{{ $i }}','preview_artisan_image_{{ $i }}')">
                        <div class="pick-icon" style="font-size:20px">🖼</div>
                        <span class="pick-btn" style="font-size:11px;padding:5px 10px">Select</span>
                        <img id="preview_artisan_image_{{ $i }}" style="display:none">
                        <div class="overlay">
                            <button type="button" onclick="event.stopPropagation(); openMediaUploader('artisan_image_{{ $i }}','preview_artisan_image_{{ $i }}')">Change</button>
                            <button type="button" class="del-btn" onclick="event.stopPropagation(); clearImage('artisan_image_{{ $i }}','preview_artisan_image_{{ $i }}','picker_artisan_image_{{ $i }}')">✕</button>
                        </div>
                    </div>

                    <input type="text" name="artisan_gallery[{{ $i }}][title]" placeholder="Title / Heading" value="{{ old('artisan_gallery.'.$i.'.title') }}">

                    <textarea name="artisan_gallery[{{ $i }}][description]" placeholder="Short description…">{{ old('artisan_gallery.'.$i.'.description') }}</textarea>
                </div>
                @endfor
            </div>
        </div>

        {{-- Square Banner --}}
        <div class="card">
            <div class="card-title">Square Banner</div>

            <div class="artisan-slot">

                <input type="hidden" name="square_banner" id="square_banner">

                <div class="media-picker" id="picker_square_banner" style="min-height:100px" onclick="openMediaUploader('square_banner', 'preview_square_banner')">
                    <div class="pick-icon" style="font-size:20px">🖼</div>
                    <span class="pick-btn" style="font-size:11px;padding:5px 10px">Select</span>
                    <img id="preview_square_banner" style="display:none">
                    <div class="overlay">
                        <button type="button" onclick="event.stopPropagation(); openMediaUploader('square_banner', 'preview_square_banner')">Change</button>
                        <button type="button" class="del-btn" onclick="event.stopPropagation(); clearImage('square_banner','preview_square_banner','picker_square_banner')">✕</button>
                    </div>
                </div>

                <input type="text" name="square_banner_title" placeholder="Title / Heading" value="{{ old('square_banner.'.$i.'.title') }}">

                <textarea name="square_banner_description" placeholder="Short description…">{{ old('square_banner.'.$i.'.description') }}</textarea>
            </div>
        </div>


        {{-- PRODUCT ATTRIBUTES --}}
        <div class="card">
            <div class="card-title">Attributes</div>

            <div id="attribute-wrapper" style="display:flex;flex-direction:column;gap:10px">

                <div class="attr-row">
                    <input type="text" name="attributes[key][]" placeholder="e.g. Material">
                    <input type="text" name="attributes[value][]" placeholder="e.g. Ceramic">
                    <button type="button" class="attr-remove remove-attribute" title="Remove">✕</button>
                </div>

            </div>

            <div class="divider"></div>

            <button type="button" id="add-attribute" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Add Attribute
            </button>
        </div>

        {{--  Meta Details --}}
        <div class="card">
            <div class="card-title">Meta Details</div>

            <div class="field">
                <label for="meta_title">Meta Title</label>
                <input type="text" name="meta_title" placeholder="Meta Title" value="{{ old('meta_title') }}">
            </div>

            <div class="field">
                <label for="meta_description">Meta Description</label>
                <textarea name="meta_description" placeholder="Meta Description">{{ old('meta_description') }}</textarea>
            </div>
            
            <div class="field">
                <label for="meta_keywords">Meta Keywords</label>
                <input type="text" name="meta_keywords" placeholder="Meta Keywords" value="{{ old('meta_keywords') }}">
            </div>
        </div>

        {{-- Upload Icons --}}
        <div class="card">
            <div class="card-title">Upload Product Icons</div>

            <div class="artisan-grid">
                @for($i = 1; $i <= 6; $i++)
                <div class="image-picker"
                    id="picker_productIcons_{{ $i }}"
                    onclick="openMediaUploader('productIcons_{{ $i }}','preview_productIcons_{{ $i }}','picker_productIcons_{{ $i }}')">

                    {{-- Hidden image --}}
                    <input type="hidden" name="product_icons[{{ $i }}][image]" id="productIcons_{{ $i }}">

                    <div class="pick-icon">🖼</div>
                    <span class="pick-btn">Select</span>

                    <img id="preview_productIcons_{{ $i }}" style="display:none">

                    <div class="overlay">
                        <button type="button"
                            onclick="event.stopPropagation(); openMediaUploader('productIcons_{{ $i }}','preview_productIcons_{{ $i }}','picker_productIcons_{{ $i }}')">
                            Change
                        </button>

                        <button type="button" class="del-btn"
                            onclick="event.stopPropagation(); clearImage('productIcons_{{ $i }}','preview_productIcons_{{ $i }}','picker_productIcons_{{ $i }}')">
                            ✕
                        </button>
                    </div>
                </div>

                {{-- Text --}}
                <input type="text" name="product_icons[{{ $i }}][text]" placeholder="Text" value="{{ old('product_icons.'.$i.'.text') }}">
                @endfor
            </div>
        </div>
    </div>
    {{-- END LEFT --}}


    {{-- ═══════════ RIGHT COLUMN ═══════════ --}}
    <div class="form-right">


        {{-- PUBLISH --}}
        <div class="card">
            <div class="card-title">Publish</div>

            <button class="btn btn-primary btn-full" type="submit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                Publish Product
            </button>

            <div class="divider"></div>

            <div class="field">
                <label>Stock Status</label>
                <div class="stock-toggle">
                    <input type="radio" name="stock_status" id="in_stock" value="1" {{ old('stock_status','1')=='1'?'checked':'' }}>
                    <label for="in_stock">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path d="M20 6L9 17l-5-5"/></svg>
                        In Stock
                    </label>
                    <input type="radio" name="stock_status" id="out_stock" value="0" {{ old('stock_status')=='0'?'checked':'' }}>
                    <label for="out_stock">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        Out of Stock
                    </label>
                </div>
            </div>
        </div>


        {{-- PRICING --}}
        <div class="card">
            <div class="card-title">Pricing</div>

            <div class="field">
                <label>Regular Price <span class="req">*</span></label>
                <div class="price-prefix">
                    <span>₹</span>
                    <input type="number" name="regular_price" step="1" value="{{ old('regular_price') }}" placeholder="0.00" required>
                </div>
                @error('regular_price')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Sale Price</label>
                <div class="price-prefix">
                    <span>₹</span>
                    <input type="number" name="sale_price" step="1" value="{{ old('sale_price') }}" placeholder="0.00">
                </div>
            </div>
        </div>


        {{-- INVENTORY --}}
        <div class="card">
            <div class="card-title">Inventory</div>

            <div class="field">
                <label>SKU</label>
                <input type="text" name="sku" value="{{ old('sku') }}" placeholder="e.g. ART-001">
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Quantity</label>
                <input type="number" name="quantity" min="0" value="{{ old('quantity') }}" placeholder="0">
            </div>
        </div>


        {{-- SHIPPING --}}
        <div class="card">
            <div class="card-title">Shipping</div>

            <div class="field">
                <label>Weight</label>
                <input type="text" name="weight" value="{{ old('weight') }}" placeholder="e.g. 500g">
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Dimensions</label>
                <input type="text" name="dimension" value="{{ old('dimension') }}" placeholder="e.g. 10×10×20 cm">
            </div>
        </div>


        {{-- META --}}
        <div class="card">
            <div class="card-title">Meta</div>

            <div class="field">
                <label>Color</label>
                <input type="text" name="color" value="{{ old('color') }}" placeholder="e.g. Cobalt Blue">
            </div>

            <div class="divider"></div>

            <div class="field">
                <label>Tags</label>
                <input type="text" name="tags" value="{{ old('tags') }}" placeholder="handmade, ceramic, artisan">
            </div>
        </div>

    </div>
    {{-- END RIGHT --}}


</div>
{{-- /form-grid --}}

</form>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{--  MEDIA MANAGER MODAL                                           --}}
{{-- ══════════════════════════════════════════════════════════════ --}}

<div class="mm-overlay" id="mediaModal" onclick="handleOverlayClick(event)">
    <div class="mm-shell" onclick="event.stopPropagation()">

        {{-- HEADER --}}
        <div class="mm-header">
            <h4>Media Library</h4>
            <div class="mm-header-actions">
                <input type="text" class="mm-search" id="mmSearch" placeholder="Search media…" oninput="filterMedia(this.value)">
                <button class="mm-close" onclick="closeMediaModal()" title="Close">✕</button>
            </div>
        </div>

        {{-- BODY --}}
        <div class="mm-body">

            {{-- SIDEBAR --}}
            <div class="mm-sidebar">
                <div class="mm-sidebar-label">Library</div>
                <div class="mm-nav-item active" onclick="setFilter('all', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    All Files
                </div>
                <div class="mm-nav-item" onclick="setFilter('image', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    Images
                </div>
                <div class="mm-sidebar-label">Sort</div>
                <div class="mm-nav-item active-sort" onclick="setSort('newest', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M7 12h10M11 18h2"/></svg>
                    Newest First
                </div>
                <div class="mm-nav-item" onclick="setSort('oldest', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18h18M7 12h10M11 6h2"/></svg>
                    Oldest First
                </div>
            </div>

            {{-- MAIN --}}
            <div class="mm-main">

                <div class="mm-toolbar">
                    <span class="mm-count" id="mmCount">0 items</span>
                    <label class="mm-upload-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                        Upload File
                        <input type="file" id="mmUploadInput" accept="image/*" multiple>
                    </label>
                </div>

                <div class="mm-grid" id="mmGrid">
                    {{-- populated by JS --}}
                </div>

            </div>

        </div>

        {{-- FOOTER --}}
        <div class="mm-footer">
            <div class="mm-selected-info" id="mmSelectedInfo">No file selected</div>
            <button class="mm-confirm" id="mmConfirmBtn" onclick="confirmSelection()">
                Insert Selected
            </button>
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
let mmInputId      = null;
let mmPreviewId    = null;
let mmMultiple     = false;
let mmSelected     = [];   // paths chosen in current session
let mmAllMedia     = [];
let mmCurrentSort  = 'newest';
let mmCurrentFilter= 'all';

$("#productName").on("change", function () {
    const slug = this.value.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
    $("#slugField").val(slug);
});

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

function openMediaUploader(inputId, previewId = null) {
    mmInputId   = inputId;
    mmPreviewId = previewId;
    mmMultiple  = false;
    mmSelected  = [];
    document.getElementById("mediaModal").classList.add("open");
    loadMedia();
}

function openMediaUploaderMultiple(inputId, previewId) {
    mmInputId   = inputId;
    mmPreviewId = previewId;
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

function loadMedia() {
    const grid = document.getElementById("mmGrid");
    grid.innerHTML = `<div class="mm-uploading"><div class="mm-spinner"></div> Loading media…</div>`;

    fetch('/admin/media')
        .then(r => r.json())
        .then(data => {
            mmAllMedia = data;
            renderGrid(data);
        })
        .catch(() => {
            grid.innerHTML = `<div class="mm-empty"><p>Could not load media.</p></div>`;
        });
}

function renderGrid(data) {
    const grid = document.getElementById("mmGrid");
    let items  = [...data];

    // sort
    if (mmCurrentSort === 'oldest') items.reverse();

    // filter
    const q = document.getElementById("mmSearch").value.toLowerCase();
    if (q) items = items.filter(i => i.file_path.toLowerCase().includes(q));

    document.getElementById("mmCount").textContent = `${items.length} item${items.length!==1?'s':''}`;

    if (!items.length) {
        grid.innerHTML = `
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

    grid.innerHTML = items.map(item => {
        const sel = mmSelected.includes(item.file_path) ? 'selected' : '';
        const name = item.file_path.split('/').pop();
        return `
            <div class="mm-item ${sel}" data-path="${item.file_path}" onclick="toggleItem(this, '${item.file_path}')">
                <img src="/storage/${item.file_path}" alt="${name}" loading="lazy">
                <div class="mm-item-name">${name}</div>
            </div>
        `;
    }).join('');
}

function toggleItem(el, path) {
    if (mmMultiple) {
        const idx = mmSelected.indexOf(path);
        if (idx > -1) {
            mmSelected.splice(idx, 1);
            el.classList.remove("selected");
        } else {
            mmSelected.push(path);
            el.classList.add("selected");
        }
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
    const info= document.getElementById("mmSelectedInfo");

    if (n === 0) {
        info.innerHTML = "No file selected";
        btn.classList.remove("ready");
    } else {
        info.innerHTML = `<strong>${n}</strong> file${n>1?'s':''} selected`;
        btn.classList.add("ready");
    }
}

function confirmSelection() {
    if (!mmSelected.length) return;

    if (mmMultiple) {
        const input   = document.getElementById(mmInputId);
        const current = input.value ? input.value.split(',') : [];
        mmSelected.forEach(path => {
            if (!current.includes(path)) current.push(path);
        });
        input.value = current.join(',');

        // rebuild gallery preview
        const preview = document.getElementById(mmPreviewId);
        preview.innerHTML = '';
        current.forEach(path => addGalleryThumb(preview, path));
        // re-add "+" button
        const addBtn = document.createElement("button");
        addBtn.type      = "button";
        addBtn.className = "gallery-add-btn";
        addBtn.textContent = "+";
        addBtn.onclick   = () => openMediaUploaderMultiple(mmInputId, mmPreviewId);
        preview.appendChild(addBtn);

    } else {
        const path = mmSelected[0];
        document.getElementById(mmInputId).value = path;

        if (mmPreviewId) {
            const img    = document.getElementById(mmPreviewId);
            const picker = img.closest(".media-picker");
            img.src      = "/storage/" + path;
            img.style.display = "block";
            picker.classList.add("has-image");
        }
    }

    closeMediaModal();
}

function addGalleryThumb(container, path) {
    const div = document.createElement("div");
    div.className = "gallery-thumb";
    div.innerHTML = `
        <img src="/storage/${path}" alt="">
        <button type="button" class="remove-thumb" title="Remove" onclick="removeGalleryThumb(this,'${path}')">✕</button>
    `;
    container.appendChild(div);
}

function removeGalleryThumb(btn, path) {
    btn.closest(".gallery-thumb").remove();
    const input  = document.getElementById(mmInputId || "gallery_images");
    const current= input.value ? input.value.split(',') : [];
    const idx    = current.indexOf(path);
    if (idx > -1) current.splice(idx, 1);
    input.value  = current.join(',');
}

function clearImage(inputId, previewId, pickerId) {
    document.getElementById(inputId).value = '';
    const img = document.getElementById(previewId);
    img.src   = '';
    img.style.display = 'none';
    document.getElementById(pickerId).classList.remove("has-image");
}

function filterMedia(q) { renderGrid(mmAllMedia); }

function setFilter(type, el) {
    mmCurrentFilter = type;
    document.querySelectorAll(".mm-sidebar .mm-nav-item").forEach(i => i.classList.remove("active"));
    el.classList.add("active");
    renderGrid(mmAllMedia);
}

function setSort(order, el) {
    mmCurrentSort = order;
    renderGrid(mmAllMedia);
}

document.getElementById("mmUploadInput").addEventListener("change", function () {
    const files   = Array.from(this.files);
    if (!files.length) return;

    const grid = document.getElementById("mmGrid");
    grid.insertAdjacentHTML("afterbegin",
        `<div class="mm-uploading" id="mmUploading"><div class="mm-spinner"></div> Uploading ${files.length} file${files.length>1?'s':''}…</div>`);

    const uploads = files.map(file => {
        const fd = new FormData();
        fd.append("file", file);
        return fetch('/admin/media/upload', {
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body   : fd,
        });
    });

    Promise.allSettled(uploads).then(() => {
        document.getElementById("mmUploading")?.remove();
        loadMedia();
        this.value = '';
    });
});

document.addEventListener("keydown", e => {
    if (e.key === "Escape") closeMediaModal();
});
</script>
@endpush