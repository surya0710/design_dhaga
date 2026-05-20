@extends('layouts.admin')

@section('content')

<div class="main-content-inner">

    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">

            <h3>Footer Widgets</h3>

            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">

                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>

                <li>
                    <i class="icon-chevron-right"></i>
                </li>

                <li>
                    <div class="text-tiny">Footer Widgets</div>
                </li>

            </ul>

        </div>

        {{-- Add Widget --}}
        <div class="wg-box mb-30">

            <div class="flex items-center justify-between gap10 flex-wrap mb-20">

                <h5>Add New Widget</h5>

                <button type="button"
                    class="tf-button style-1"
                    id="toggleWidgetForm">
                    + Add Widget
                </button>

            </div>

            <form action="{{ route('footer.widgets.store') }}"
                method="POST"
                id="widgetCreateForm"
                style="display:none;">

                @csrf

                <div class="row">

                    <div class="col-lg-6">

                        <fieldset class="mb-20">

                            <input type="text"
                                name="title"
                                placeholder="Widget Title"
                                required>

                        </fieldset>

                    </div>

                    <div class="col-lg-6">

                        <fieldset class="mb-20">

                            <input type="number"
                                name="sort_order"
                                placeholder="Sort Order">

                        </fieldset>

                    </div>

                </div>

                <div id="newWidgetItems">

                    <div class="item-box">

                        <div class="row">

                            <div class="col-lg-4">

                                <fieldset class="mb-20">

                                    <input type="text"
                                        name="items[0][text]"
                                        placeholder="Link Text">

                                </fieldset>

                            </div>

                            <div class="col-lg-5">

                                <fieldset class="mb-20">

                                    <input type="text"
                                        name="items[0][link]"
                                        placeholder="URL">

                                </fieldset>

                            </div>

                            <div class="col-lg-2">

                                <fieldset class="mb-20">

                                    <input type="number"
                                        name="items[0][sort_order]"
                                        placeholder="Sort">

                                </fieldset>

                            </div>

                            <div class="col-lg-1">

                                <button type="button"
                                    class="tf-button style-2 remove-item">
                                    ×
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="flex items-center gap10 mt-10">

                    <button type="button"
                        class="tf-button style-5"
                        id="addNewItem">
                        + Add Link
                    </button>

                    <button type="submit"
                        class="tf-button style-1">
                        Save Widget
                    </button>

                </div>

            </form>

        </div>

        {{-- Existing Widgets --}}
        @forelse($widgets as $widget)

            <div class="wg-box mb-30">

                <form action="{{ route('footer.widgets.update', $widget->id) }}"
                    method="POST">

                    @csrf

                    <div class="flex items-center justify-between gap10 flex-wrap mb-20">

                        <h5>{{ $widget->title }}</h5>

                        <div class="flex items-center gap10">

                            <button type="submit"
                                class="tf-button style-1">
                                Save
                            </button>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-lg-6">

                            <fieldset class="mb-20">

                                <input type="text"
                                    name="title"
                                    value="{{ $widget->title }}"
                                    placeholder="Widget Title">

                            </fieldset>

                        </div>

                        <div class="col-lg-6">

                            <fieldset class="mb-20">

                                <input type="number"
                                    name="sort_order"
                                    value="{{ $widget->sort_order }}"
                                    placeholder="Sort Order">

                            </fieldset>

                        </div>

                    </div>

                    <div class="widget-items">

                        @foreach($widget->items as $index => $item)

                            <div class="item-box">

                                <div class="row">

                                    <div class="col-lg-4">

                                        <fieldset class="mb-20">

                                            <input type="text"
                                                name="items[{{ $index }}][text]"
                                                value="{{ $item->text }}"
                                                placeholder="Link Text">

                                        </fieldset>

                                    </div>

                                    <div class="col-lg-5">

                                        <fieldset class="mb-20">

                                            <input type="text"
                                                name="items[{{ $index }}][link]"
                                                value="{{ $item->link }}"
                                                placeholder="URL">

                                        </fieldset>

                                    </div>

                                    <div class="col-lg-2">

                                        <fieldset class="mb-20">

                                            <input type="number"
                                                name="items[{{ $index }}][sort_order]"
                                                value="{{ $item->sort_order }}"
                                                placeholder="Sort">

                                        </fieldset>

                                    </div>

                                    <div class="col-lg-1">

                                        <button type="button"
                                            class="tf-button style-2 remove-item">
                                            ×
                                        </button>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                    <div class="flex items-center justify-between mt-10">

                        <button type="button"
                            class="tf-button style-5 add-item-btn">
                            + Add Link
                        </button>

                </form>

                        <form action="{{ route('footer.widgets.delete', $widget->id) }}"
                            method="POST"
                            onsubmit="return confirm('Delete widget?')">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="tf-button style-2">
                                Delete Widget
                            </button>

                        </form>

                    </div>

            </div>

        @empty

            <div class="wg-box">

                <div class="text-center">

                    No footer widgets found.

                </div>

            </div>

        @endforelse

    </div>

