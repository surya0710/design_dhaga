<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::withCount('allItems')->latest()->paginate(15);
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'slug'      => 'nullable|string|max:100|unique:menus,slug',
            'is_active' => 'boolean',
        ]);

        $data['slug']      = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        Menu::create($data);

        return redirect()->route('admin.menus.index')
                         ->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        $menu->load(['allItems' => fn($q) => $q->with('children')]);

        return view('admin.menus.edit', [
            'menu' => $menu,
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'slug'      => "nullable|string|max:100|unique:menus,slug,{$menu->id}",
            'is_active' => 'boolean',
        ]);

        $data['slug']      = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        $menu->update($data);

        return redirect()->route('admin.menus.index')
                         ->with('success', 'Menu updated.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return back()->with('success', 'Menu deleted.');
    }
}