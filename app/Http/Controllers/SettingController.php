<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class SettingController extends Controller
{
    public function settings()
    {
        $settings = Setting::first();

        return view('admin.settings', compact('settings'));
    }

    public function settings_update(Request $request)
    {
        $request->validate([
            'store_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email',
            'contact_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'dark_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'favicon' => 'nullable|image|mimes:jpg,jpeg,png,ico,webp',
        ]);

        $settings = Setting::first();

        if (!$settings) {
            $settings = new Setting();
        }

        // Logo Upload
        if ($request->hasFile('logo')) {

            if ($settings->logo && File::exists(public_path('uploads/settings/' . $settings->logo))) {
                File::delete(public_path('uploads/settings/' . $settings->logo));
            }

            $logo = time() . '_logo.' . $request->logo->extension();
            $request->logo->move(public_path('uploads/settings'), $logo);

            $settings->logo = $logo;
        }

        // Dark Logo Upload
        if ($request->hasFile('dark_logo')) {

            if ($settings->dark_logo && File::exists(public_path('uploads/settings/' . $settings->dark_logo))) {
                File::delete(public_path('uploads/settings/' . $settings->dark_logo));
            }

            $darkLogo = time() . '_dark_logo.' . $request->dark_logo->extension();
            $request->dark_logo->move(public_path('uploads/settings'), $darkLogo);

            $settings->dark_logo = $darkLogo;
        }

        // Favicon Upload
        if ($request->hasFile('favicon')) {

            if ($settings->favicon && File::exists(public_path('uploads/settings/' . $settings->favicon))) {
                File::delete(public_path('uploads/settings/' . $settings->favicon));
            }

            $favicon = time() . '_favicon.' . $request->favicon->extension();
            $request->favicon->move(public_path('uploads/settings'), $favicon);

            $settings->favicon = $favicon;
        }

        $settings->store_name = $request->store_name;
        $settings->office_address = $request->office_address;
        $settings->store_address = $request->store_address;
        $settings->support_email = $request->support_email;
        $settings->contact_number = $request->contact_number;
        $settings->whatsapp_number = $request->whatsapp_number;

        $settings->working_days = $request->working_days;
        $settings->opening_time = $request->opening_time;
        $settings->closing_time = $request->closing_time;

        $settings->facebook = $request->facebook;
        $settings->instagram = $request->instagram;
        $settings->twitter = $request->twitter;
        $settings->linkedin = $request->linkedin;
        $settings->youtube = $request->youtube;

        $settings->meta_title = $request->meta_title;
        $settings->meta_description = $request->meta_description;

        $settings->save();

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
