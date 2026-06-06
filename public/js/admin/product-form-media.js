/**
 * Restore media picker previews from hidden input values (e.g. after validation errors).
 */
(function () {
    'use strict';

    function hidePickerChrome(picker) {
        if (!picker) return;
        picker.querySelectorAll('.pick-icon, .pick-label, .pick-btn').forEach(function (el) {
            el.style.display = 'none';
        });
    }

    function restoreSingleImage(inputId, previewId, pickerId) {
        var input = document.getElementById(inputId);
        var img = document.getElementById(previewId);
        if (!input || !img || !input.value.trim()) return;

        var path = input.value.trim();
        var picker = pickerId ? document.getElementById(pickerId) : img.closest('.media-picker, .image-picker');

        img.src = '/storage/' + path;
        img.style.display = 'block';
        if (picker) {
            picker.classList.add('has-image');
            hidePickerChrome(picker);
        }
    }

    function restoreGallery() {
        var galleryInput = document.getElementById('gallery_images');
        var galleryPreview = document.getElementById('gallery_preview');
        if (!galleryInput || !galleryPreview || !galleryInput.value.trim()) return;
        if (typeof addGalleryThumb !== 'function') return;

        var paths = galleryInput.value.split(',').map(function (p) { return p.trim(); }).filter(Boolean);
        if (!paths.length) return;

        galleryPreview.innerHTML = '';
        paths.forEach(function (path) {
            addGalleryThumb(galleryPreview, path);
        });

        var addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'gallery-add-btn';
        addBtn.textContent = '+';
        addBtn.onclick = function () {
            openMediaUploaderMultiple('gallery_images', 'gallery_preview');
        };
        galleryPreview.appendChild(addBtn);
    }

    function initProductMediaPreviews() {
        restoreSingleImage('product_image', 'preview_product_image', 'picker_product_image');
        restoreSingleImage('square_banner', 'preview_square_banner', 'picker_square_banner');

        for (var i = 1; i <= 3; i++) {
            restoreSingleImage('artisan_image_' + i, 'preview_artisan_image_' + i, 'picker_artisan_image_' + i);
        }

        for (var j = 1; j <= 6; j++) {
            restoreSingleImage('productIcons_' + j, 'preview_productIcons_' + j, 'picker_productIcons_' + j);
        }

        restoreGallery();
    }

    document.addEventListener('DOMContentLoaded', initProductMediaPreviews);
})();