</div>

<script>

    // Toggle Create Form
    document.getElementById('toggleWidgetForm')
        .addEventListener('click', function () {

            let form =
                document.getElementById('widgetCreateForm');

            form.style.display =
                form.style.display === 'none'
                ? 'block'
                : 'none';

        });

    // Add New Widget Link
    let newIndex = 1;

    document.getElementById('addNewItem')
        .addEventListener('click', function () {

            let wrapper =
                document.getElementById('newWidgetItems');

            wrapper.insertAdjacentHTML('beforeend', `

                <div class="item-box">

                    <div class="row">

                        <div class="col-lg-4">

                            <fieldset class="mb-20">

                                <input type="text"
                                    name="items[${newIndex}][text]"
                                    placeholder="Link Text">

                            </fieldset>

                        </div>

                        <div class="col-lg-5">

                            <fieldset class="mb-20">

                                <input type="text"
                                    name="items[${newIndex}][link]"
                                    placeholder="URL">

                            </fieldset>

                        </div>

                        <div class="col-lg-2">

                            <fieldset class="mb-20">

                                <input type="number"
                                    name="items[${newIndex}][sort_order]"
                                    placeholder="Sort">

                            </fieldset>

                        </div>

                        <div class="col-lg-1">

                            <button type="button"
                                class="tf-button style-2 remove-item">
                                ×
                            </button>

                        </div>

                    </div>

                </div>

            `);

            newIndex++;

        });

    // Add Existing Widget Link
    document.querySelectorAll('.add-item-btn')
        .forEach(button => {

            button.addEventListener('click', function () {

                let wrapper =
                    this.closest('.wg-box')
                    .querySelector('.widget-items');

                let index =
                    wrapper.querySelectorAll('.item-box').length;

                wrapper.insertAdjacentHTML('beforeend', `

                    <div class="item-box">

                        <div class="row">

                            <div class="col-lg-4">

                                <fieldset class="mb-20">

                                    <input type="text"
                                        name="items[${index}][text]"
                                        placeholder="Link Text">

                                </fieldset>

                            </div>

                            <div class="col-lg-5">

                                <fieldset class="mb-20">

                                    <input type="text"
                                        name="items[${index}][link]"
                                        placeholder="URL">

                                </fieldset>

                            </div>

                            <div class="col-lg-2">

                                <fieldset class="mb-20">

                                    <input type="number"
                                        name="items[${index}][sort_order]"
                                        placeholder="Sort">

                                </fieldset>

                            </div>

                            <div class="col-lg-1">

                                <button type="button"
                                    class="tf-button style-2 remove-item">
                                    ×
                                </button>

                            </div>

                        </div>

                    </div>

                `);

            });

        });

    // Remove Item
    document.addEventListener('click', function(e) {

        if (e.target.classList.contains('remove-item')) {

            e.target.closest('.item-box').remove();

        }

    });

</script>

@endsection