@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" />

<style>
    .bootstrap-tagsinput {
        width: 100%;
        min-height: 42px;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
    }

    .bootstrap-tagsinput .tag {
        display: inline-block;
        margin-right: 5px;
        padding: 3px 8px;
        background: #2275fc;
        color: #fff;
        border-radius: 4px;
        font-size: 12px;
    }

    #imgpreview img {
        max-width: 100%;
        display: block;
    }

    #col-layout-modal .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }

    .col-option {
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 10px 8px;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        height: 100%;
    }

    .col-option:hover,
    .col-option.selected {
        border-color: #2275fc;
        background: #f0f5ff;
    }

    .col-preview {
        display: flex;
        gap: 4px;
        height: 38px;
        margin-bottom: 7px;
    }

    .cp-img {
        background: #bdd0f8;
        border-radius: 3px;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        color: #2275fc;
        font-weight: 600;
    }

    .cp-txt {
        background: #f3f4f6;
        border-radius: 3px;
        flex: 2;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 4px 5px;
        gap: 3px;
    }

    .cp-txt.flex-1 { flex: 1; }

    .cp-txt span {
        display: block;
        height: 3px;
        background: #d1d5db;
        border-radius: 2px;
    }

    .cp-txt span.short { width: 55%; }

    .col-option label {
        display: block;
        text-align: center;
        font-size: 11px;
        color: #6b7280;
        cursor: pointer;
        margin: 0;
    }

    .col-option.selected label {
        color: #2275fc;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add Blog</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('admin.blogs') }}">
                        <div class="text-tiny">Blogs</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Add blog</div>
                </li>
            </ul>
        </div>

        <form class="form-add-product" method="POST" enctype="multipart/form-data" action="{{ route('admin.blog.store') }}" id="blog-form">
            @csrf

            @if(Session::has('status'))
                <p class="alert alert-success">{{ Session::get('status') }}</p>
            @endif
            @if(Session::has('error'))
                <p class="alert alert-danger">{{ Session::get('error') }}</p>
            @endif

            <div class="wg-box">

                {{-- Title --}}
                <fieldset class="name">
                    <div class="body-title mb-10">Blog title <span class="tf-color-1">*</span></div>
                    <input
                        class="mb-10"
                        type="text"
                        placeholder="Enter blog title"
                        name="title"
                        id="blog-title"
                        value="{{ old('title') }}"
                        aria-required="true"
                        required
                    >
                    <div class="text-tiny">Do not exceed 100 characters when entering the blog title.</div>
                    <div class="text-danger">{{ $errors->first('title') }}</div>
                </fieldset>

                {{-- Slug --}}
                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input
                        class="mb-10"
                        type="text"
                        placeholder="Enter blog slug"
                        name="slug"
                        id="blog-slug"
                        value="{{ old('slug') }}"
                        aria-required="true"
                        required
                    >
                    <div class="text-danger">{{ $errors->first('slug') }}</div>
                </fieldset>

                {{-- Tags --}}
                <fieldset class="name">
                    <div class="body-title mb-10">Tags <span class="tf-color-1">*</span></div>
                    <input
                        type="text"
                        name="tags"
                        class="form-control w-100"
                        data-role="tagsinput"
                        value="{{ old('tags') }}"
                    >
                    <div class="text-danger">{{ $errors->first('tags') }}</div>
                </fieldset>

                {{-- Content (TinyMCE) --}}
                <fieldset class="description">
                    <div class="body-title mb-10">Content <span class="tf-color-1">*</span></div>
                    <textarea name="content" id="blog-content">{{ old('content') }}</textarea>
                    <div class="text-danger">{{ $errors->first('content') }}</div>
                </fieldset>

                {{-- Meta title --}}
                <fieldset class="name">
                    <div class="body-title mb-10">Meta title</div>
                    <input
                        class="mb-10"
                        type="text"
                        placeholder="Enter blog meta title"
                        name="meta_title"
                        value="{{ old('meta_title') }}"
                    >
                    <div class="text-danger">{{ $errors->first('meta_title') }}</div>
                </fieldset>

                {{-- Meta keywords --}}
                <fieldset class="name">
                    <div class="body-title mb-10">Meta keywords</div>
                    <input
                        class="mb-10"
                        type="text"
                        placeholder="Enter blog meta keywords"
                        name="meta_keywords"
                        value="{{ old('meta_keywords') }}"
                    >
                    <div class="text-danger">{{ $errors->first('meta_keywords') }}</div>
                </fieldset>

                {{-- Meta description --}}
                <fieldset class="name">
                    <div class="body-title mb-10">Meta description</div>
                    <textarea
                        class="mb-10"
                        placeholder="Enter blog meta description"
                        name="meta_description"
                    >{{ old('meta_description') }}</textarea>
                    <div class="text-danger">{{ $errors->first('meta_description') }}</div>
                </fieldset>

                {{-- Featured image --}}
                <fieldset>
                    <div class="body-title mb-10">Upload image <span class="tf-color-1">*</span></div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none;">
                            <img src="" class="effect8" alt="Preview">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon"><i class="icon-upload-cloud"></i></span>
                                <span class="body-text">
                                    Drop your image here or select
                                    <span class="tf-color">click to browse</span>
                                </span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                    <div class="text-danger">{{ $errors->first('image') }}</div>
                </fieldset>

                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit" id="submit-btn">Add Blog</button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Column Layout Picker Modal --}}
<div class="modal fade" id="col-layout-modal" tabindex="-1" aria-labelledby="colLayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-medium" id="colLayoutModalLabel">Insert column layout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-1">
                <p class="text-muted small mb-3">Choose how to split the section</p>
                <div class="row g-2 mb-3">

                    <div class="col-6">
                        <div class="col-option selected" data-type="img-left">
                            <div class="col-preview">
                                <div class="cp-img">IMG</div>
                                <div class="cp-txt"><span></span><span></span><span class="short"></span></div>
                            </div>
                            <label>Image left, text right</label>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="col-option" data-type="img-right">
                            <div class="col-preview">
                                <div class="cp-txt"><span></span><span></span><span class="short"></span></div>
                                <div class="cp-img">IMG</div>
                            </div>
                            <label>Text left, image right</label>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="col-option" data-type="two-text">
                            <div class="col-preview">
                                <div class="cp-txt flex-1"><span></span><span></span><span class="short"></span></div>
                                <div class="cp-txt flex-1"><span></span><span></span><span class="short"></span></div>
                            </div>
                            <label>Two equal text columns</label>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="col-option" data-type="three-col">
                            <div class="col-preview">
                                <div class="cp-txt flex-1"><span></span><span class="short"></span></div>
                                <div class="cp-txt flex-1"><span></span><span class="short"></span></div>
                                <div class="cp-txt flex-1"><span></span><span class="short"></span></div>
                            </div>
                            <label>Three equal columns</label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="col-insert-btn">Insert</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
// ── Column picker ──────────────────────────────────────────────
const colModalEl    = document.getElementById('col-layout-modal');
const colModalBS    = new bootstrap.Modal(colModalEl);
const colOptions    = document.querySelectorAll('.col-option');
let selectedColType = 'img-left';

colOptions.forEach(opt => {
    opt.addEventListener('click', function () {
        colOptions.forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
        selectedColType = this.dataset.type;
    });
});

document.getElementById('col-insert-btn').addEventListener('click', () => {
    colModalBS.hide();
    insertColumnLayout(selectedColType);
});

// ── Column HTML builder ────────────────────────────────────────
function buildColHtml(type) {
    const imgSlot  = `<img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='220'%3E%3Crect width='400' height='220' fill='%23e8effd' rx='8'/%3E%3Ctext x='50%25' y='45%25' dominant-baseline='middle' text-anchor='middle' fill='%232275fc' font-size='15' font-family='sans-serif'%3E%F0%9F%93%B7 Double-click to upload image%3C/text%3E%3Ctext x='50%25' y='62%25' dominant-baseline='middle' text-anchor='middle' fill='%236b9ef0' font-size='12' font-family='sans-serif'%3E(single click selects)%3C/text%3E%3C/svg%3E" alt="column-image" style="width:100%;height:auto;display:block;border-radius:6px;cursor:pointer;">`;
    const textSlot = `<p>Type your text here...</p>`;

    const layouts = {
        'img-left'  : `<div class="row g-3 align-items-center my-2"><div class="col-md-6">${imgSlot}</div><div class="col-md-6">${textSlot}</div></div>`,
        'img-right' : `<div class="row g-3 align-items-center my-2"><div class="col-md-6">${textSlot}</div><div class="col-md-6">${imgSlot}</div></div>`,
        'two-text'  : `<div class="row g-3 align-items-center my-2"><div class="col-md-6">${textSlot}</div><div class="col-md-6">${textSlot}</div></div>`,
        'three-col' : `<div class="row g-3 align-items-center my-2"><div class="col-md-4">${textSlot}</div><div class="col-md-4">${textSlot}</div><div class="col-md-4">${textSlot}</div></div>`,
    };
    return (layouts[type] || layouts['img-left']) + '<p>&nbsp;</p>';
}

function insertColumnLayout(type) {
    const ed = tinymce.get('blog-content');
    if (ed) ed.insertContent(buildColHtml(type));
}

// ── TinyMCE ────────────────────────────────────────────────────
tinymce.init({
    selector: '#blog-content',
    height: 500,
    menubar: false,
    branding: false,
    promotion: false,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image',
        'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'table', 'wordcount'
    ],
    toolbar:
        'undo redo | blocks | ' +
        'bold italic underline | forecolor | ' +
        'alignleft aligncenter alignright | ' +
        'bullist numlist | blockquote | ' +
        'link image table | ' +
        'columns | code | fullscreen',
    block_formats: 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6',
    extended_valid_elements: '*[*]',
    valid_children: '+body[style],+div[p|div|img|h1|h2|h3|h4|h5|h6|ul|ol|li|blockquote|table|tr|td|th|thead|tbody]',
    verify_html: false,
    automatic_uploads: false,
    file_picker_types: 'image',
    file_picker_callback: function (cb) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.addEventListener('change', function () {
            const file = this.files[0];
            const reader = new FileReader();
            reader.onload = function () {
                cb(reader.result, { title: file.name });
            };
            reader.readAsDataURL(file);
        });
        input.click();
    },
    setup: function (editor) {
        // ── Custom Columns toolbar button ───────────────────────
        editor.ui.registry.addButton('columns', {
            text: '⊞ Columns',
            tooltip: 'Insert column layout',
            onAction: function () {
                colModalBS.show();
            }
        });

        // ── Double-click placeholder → file picker ──────────────
        editor.on('dblclick', function (e) {
            const img = e.target.closest('img[alt="column-image"]');
            if (!img) return;

            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.cssText = 'position:fixed;top:-999px;left:-999px;opacity:0;';
            document.body.appendChild(fileInput);

            fileInput.addEventListener('change', function () {
                const file = fileInput.files[0];
                document.body.removeChild(fileInput);
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function (ev) {
                    editor.dom.setAttrib(img, 'src', ev.target.result);
                    editor.dom.setAttrib(img, 'alt', file.name);
                    editor.dom.setStyle(img, 'cursor', '');
                    editor.dom.setStyle(img, 'outline', '');
                };
                reader.readAsDataURL(file);
            });

            fileInput.click();
        });
    },
    content_css: [
        'https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css'
    ],
    content_style: `
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.7;
            color: #212529;
            padding: 16px;
            margin: 0;
        }
        img { max-width: 100%; height: auto; }
        p { margin-top: 0; margin-bottom: 1rem; }
        img[alt="column-image"] {
            outline: 2px dashed #2275fc;
            cursor: pointer;
        }
    `
});

// ── Misc ───────────────────────────────────────────────────────
$(function () {
    $('#myFile').on('change', function () {
        const file = this.files[0];
        if (file) {
            $('#imgpreview img').attr('src', URL.createObjectURL(file)).show();
        }
    });

    $('#blog-title').on('input', function () {
        $('#blog-slug').val(StringToSlug($(this).val()));
    });
});

function StringToSlug(text) {
    return text.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
}
</script>
@endpush
