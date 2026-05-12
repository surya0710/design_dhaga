<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function create(Menu $menu)
    {
        $parents = $menu->allItems()->whereNull('parent_id')->get();
        return view('admin.menus.menu-item-create', compact('menu', 'parents'));
    }

    public function store(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'label'       => 'required|string|max:100',
            'url'         => 'nullable|string|max:255',
            'route_name'  => 'nullable|string|max:100',
            'route_params'=> 'nullable|string',   // JSON string from textarea
            'icon'        => 'nullable|string|max:100',
            'target'      => 'in:_self,_blank',
            'parent_id'   => 'nullable|exists:menu_items,id',
            'order'       => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        // Decode JSON params if provided
        $data['route_params'] = filled($data['route_params'] ?? null)
            ? json_decode($data['route_params'], true)
            : null;

        $data['is_active'] = $request->boolean('is_active', true);
        $data['menu_id']   = $menu->id;

        MenuItem::create($data);

        return redirect()->route('admin.menus.edit', $menu)
                         ->with('success', 'Menu item added.');
    }

    public function edit(Menu $menu, MenuItem $menuItem)
    {
        $parents = $menu->allItems()
                        ->whereNull('parent_id')
                        ->where('id', '!=', $menuItem->id)
                        ->get();

        return view('admin.menus-item.edit', [
            'menu'     => $menu,
            'menuItem' => $menuItem,  // ← must be exactly 'menuItem'
            'parents'  => $parents,
        ]);
    }

    public function update(Request $request, Menu $menu, MenuItem $menuItem)
    {
        $data = $request->validate([
            'label'        => 'required|string|max:100',
            'url'          => 'nullable|string|max:255',
            'route_name'   => 'nullable|string|max:100',
            'route_params' => 'nullable|string',
            'icon'         => 'nullable|string|max:100',
            'target'       => 'in:_self,_blank',
            'parent_id'    => 'nullable|exists:menu_items,id',
            'order'        => 'integer|min:0',
            'is_active'    => 'boolean',
        ]);

        $data['route_params'] = filled($data['route_params'] ?? null)
            ? json_decode($data['route_params'], true)
            : null;

        $data['is_active'] = $request->boolean('is_active', true);

        $menuItem->update($data);

        return redirect()->route('admin.menus.edit', $menu)
                         ->with('success', 'Menu item updated.');
    }

    public function destroy(Menu $menu, MenuItem $menuItem)
    {
        $menuItem->delete();
        return back()->with('success', 'Menu item deleted.');
    }

    /** AJAX: reorder items via drag-and-drop. */
    public function reorder(Request $request)
    {
        $request->validate(['items' => 'required|array']);

        foreach ($request->items as $index => $id) {
            MenuItem::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}