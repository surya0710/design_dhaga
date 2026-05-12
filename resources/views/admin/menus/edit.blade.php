@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Menu: {{ $menu->name }}</h3>
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
                    <div class="text-tiny">Edit</div>
                </li>
            </ul>
        </div>

        {{-- ─── Menu Settings ─────────────────────────────────────────────── --}}
        <div class="wg-box mb-20">
            <h5 class="mb-20">Menu Settings</h5>

            <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="gap20 columns-3">

                    <fieldset class="name">
                        <div class="body-title mb-10">Menu Name <span class="tf-color-1">*</span></div>
                        <input class="flex-grow @error('name') is-invalid @enderror"
                               type="text" name="name"
                               value="{{ old('name', $menu->name) }}" required>
                        @error('name')
                            <div class="text-danger mt-5">{{ $message }}</div>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title mb-10">Slug</div>
                        <input class="flex-grow @error('slug') is-invalid @enderror"
                               type="text" name="slug"
                               value="{{ old('slug', $menu->slug) }}">
                        @error('slug')
                            <div class="text-danger mt-5">{{ $message }}</div>
                        @enderror
                    </fieldset>

                    <fieldset>
                        <div class="body-title mb-10">Status</div>
                        <div class="d-flex gap-3 mt-5">
                            <label class="d-flex align-items-center gap-2">
                                <input type="radio" name="is_active" value="1"
                                       {{ old('is_active', $menu->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                                Active
                            </label>
                            <label class="d-flex align-items-center gap-2">
                                <input type="radio" name="is_active" value="0"
                                       {{ old('is_active', $menu->is_active ? '1' : '0') == '0' ? 'checked' : '' }}>
                                Inactive
                            </label>
                        </div>
                    </fieldset>

                </div>

                <div class="bot mt-5">
                    <button class="tf-button w208" type="submit">Update Menu</button>
                </div>

            </form>
        </div>

        {{-- ─── Menu Items ─────────────────────────────────────────────────── --}}
        <!-- <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap mb-20">
                <h5>Menu Items</h5>
                <a href="{{ route('admin.menu-items.create', $menu->id) }}" class="tf-button style-1 w208">
                    + Add Item
                </a>
            </div>

            <div class="wg-table table-all-user">
                <table class="table table-striped table-bordered" id="items-table">
                    <thead>
                        <tr>
                            <th width="40">↕</th>
                            <th>#</th>
                            <th>Label</th>
                            <th>URL / Route</th>
                            <th>Parent</th>
                            <th>Target</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-items">
                        @forelse($menu->allItems as $key => $item)
                        <tr data-id="{{ $item->id }}">
                            <td>
                                <span class="drag-handle" style="cursor:grab; font-size:18px; color:#aaa;">☰</span>
                            </td>
                            <td>{{ $key + 1 }}</td>

                            <td>
                                @if($item->parent_id)
                                    <span class="text-muted" style="padding-left:12px;">↳</span>
                                @endif
                                @if($item->icon)
                                    <i class="{{ $item->icon }}"></i>
                                @endif
                                {{ $item->label }}
                            </td>

                            <td>
                                @if($item->route_name)
                                    <code>{{ $item->route_name }}</code>
                                @elseif($item->url)
                                    <span class="text-tiny">{{ $item->url }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>{{ $item->parent?->label ?? '—' }}</td>

                            <td>
                                <span class="badge {{ $item->target === '_blank' ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $item->target }}
                                </span>
                            </td>

                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.menu-items.edit', [$menu->id, $item->id]) }}"
                                       title="Edit Item">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>

                                    <form action="{{ route('admin.menu-items.destroy', [$menu->id, $item->id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this menu item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none;border:none;padding:0;">
                                            <div class="item text-danger delete">
                                                <i class="icon-trash-2"></i>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No items yet.
                                <a href="{{ route('admin.menu-items.create', $menu->id) }}">Add your first item →</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div> -->

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const tbody = document.getElementById('sortable-items');
    if (tbody) {
        Sortable.create(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd() {
                const ids = [...tbody.querySelectorAll('tr[data-id]')].map(tr => tr.dataset.id);
                fetch('{{ route('admin.menu-items.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items: ids })
                });
            }
        });
    }
</script>
@endpush