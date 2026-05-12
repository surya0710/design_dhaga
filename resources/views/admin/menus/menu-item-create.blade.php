@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add Menu Item</h3>
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
                    <a href="{{ route('admin.menus.edit', $menu->id) }}">
                        <div class="text-tiny">{{ $menu->name }}</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Add Item</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <form action="{{ route('admin.menu-items.store', $menu->id) }}" method="POST">
                @csrf

                <div class="gap20 columns-2">

                    {{-- Left Column --}}
                    <div class="wg-box">
                        <h5 class="mb-20">Item Details</h5>

                        <fieldset class="name">
                            <div class="body-title mb-10">Label <span class="tf-color-1">*</span></div>
                            <input class="flex-grow @error('label') is-invalid @enderror"
                                   type="text" placeholder="e.g. Home, About Us"
                                   name="label" value="{{ old('label') }}" required>
                            @error('label')
                                <div class="text-danger mt-5">{{ $message }}</div>
                            @enderror
                        </fieldset>

                        <fieldset class="name">
                            <div class="body-title mb-10">
                                URL
                                <span class="text-tiny text-muted">(static link)</span>
                            </div>
                            <input class="flex-grow @error('url') is-invalid @enderror"
                                   type="text" placeholder="https://example.com or /about"
                                   name="url" value="{{ old('url') }}">
                            @error('url')
                                <div class="text-danger mt-5">{{ $message }}</div>
                            @enderror
                        </fieldset>

                        <fieldset class="name">
                            <div class="body-title mb-10">
                                Route Name
                                <span class="text-tiny text-muted">(takes priority over URL)</span>
                            </div>
                            <input class="flex-grow @error('route_name') is-invalid @enderror"
                                   type="text" placeholder="e.g. about-us, contact-us"
                                   name="route_name" value="{{ old('route_name') }}">
                            @error('route_name')
                                <div class="text-danger mt-5">{{ $message }}</div>
                            @enderror
                        </fieldset>

                        <fieldset class="name">
                            <div class="body-title mb-10">
                                Route Params
                                <span class="text-tiny text-muted">(JSON format, optional)</span>
                            </div>
                            <input class="flex-grow @error('route_params') is-invalid @enderror"
                                   type="text" placeholder='{"id": 5}'
                                   name="route_params" value="{{ old('route_params') }}">
                            @error('route_params')
                                <div class="text-danger mt-5">{{ $message }}</div>
                            @enderror
                        </fieldset>
                    </div>

                    {{-- Right Column --}}
                    <div class="wg-box">
                        <h5 class="mb-20">Options</h5>

                        <fieldset class="name">
                            <div class="body-title mb-10">Icon Class</div>
                            <input class="flex-grow"
                                   type="text" placeholder="e.g. icon-home, fas fa-home"
                                   name="icon" value="{{ old('icon') }}">
                        </fieldset>

                        <fieldset>
                            <div class="body-title mb-10">Open In</div>
                            <div class="select">
                                <select name="target" class="flex-grow">
                                    <option value="_self"  {{ old('target', '_self') === '_self'  ? 'selected' : '' }}>Same Tab (_self)</option>
                                    <option value="_blank" {{ old('target') === '_blank' ? 'selected' : '' }}>New Tab (_blank)</option>
                                </select>
                            </div>
                        </fieldset>

                        <fieldset>
                            <div class="body-title mb-10">Parent Item</div>
                            <div class="select">
                                <select name="parent_id" class="flex-grow">
                                    <option value="">— None (top level) —</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}"
                                                {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>

                        <fieldset class="name">
                            <div class="body-title mb-10">Order</div>
                            <input class="flex-grow"
                                   type="number" name="order"
                                   value="{{ old('order', 0) }}" min="0">
                        </fieldset>

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
                        <a href="{{ route('admin.menus.edit', $menu->id) }}"
                           class="tf-button w208" style="background:#6c757d">
                            Cancel
                        </a>
                        <button class="tf-button w208" type="submit">Add Item</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection