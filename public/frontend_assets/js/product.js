let desktopImageIndex = 0;
let modalImageIndex = 0;
let mobileIndex = 0;

let scale = 1;
let translateX = 0;
let translateY = 0;
let isDragging = false;
let startX = 0;
let startY = 0;
let modalImg = null;
let mobileThumbs = [];

function setDesktopImage(element) {
    const index = Array.from(document.querySelectorAll('.desktop-thumb')).indexOf(element);
    if (index >= 0) {
        desktopImageIndex = index;
        updateDesktopImage();
    }
}

function nextDesktopImage() {
    desktopImageIndex = (desktopImageIndex + 1) % imageUrls.length;
    updateDesktopImage();
}

function prevDesktopImage() {
    desktopImageIndex = (desktopImageIndex - 1 + imageUrls.length) % imageUrls.length;
    updateDesktopImage();
}

function updateDesktopImage() {
    const desktopMainImage = document.getElementById('desktopMainImage');
    if (desktopMainImage) {
        desktopMainImage.src = imageUrls[desktopImageIndex];
    }

    document.querySelectorAll('.desktop-thumb').forEach((thumb, index) => {
        thumb.style.opacity = index === desktopImageIndex ? '1' : '0.6';
        thumb.classList.toggle('border-2', index === desktopImageIndex);
        thumb.classList.toggle('border-danger', index === desktopImageIndex);
    });
}

function nextMobileImage() {
    mobileIndex = (mobileIndex + 1) % imageUrls.length;
    setMobileImageByIndex(mobileIndex);
}

function prevMobileImage() {
    mobileIndex = (mobileIndex - 1 + imageUrls.length) % imageUrls.length;
    setMobileImageByIndex(mobileIndex);
}

function openImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'flex';
    modal.style.flexDirection = 'column';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    document.body.style.overflow = 'hidden';

    modalImageIndex = window.innerWidth >= 768 ? desktopImageIndex : mobileIndex;
    updateModalImage();
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.body.style.overflow = '';
}

function setModalImage(element) {
    const index = Array.from(document.querySelectorAll('.modal-thumb')).indexOf(element);
    if (index >= 0) {
        modalImageIndex = index;
        updateModalImage();
    }
}

function nextModalImage() {
    modalImageIndex = (modalImageIndex + 1) % imageUrls.length;
    updateModalImage();
}

function prevModalImage() {
    modalImageIndex = (modalImageIndex - 1 + imageUrls.length) % imageUrls.length;
    updateModalImage();
}

function updateModalImage() {
    const modalMainImage = document.getElementById('modalMainImage');
    if (modalMainImage) {
        modalMainImage.src = imageUrls[modalImageIndex];
    }

    scale = 1;
    translateX = 0;
    translateY = 0;

    if (modalImg) {
        modalImg.style.transform = 'translate(0px, 0px) scale(1)';
        modalImg.style.cursor = 'default';
    }

    document.querySelectorAll('.modal-thumb').forEach((thumb, index) => {
        thumb.style.opacity = index === modalImageIndex ? '1' : '0.5';
        thumb.style.borderColor = index === modalImageIndex ? 'white' : 'transparent';
    });
}

function setMobileImageByIndex(nextIndex) {
    if (!mobileThumbs.length) return;

    const total = mobileThumbs.length;
    const safeIndex = ((nextIndex % total) + total) % total;
    const nextThumb = mobileThumbs[safeIndex];
    const mainImage = document.getElementById('mobileMainImage');

    if (mainImage) {
        mainImage.classList.remove('mobile-slide-anim');
        mainImage.offsetWidth;
        mainImage.src = nextThumb.src;
        mainImage.classList.add('mobile-slide-anim');
    }

    mobileThumbs.forEach(img => {
        img.classList.add('opacity-75');
        img.classList.remove('border', 'border-2', 'border-danger');
    });

    nextThumb.classList.remove('opacity-75');
    nextThumb.classList.add('border', 'border-2', 'border-danger');
    mobileIndex = safeIndex;
}

function changeImage(element) {
    if (element && element.classList.contains('mobile-thumb')) {
        const index = mobileThumbs.indexOf(element);
        setMobileImageByIndex(index >= 0 ? index : 0);
        return;
    }

    const mobileMainImage = document.getElementById('mobileMainImage');
    if (mobileMainImage) {
        mobileMainImage.src = element.src;
    }

    element.parentElement.querySelectorAll('img').forEach(img => {
        img.classList.add('opacity-75');
        img.classList.remove('border', 'border-2', 'border-danger');
    });

    element.classList.remove('opacity-75');
    element.classList.add('border', 'border-2', 'border-danger');
}

