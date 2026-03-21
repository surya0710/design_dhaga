@extends('layouts.admin')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" />

<style>
    #editor-toolbar {
        border: 1px solid #e5e7eb;
        border-bottom: 0;
        border-radius: 8px 8px 0 0;
        background: #fff;
    }

    #editor-container {
        min-height: 400px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0 0 8px 8px;
    }

    #editor-container .ql-editor {
        min-height: 400px;
        font-size: 14px;
        line-height: 1.7;
    }

    #html-editor {
        display: none;
        width: 100%;
        min-height: 400px;
        border: 1px solid #e5e7eb;
        border-radius: 0 0 8px 8px;
        background: #fff;
        padding: 15px;
        font-family: monospace;
        font-size: 14px;
        resize: vertical;
    }

    .editor-top-actions {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 10px;
    }

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
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.blogs') }}">
                        <div class="text-tiny">Blogs</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Add blog</div>
                </li>
            </ul>
        </div>

        <form class="form-add-product" method="POST" enctype="multipart/form-data" action="{{ route('admin.blog.store') }}">
            @csrf

            @if(Session::has('status'))
                <p class="alert alert-success">{{ Session::get('status') }}</p>
            @endif

            @if(Session::has('error'))
                <p class="alert alert-danger">{{ Session::get('error') }}</p>
            @endif

            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Blog title <span class="tf-color-1">*</span></div>
                    <input
                        class="mb-10"
                        type="text"
                        placeholder="Enter blog title"
                        name="title"
                        tabindex="0"
                        value="{{ old('title') }}"
                        aria-required="true"
                        required
                    >
                    <div class="text-tiny">Do not exceed 100 characters when entering the blog title.</div>
                    <div class="text-danger">{{ $errors->first('title') }}</div>
                </fieldset>

                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input
                        class="mb-10"
                        type="text"
                        placeholder="Enter blog slug"
                        name="slug"
                        tabindex="0"
                        value="{{ old('slug') }}"
                        aria-required="true"
                        required
                    >
                    <div class="text-danger">{{ $errors->first('slug') }}</div>
                </fieldset>

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

                <fieldset class="description">
                    <div class="body-title mb-10">Content <span class="tf-color-1">*</span></div>

                    <div class="editor-top-actions">
                        <button type="button" class="tf-button style-1" id="toggleHtmlBtn">Edit HTML</button>
                    </div>

                    <div id="editor-toolbar">
                        <span class="ql-formats">
                            <select class="ql-header">
                                <option value="" selected>Normal</option>
                                <option value="1">H1</option>
                                <option value="2">H2</option>
                                <option value="3">H3</option>
                                <option value="4">H4</option>
                                <option value="5">H5</option>
                                <option value="6">H6</option>
                            </select>
                            <select class="ql-size">
                                <option value="small"></option>
                                <option selected></option>
                                <option value="large"></option>
                                <option value="huge"></option>
                            </select>
                        </span>

                        <span class="ql-formats">
                            <button type="button" class="ql-bold"></button>
                            <button type="button" class="ql-italic"></button>
                            <button type="button" class="ql-underline"></button>
                            <button type="button" class="ql-strike"></button>
                        </span>

                        <span class="ql-formats">
                            <button type="button" class="ql-script" value="sub"></button>
                            <button type="button" class="ql-script" value="super"></button>
                            <button type="button" class="ql-blockquote"></button>
                            <button type="button" class="ql-code-block"></button>
                        </span>

                        <span class="ql-formats">
                            <button type="button" class="ql-list" value="ordered"></button>
                            <button type="button" class="ql-list" value="bullet"></button>
                            <button type="button" class="ql-indent" value="-1"></button>
                            <button type="button" class="ql-indent" value="+1"></button>
                        </span>

                        <span class="ql-formats">
                            <select class="ql-align"></select>
                            <select class="ql-color"></select>
                            <select class="ql-background"></select>
                        </span>

                        <span class="ql-formats">
                            <button type="button" class="ql-link"></button>
                            <button type="button" class="ql-image"></button>
                        </span>

                        <span class="ql-formats">
                            <button type="button" class="ql-clean"></button>
                        </span>
                    </div>

                    <div id="editor-container"></div>
                    <textarea id="html-editor"></textarea>
                    <input type="hidden" name="content" id="content" value="{{ old('content') }}">

                    <div class="text-danger">{{ $errors->first('content') }}</div>
                </fieldset>

                <fieldset class="name">
                    <div class="body-title mb-10">Meta title</div>
                    <input
                        class="mb-10"
                        type="text"
                        placeholder="Enter blog meta title"
                        name="meta_title"
                        tabindex="0"
                        value="{{ old('meta_title') }}"
                    >
                    <div class="text-danger">{{ $errors->first('meta_title') }}</div>
                </fieldset>

                <fieldset class="name">
                    <div class="body-title mb-10">Meta keywords</div>
                    <input
                        class="mb-10"
                        type="text"
                        placeholder="Enter blog meta keywords"
                        name="meta_keywords"
                        tabindex="0"
                        value="{{ old('meta_keywords') }}"
                    >
                    <div class="text-danger">{{ $errors->first('meta_keywords') }}</div>
                </fieldset>

                <fieldset class="name">
                    <div class="body-title mb-10">Meta description</div>
                    <textarea
                        class="mb-10"
                        placeholder="Enter blog meta description"
                        name="meta_description"
                        tabindex="0"
                    >{{ old('meta_description') }}</textarea>
                    <div class="text-danger">{{ $errors->first('meta_description') }}</div>
                </fieldset>

                <fieldset>
                    <div class="body-title mb-10">Upload image <span class="tf-color-1">*</span></div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none;">
                            <img src="" class="effect8" alt="Preview">
                        </div>

                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
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
                    <button class="tf-button w-full" type="submit">Add Blog</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

