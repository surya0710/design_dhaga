<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FooterWidget;
use App\Models\FooterWidgetItem;

class FooterWidgetController extends Controller
{
    public function index()
    {
        $widgets = FooterWidget::with('items')
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.footer-widgets.index',
            compact('widgets')
        );
    }

    public function store(Request $request)
    {
        $widget = FooterWidget::create([
            'title' => $request->title,
            'sort_order' => $request->sort_order ?? 0
        ]);

        if ($request->items) {

            foreach ($request->items as $item) {

                if (!empty($item['text'])) {

                    FooterWidgetItem::create([
                        'footer_widget_id' => $widget->id,
                        'text' => $item['text'],
                        'link' => $item['link'],
                        'sort_order' => $item['sort_order'] ?? 0
                    ]);
                }
            }
        }

        return back()->with(
            'success',
            'Footer widget added successfully.'
        );
    }

    public function update(Request $request, $id)
    {
        $widget = FooterWidget::findOrFail($id);

        $widget->update([
            'title' => $request->title,
            'sort_order' => $request->sort_order ?? 0
        ]);

        FooterWidgetItem::where(
            'footer_widget_id',
            $widget->id
        )->delete();

        if ($request->items) {

            foreach ($request->items as $item) {

                if (!empty($item['text'])) {

                    FooterWidgetItem::create([
                        'footer_widget_id' => $widget->id,
                        'text' => $item['text'],
                        'link' => $item['link'],
                        'sort_order' => $item['sort_order'] ?? 0
                    ]);
                }
            }
        }

        return back()->with(
            'success',
            'Footer widget updated successfully.'
        );
    }

    public function delete($id)
    {
        FooterWidget::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Widget deleted successfully.'
        );
    }
}