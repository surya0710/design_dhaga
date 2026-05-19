@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center justify-between flex-wrap gap20 mb-27">
            <h3>Highlights</h3>
        </div>

        {{-- Success Message --}}
        @if(Session::has('status'))
            <div class="alert alert-success mb-20">
                {{ Session::get('status') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-20">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===================== ADD FORM ===================== --}}
        <div class="wg-box mb-30">

            <h5 class="mb-20">Add Highlight</h5>

            <form class="form-new-product form-style-1"
                  action="{{ route('admin.highlights.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <fieldset>
                    <div class="body-title">Title <span class="tf-color-1">*</span></div>
                    <input type="text" name="title" placeholder="Enter title" value="{{ old('title') }}" required>
                </fieldset>

                <fieldset>
                    <div class="body-title">Alt Text</div>
                    <input type="text" name="alt_text" placeholder="Enter alt text" value="{{ old('alt_text') }}">
                </fieldset>

                <fieldset>
                    <div class="body-title">Image <span class="tf-color-1">*</span></div>
                    <div class="upload-image flex-grow">

                        <div class="item" id="add-imgpreview" style="display:none">
                            <img id="add-previewImg" src="" class="effect8">
                        </div>

                        <div class="item up-load">
                            <label class="uploadfile" for="addFile">
                                <span class="icon"><i class="icon-upload-cloud"></i></span>
                                <span class="body-text">
                                    Drop your image or <span class="tf-color">click to browse</span>
                                </span>
                                <input type="file" id="addFile" name="emoji" accept="image/*">
                            </label>
                        </div>

                    </div>
                </fieldset>

                <div class="flex gap20">
                    <fieldset class="flex-grow">
                        <div class="body-title">Sort Order</div>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                    </fieldset>

                    <fieldset class="flex-grow">
                        <div class="body-title">Status</div>
                        <div class="select">
                            <select name="status">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </fieldset>
                </div>

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Add Highlight</button>
                </div>

            </form>

        </div>

        {{-- ===================== TABLE ===================== --}}
        <div class="wg-box">

            <div class="flex items-center justify-between gap10 mb-20">
                <h5>All Highlights</h5>
            </div>

            <div class="table-responsive">

                @if($highlights->count())

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Alt Text</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($highlights as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        <img src="{{ Storage::url($item->emoji) }}"
                                             width="60" height="60"
                                             style="object-fit:contain; border:1px solid #eee; border-radius:6px; padding:4px; background:#fff;">
                                    </td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->alt_text ?? '—' }}</td>
                                    <td>{{ $item->sort_order }}</td>
                                    <td>
                                        @if($item->status)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="list-icon-function">

                                            {{-- Edit --}}
                                            <button type="button"
                                                    class="item edit btn-edit"
                                                    data-id="{{ $item->id }}"
                                                    data-title="{{ $item->title }}"
                                                    data-alt_text="{{ $item->alt_text }}"
                                                    data-sort_order="{{ $item->sort_order }}"
                                                    data-status="{{ $item->status }}"
                                                    data-image="{{ Storage::url($item->emoji) }}"
                                                    data-url="{{ route('admin.highlights.edit', $item->id) }}"
                                                    style="background:none; border:none; cursor:pointer; padding:0;">
                                                <i class="icon-edit-3"></i>
                                            </button>

                                            {{-- Delete --}}
                                            <form action="{{ route('admin.highlights.destroy', $item->id) }}"
                                                  method="POST"
                                                  class="item trash"
                                                  onsubmit="return confirm('Delete this highlight?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background:none; border:none; padding:0; cursor:pointer;">
                                                    <i class="icon-trash-2"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($highlights->hasPages())
                        <div class="divider"></div>
                        <div class="flex items-center justify-between flex-wrap gap10">
                            <div class="wg-pagination">
                                {{ $highlights->links() }}
                            </div>
                        </div>
                    @endif

                @else
                    <div class="body-text text-center py-20">No highlights found.</div>
                @endif

            </div>

        </div>

    </div>
</div>

{{-- ===================== EDIT MODAL ===================== --}}
<div id="editModal" style="
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
">
    <div style="
        background: #fff;
        border-radius: 12px;
        width: 100%;
        max-width: 580px;
        margin: 20px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    ">

        {{-- Modal Header --}}
        <div style="
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
        ">
            <h5 style="margin:0; font-weight:600;">Edit Highlight</h5>
            <button id="closeModal" type="button" style="
                background: none;
                border: none;
                cursor: pointer;
                font-size: 20px;
                color: #888;
                line-height: 1;
                padding: 0;
            ">&times;</button>
        </div>

        {{-- Modal Body --}}
        <div style="padding: 24px;">

            <form class="form-new-product form-style-1"
                  id="edit-form"
                  action=""
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <fieldset>
                    <div class="body-title">Title <span class="tf-color-1">*</span></div>
                    <input type="text" id="edit-title" name="title" placeholder="Enter title" required>
                </fieldset>

                <fieldset>
                    <div class="body-title">Alt Text</div>
                    <input type="text" id="edit-alt_text" name="alt_text" placeholder="Enter alt text">
                </fieldset>

                <fieldset>
                    <div class="body-title">Image <br>
                        <span class="tf-color-1" style="font-weight:400; font-size:12px;">(leave empty to keep current)</span>
                    </div>
                    <div class="upload-image flex-grow">

                        <div class="item" id="edit-imgpreview" style="display:none;">
                            <img id="edit-previewImg" src="" class="effect8">
                        </div>

                        <div class="item up-load">
                            <label class="uploadfile" for="editFile">
                                <span class="icon"><i class="icon-upload-cloud"></i></span>
                                <span class="body-text">
                                    Drop your image or <span class="tf-color">click to browse</span>
                                </span>
                                <input type="file" id="editFile" name="emoji" accept="image/*">
                            </label>
                        </div>

                    </div>
                </fieldset>

                <div class="flex gap20">
                    <fieldset class="flex-grow">
                        <div class="body-title">Sort Order</div>
                        <input type="number" id="edit-sort_order" name="sort_order" min="0">
                    </fieldset>

                    <fieldset class="flex-grow">
                        <div class="body-title">Status</div>
                        <div class="select">
                            <select id="edit-status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </fieldset>
                </div>

                <div class="bot" style="margin-top: 8px;">
                    <button type="button" id="cancelModal" class="tf-button" style="background:#f5f5f5; color:#333;">
                        Cancel
                    </button>
                    <button class="tf-button w208" type="submit">Save Changes</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>

    $(function () {

        // ── Add form image preview ──
        $('#addFile').on('change', function () {
            const file = this.files[0];
            if (file) {
                $('#add-previewImg').attr('src', URL.createObjectURL(file));
                $('#add-imgpreview').show();
            }
        });

        // ── Edit form image preview ──
        $('#editFile').on('change', function () {
            const file = this.files[0];
            if (file) {
                $('#edit-previewImg').attr('src', URL.createObjectURL(file));
                $('#edit-imgpreview').show();
            }
        });

        // ── Open modal ──
        $(document).on('click', '.btn-edit', function () {

            const title      = $(this).data('title');
            const alt_text   = $(this).data('alt_text');
            const sort_order = $(this).data('sort_order');
            const status     = $(this).data('status');
            const image      = $(this).data('image');
            const url        = $(this).data('url');

            // Populate fields
            $('#edit-title').val(title);
            $('#edit-alt_text').val(alt_text);
            $('#edit-sort_order').val(sort_order);
            $('#edit-status').val(status);

            // Show current image preview
            $('#edit-previewImg').attr('src', image);
            $('#edit-imgpreview').show();

            // Reset file input
            $('#editFile').val('');

            // Set form action
            $('#edit-form').attr('action', url);

            // Show modal
            $('#editModal').css('display', 'flex');
            $('body').css('overflow', 'hidden');

        });

        // ── Close modal helpers ──
        function closeModal() {
            $('#editModal').hide();
            $('body').css('overflow', '');
            $('#editFile').val('');
            $('#edit-imgpreview').hide();
        }

        $('#closeModal, #cancelModal').on('click', closeModal);

        // Close on backdrop click
        $('#editModal').on('click', function (e) {
            if ($(e.target).is('#editModal')) {
                closeModal();
            }
        });

        // Close on Escape key
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

    });

</script>
@endpush