<script>
    const quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: {
                container: '#editor-toolbar',
                handlers: {
                    image: imageHandler
                }
            }
        }
    });

    const oldContent = document.getElementById('content').value;
    if (oldContent) {
        quill.root.innerHTML = oldContent;
    }

    function imageHandler() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = function () {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (e) {
                const range = quill.getSelection(true) || {
                    index: quill.getLength(),
                    length: 0
                };

                quill.insertEmbed(range.index, 'image', e.target.result);
                quill.setSelection(range.index + 1);
            };

            reader.readAsDataURL(file);
        };
    }

    const toggleHtmlBtn = document.getElementById('toggleHtmlBtn');
    const htmlEditor = document.getElementById('html-editor');
    const editorContainer = document.getElementById('editor-container');
    const editorToolbar = document.getElementById('editor-toolbar');
    let htmlMode = false;

    toggleHtmlBtn.addEventListener('click', function () {
        if (!htmlMode) {
            htmlEditor.value = quill.root.innerHTML;
            htmlEditor.style.display = 'block';
            editorContainer.style.display = 'none';
            editorToolbar.style.display = 'none';
            toggleHtmlBtn.textContent = 'Back to Editor';
            htmlMode = true;
        } else {
            quill.root.innerHTML = htmlEditor.value;
            htmlEditor.style.display = 'none';
            editorContainer.style.display = 'block';
            editorToolbar.style.display = 'block';
            toggleHtmlBtn.textContent = 'Edit HTML';
            htmlMode = false;
        }
    });

    document.querySelector('.form-add-product').addEventListener('submit', function () {
        document.querySelector('#content').value = htmlMode ? htmlEditor.value : quill.root.innerHTML;
    });

    $(function () {
        $('#myFile').on('change', function () {
            const file = this.files[0];
            if (file) {
                $('#imgpreview img').attr('src', URL.createObjectURL(file));
                $('#imgpreview').show();
            }
        });

        $("input[name='title']").on('change keyup', function () {
            $("input[name='slug']").val(StringToSlug($(this).val()));
        });
    });

    function StringToSlug(text) {
        return text.toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    }
</script>
@endpush