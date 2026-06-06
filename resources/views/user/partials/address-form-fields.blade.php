@php
    $address = $address ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label text-muted small text-uppercase fw-bold">Full Name</label>
        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $address->full_name ?? Auth::user()->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted small text-uppercase fw-bold">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $address->phone ?? (Auth::user()->mobile ?? '')) }}" required>
    </div>
    <div class="col-12">
        <label class="form-label text-muted small text-uppercase fw-bold">Address Line 1</label>
        <textarea name="address_line_1" class="form-control" rows="2" required>{{ old('address_line_1', $address->address_line_1 ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted small text-uppercase fw-bold">Address Line 2</label>
        <input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2', $address->address_line_2 ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted small text-uppercase fw-bold">Landmark</label>
        <input type="text" name="landmark" class="form-control" value="{{ old('landmark', $address->landmark ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-muted small text-uppercase fw-bold">City</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $address->city ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label text-muted small text-uppercase fw-bold">State</label>
        <input type="text" name="state" class="form-control" value="{{ old('state', $address->state ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label text-muted small text-uppercase fw-bold">Pincode</label>
        <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $address->pincode ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted small text-uppercase fw-bold">Country</label>
        <input type="text" name="country" class="form-control" value="{{ old('country', $address->country ?? 'India') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted small text-uppercase fw-bold">Address Type</label>
        <select name="address_type" class="form-select" required>
            @foreach(['home' => 'Home', 'work' => 'Work', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('address_type', $address->address_type ?? 'home') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <div class="form-check">
            <input type="hidden" name="is_default" value="0">
            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="defaultAddress{{ $address->id ?? 'New' }}" @checked(old('is_default', $address->is_default ?? false))>
            <label class="form-check-label" for="defaultAddress{{ $address->id ?? 'New' }}">Use as default address</label>
        </div>
    </div>
</div>
