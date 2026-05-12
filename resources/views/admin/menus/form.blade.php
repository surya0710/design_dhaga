<div class="mb-3">
    <label>Menu Title</label>

    <input type="text"
           name="title"
           class="form-control"
           value="{{ old('title', $menu->title ?? '') }}">
</div>

<div class="mb-3">
    <label>URL</label>

    <input type="text"
           name="url"
           class="form-control"
           placeholder="/about-us"
           value="{{ old('url', $menu->url ?? '') }}">
</div>

<div class="mb-3">
    <label>Parent Menu</label>

    <select name="parent_id" class="form-control">

        <option value="">Main Menu</option>

        @foreach($parents as $parent)

            <option value="{{ $parent->id }}"
                {{ old('parent_id', $menu->parent_id ?? '') == $parent->id ? 'selected' : '' }}>

                {{ $parent->title }}

            </option>

        @endforeach

    </select>
</div>

<div class="mb-3">
    <label>Sort Order</label>

    <input type="number"
           name="sort_order"
           class="form-control"
           value="{{ old('sort_order', $menu->sort_order ?? 0) }}">
</div>

<div class="mb-3">

    <label>Status</label>

    <select name="status" class="form-control">

        <option value="1"
            {{ old('status', $menu->status ?? 1) == 1 ? 'selected' : '' }}>
            Active
        </option>

        <option value="0"
            {{ old('status', $menu->status ?? 1) == 0 ? 'selected' : '' }}>
            Inactive
        </option>

    </select>

</div>