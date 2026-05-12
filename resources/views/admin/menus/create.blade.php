@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add New Menu</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('admin.menus.index') }}">
                        <div class="text-tiny">Menus</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Add New</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <form action="{{ route('admin.menus.store') }}" method="POST">
                @csrf

                <div class="gap20 columns-2">

                    {{-- Name --}}
                    <div class="wg-box">
                        <fieldset class="name">
                            <div class="body-title mb-10">
                                Menu Name <span class="tf-color-1">*</span>
                            </div>
                            <input class="flex-grow @error('name') is-invalid @enderror"
                                   type="text" placeholder="e.g. Main Menu" name="name"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger mt-5">{{ $message }}</div>
                            @enderror
                        </fieldset>

                        <fieldset class="name">
                            <div class="body-title mb-10">
                                Slug
                                <span class="text-tiny text-muted">(auto-generated if blank)</span>
                            </div>
                            <input class="flex-grow @error('slug') is-invalid @enderror"
                                   type="text" placeholder="e.g. main-menu" name="slug"
                                   value="{{ old('slug') }}">
                            @error('slug')
                                <div class="text-danger mt-5">{{ $message }}</div>
                            @enderror
                        </fieldset>

                        {{-- Status --}}
                        <fieldset>
                            <div class="body-title mb-10">Status</div>
                            <div class="d-flex gap-3">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="is_active" value="1"
                                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    Active
                                </label>
                                <label class="d-flex align-items-center gap-2">
                                    <input type="radio" name="is_active" value="0"
                                           {{ old('is_active') == '0' ? 'checked' : '' }}>
                                    Inactive
                                </label>
                            </div>
                        </fieldset>
                    </div>

                </div>

                <div class="bot">
                    <div></div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.menus.index') }}" class="tf-button w208" style="background:#6c757d">
                            Cancel
                        </a>
                        <button class="tf-button w208" type="submit">Create Menu</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection