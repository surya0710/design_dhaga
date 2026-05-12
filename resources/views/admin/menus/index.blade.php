@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Menus</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Menus</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search" method="GET">
                        <fieldset class="name">
                            <input type="text" placeholder="Search by name..." name="search"
                                value="{{ request('search') }}">
                        </fieldset>
                        <div class="button-submit">
                            <button type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>

                <a href="{{ route('admin.menus.create') }}" class="tf-button style-1 w208">
                    + Add Menu
                </a>
                <!-- <form method="POST" action="{{ route('admin.menu-items.reorder') }}">
                    @csrf
                    <button type="submit" class="tf-button style-1 w208">Reorder</button>
                </form> -->
            </div>

            <div class="wg-table table-all-user">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $key => $menu)
                        <tr>
                            <td>{{ $key + 1 }}</td>

                            <td><strong>{{ $menu->name }}</strong></td>

                            <td><code>{{ $menu->slug }}</code></td>

                            <td>
                                <span class="badge bg-info">{{ $menu->all_items_count }} items</span>
                            </td>

                            <td>
                                @if($menu->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            <td>{{ $menu->created_at->format('d M Y') }}</td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.menus.edit', $menu->id) }}" title="Edit & Manage Items">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>

                                    <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST"
                                          onsubmit="return confirm('Delete this menu and all its items?')">
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
                            <td colspan="7" class="text-center">No menus found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>

            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $menus->links() }}
            </div>
        </div>
    </div>
</div>
@endsection