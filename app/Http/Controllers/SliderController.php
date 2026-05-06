<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sliders;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function sliders(){
        $sliders = Sliders::orderBy('id', 'desc')->paginate(10);
        return view('admin.sliders', compact('sliders'));
    }

    public function sliders_create(){
        return view('admin.sliders-create');
    }

    public function sliders_add(Request $request, $id = null)
    {
        $data = $request->validate([
            'heading'        => 'required|string',
            'description'    => 'nullable|string',
            'image'          => $id ? 'nullable|image|mimes:jpg,jpeg,png,webp' : 'required|image|mimes:jpg,jpeg,png,webp',
            'image_alt'      => 'nullable|string|max:255',
            'button_text'    => 'nullable|string|max:255',
            'button_link'    => 'nullable|url',
            'target'         => 'nullable|in:_self,_blank',
            'order'          => 'nullable|integer',
            'active_status'  => 'required|boolean',
            'text_location'  => 'required|in:left,center,right',
            'text_color'     => 'required|in:white,dark',
        ]);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sliders', 'public');
            $data['image'] = $imagePath;

            // Delete old image on update
            if ($id) {
                $slider = Sliders::findOrFail($id);
                if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                    Storage::disk('public')->delete($slider->image);
                }
            }
        }

        // Default values
        $data['target'] = $data['target'] ?? '_self';
        $data['order'] = $data['order'] ?? 1;

        if ($id) {
            // UPDATE
            $slider = Sliders::findOrFail($id);
            $slider->update($data);

            return redirect()->route('admin.sliders.create', $id)
                ->with('success', 'Slider updated successfully');
        } else {
            // CREATE
            $slider = Sliders::create($data);

            return redirect()->route('admin.sliders', $slider->id)
                ->with('success', 'Slider created successfully');
        }
    }

    public function sliders_edit($id)
    {
        $slider = Sliders::findOrFail($id);

        return view('admin.sliders-create', [
            'slider' => $slider
        ]);
    }

    public function update(Request $request, $id)
    {
        $slider = Sliders::findOrFail($id);

        $data = $request->validate([
            'heading'        => 'required|string',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'image_alt'      => 'nullable|string|max:255',
            'button_text'    => 'nullable|string|max:255',
            'button_link'    => 'nullable|url',
            'target'         => 'nullable|in:_self,_blank',
            'order'          => 'nullable|integer',
            'active_status'  => 'required|boolean',
            'text_location'  => 'required|in:left,center,right',
            'text_color'     => 'required',
        ]);

        // Handle Image (ONLY if uploaded)
        if ($request->hasFile('image')) {

            // Delete old image
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            // Store new image
            $data['image'] = $request->file('image')->store('sliders', 'public');
        } else {
            // Prevent overwriting existing image
            unset($data['image']);
        }

        // Defaults (optional safety)
        $data['target']         = $data['target'] ?? '_self';
        $data['order']          = $data['order'] ?? $slider->order ?? 1;
        $data['text_color']     = $data['text_color'] ?? $slider->text_color ?? 'white';

        $slider->update($data);

        return redirect()
            ->route('admin.sliders.edit', $slider->id)
            ->with('success', 'Slider updated successfully');
    }
}
