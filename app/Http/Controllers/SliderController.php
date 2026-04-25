<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sliders;

class SliderController extends Controller
{
    

    public function sliders_add(Request $request, $id = null)
    {
        if ($request->isMethod('post')) {

            $data = $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'nullable|image',
            ]);

            if ($id) {
                // UPDATE
                $slider = Sliders::findOrFail($id);
                $slider->update($data);

                return redirect()->route('admin.sliders.create', $id)
                    ->with('success', 'Slider updated successfully');
            } else {
                // CREATE
                $slider = Sliders::create($data);

                return redirect()->route('admin.sliders.create', $slider->id)
                    ->with('success', 'Slider created successfully');
            }
        }

        // GET request → Show form
        $slider = $id ? Sliders::findOrFail($id) : null;

        return view('admin.sliders.form', compact('slider'));
    }
}