function setupMobileSwipeSlider() {
    mobileThumbs = Array.from(document.querySelectorAll('.mobile-thumb'));
    if (!mobileThumbs.length) return;

    const mainImage = document.getElementById('mobileMainImage');
    if (!mainImage) return;

    const initialIndex = mobileThumbs.findIndex(img => img.src === mainImage.src);
    setMobileImageByIndex(initialIndex >= 0 ? initialIndex : 0);

    let touchStartX = 0;
    let touchStartY = 0;
    let tracking = false;

    mainImage.addEventListener('touchstart', e => {
        if (e.touches.length !== 1) return;
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        tracking = true;
    }, { passive: true });

    mainImage.addEventListener('touchend', e => {
        if (!tracking) return;
        tracking = false;

        const deltaX = e.changedTouches[0].clientX - touchStartX;
        const deltaY = e.changedTouches[0].clientY - touchStartY;

        if (Math.abs(deltaX) < 35 || Math.abs(deltaX) < Math.abs(deltaY)) return;

        if (deltaX < 0) {
            setMobileImageByIndex(mobileIndex + 1);
        } else {
            setMobileImageByIndex(mobileIndex - 1);
        }
    }, { passive: true });
}

function toggleReviewForm() {
    const summary = document.getElementById('reviewSummary');
    const form = document.getElementById('reviewForm');

    if (form.classList.contains('d-none')) {
        summary.classList.add('d-none');
        form.classList.remove('d-none');
    } else {
        form.classList.add('d-none');
        summary.classList.remove('d-none');
    }
}

function setRating(val) {
    document.querySelectorAll('.star-rating i').forEach((star, index) => {
        star.classList.toggle('fa-solid', index < val);
        star.classList.toggle('fa-regular', index >= val);
    });
}

function applyTransform() {
    if (!modalImg) return;
    modalImg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
    modalImg.style.cursor = scale > 1 ? (isDragging ? 'grabbing' : 'grab') : 'default';
}

function zoomIn() {
    if (!modalImg) return;
    scale = Math.min(scale + 0.25, 4);
    applyTransform();
}

function zoomOut() {
    if (!modalImg) return;
    scale = Math.max(scale - 0.25, 1);

    if (scale === 1) {
        translateX = 0;
        translateY = 0;
    }

    applyTransform();
}

function resetZoom() {
    if (!modalImg) return;
    scale = 1;
    translateX = 0;
    translateY = 0;
    applyTransform();
}

function setWishlistButtonState($button, inWishlist) {
    $button.toggleClass('active', inWishlist);
    $button.attr('data-in-wishlist', inWishlist ? '1' : '0');

    const icon = $button.find('i');
    icon.removeClass('fa-regular fa-solid');
    icon.addClass(inWishlist ? 'fa-solid' : 'fa-regular');
}

function showWishlistAuthPopup() {
    Swal.fire({
        icon: 'warning',
        title: 'Please Login',
        text: 'You need to be logged in to manage your wishlist.',
        confirmButtonText: 'Login',
        confirmButtonColor: '#8b1e2d',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = wishlistConfig.loginUrl;
        }
    });
}

function toggleWishlist($button) {
    const productId = $button.data('product-id');
    const inWishlist = String($button.attr('data-in-wishlist')) === '1';
    const url = inWishlist ? wishlistConfig.removeUrl : wishlistConfig.addUrl;

    $button.prop('disabled', true);

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            _token: wishlistConfig.csrfToken,
            product_id: productId
        },
        success: function (response) {
            setWishlistButtonState($button, response.in_wishlist);

            Swal.fire({
                icon: 'success',
                title: response.in_wishlist ? 'Added to Wishlist' : 'Removed from Wishlist',
                text: response.message,
                confirmButtonColor: '#8b1e2d',
                timer: 1800,
                showConfirmButton: false
            });
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                showWishlistAuthPopup();
                return;
            }

            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: xhr.responseJSON?.message ?? 'Something went wrong. Please try again.',
                confirmButtonColor: '#8b1e2d',
            });
        },
        complete: function () {
            $button.prop('disabled', false);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    setupMobileSwipeSlider();

    modalImg = document.getElementById('modalMainImage');

    if (modalImg) {
        modalImg.addEventListener('mousedown', function (e) {
            if (scale <= 1) return;
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            e.preventDefault();
            applyTransform();
        });

        modalImg.addEventListener('dragstart', function (e) {
            e.preventDefault();
        });
    }

    document.addEventListener('mousemove', function (e) {
        if (!isDragging || scale <= 1) return;
        translateX += e.clientX - startX;
        translateY += e.clientY - startY;
        startX = e.clientX;
        startY = e.clientY;
        applyTransform();
    });

    document.addEventListener('mouseup', function () {
        isDragging = false;
        applyTransform();
    });

    $(document).on('click', '.wishlist-btn', function () {
        toggleWishlist($(this));
    });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeImageModal();

    if (document.getElementById('imageModal').style.display === 'flex') {
        if (e.key === 'ArrowLeft') prevModalImage();
        if (e.key === 'ArrowRight') nextModalImage();
    }
});