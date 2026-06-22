<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\HomeSectionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class HomePageController extends Controller
{
    public function index()
    {
        $sections = HomeSection::with('items')
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.home-page.index',
            compact('sections')
        );
    }

    public function updateSection(
        Request $request,
        HomeSection $section
    ) {

        $data = $request->validate([

            'title' => 'nullable|string|max:255',

            'subtitle' => 'nullable|string|max:255',

            'body' => 'nullable|string',

            'button_text' => 'nullable|string|max:255',

            'button_url' => 'nullable|string|max:255',

            'button_target' => 'nullable|in:_self,_blank',

            'bg_class' => 'nullable|string|max:255',

            'sort_order' => 'nullable|integer|min:0',

            'status' => 'nullable|boolean',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',

            'alt_tag' => 'nullable|string|max:255',
        ]);

        $data['button_target'] =
            $data['button_target'] ?? '_self';

        $data['status'] =
            $data['status'] ?? 1;

        $oldImage = $section->image;

        if ($request->hasFile('image')) {

            $newImage = $this->storeImage(
                $request->file('image')
            );

            $data['image'] = $newImage;

            if ($oldImage) {

                $this->deleteUploadedImage(
                    $oldImage
                );
            }
        }

        $section->update($data);

        return back()->with(
            'status',
            'Home page section updated successfully.'
        );
    }

    public function storeItem(
        Request $request,
        HomeSection $section
    ) {

        $data = $this->validateItem($request);

        $data['home_section_id'] = $section->id;

        $data['status'] =
            $data['status'] ?? 1;

        if ($request->hasFile('image')) {

            $data['image'] = $this->storeImage(
                $request->file('image')
            );
        }

        $section->items()->create($data);

        return back()->with(
            'status',
            'Home page item added successfully.'
        );
    }

    public function updateItem(
        Request $request,
        HomeSectionItem $item
    ) {

        $data = $this->validateItem($request);

        $data['status'] =
            $data['status'] ?? 1;

        $oldImage = $item->image;

        if ($request->hasFile('image')) {

            $newImage = $this->storeImage(
                $request->file('image')
            );

            $data['image'] = $newImage;

            if ($oldImage) {

                $this->deleteUploadedImage(
                    $oldImage
                );
            }
        }

        $item->update($data);

        return back()->with(
            'status',
            'Home page item updated successfully.'
        );
    }

    public function deleteItem(
        HomeSectionItem $item
    ) {

        $this->deleteUploadedImage(
            $item->image
        );

        $item->delete();

        return back()->with(
            'status',
            'Home page item deleted successfully.'
        );
    }

    private function validateItem(
        Request $request
    ): array {

        return $request->validate([

            'title' => 'nullable|string|max:255',

            'subtitle' => 'nullable|string',

            'body' => 'nullable|string',

            'link_text' => 'nullable|string|max:255',

            'link_url' => 'nullable|string|max:255',

            'icon' => 'nullable|string|max:255',

            'sort_order' => 'nullable|integer|min:0',

            'status' => 'nullable|boolean',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',

            'alt_tag' => 'nullable|string|max:255',
        ]);
    }

    private function storeImage($image): string
    {
        $directory = public_path(
            'uploads/home-page'
        );

        if (!File::exists($directory)) {

            File::makeDirectory(
                $directory,
                0755,
                true
            );
        }

        $filename =
            time() .
            '_' .
            Str::random(10) .
            '.' .
            $image->getClientOriginalExtension();

        $image->move(
            $directory,
            $filename
        );

        return 'uploads/home-page/' . $filename;
    }

    private function deleteUploadedImage(
        ?string $path
    ): void {

        if (
            $path &&
            Str::startsWith(
                $path,
                'uploads/home-page/'
            ) &&
            File::exists(public_path($path))
        ) {

            File::delete(
                public_path($path)
            );
        }
    }
}