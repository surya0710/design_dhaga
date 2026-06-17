"use strict";

function getWishlistConfig() {
    return window.wishlistConfig || {};
}

function getWishlistOptions() {
    return Object.assign({ isWishlistPage: false, reloadOnError: false }, window.wishlistOptions || {});
}

function openWishlistLoginModal() {
    const loginModal = document.getElementById("loginModal");

    if (!loginModal) {
        return;
    }

    if (window.bootstrap?.Modal) {
        bootstrap.Modal.getOrCreateInstance(loginModal).show();
        return;
    }

    if (window.jQuery) {
        window.jQuery(loginModal).modal("show");
    }
}

function loadWishlistSweetAlert() {
    if (typeof window.loadSweetAlert === "function") {
        return window.loadSweetAlert();
    }

    if (window.Swal) {
        return Promise.resolve(window.Swal);
    }

    return new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = "https://cdn.jsdelivr.net/npm/sweetalert2@11";
        script.async = true;
        script.onload = () => resolve(window.Swal);
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

async function showWishlistLoginPrompt() {
    if (typeof window.showWishlistAuthPopup === "function") {
        return window.showWishlistAuthPopup();
    }

    if (typeof window.showLoginAlert === "function") {
        return window.showLoginAlert({
            text: "You need to be logged in to manage your wishlist.",
        });
    }

    if (window.Swal) {
        const result = await window.Swal.fire({
            icon: "warning",
            title: "Please Login",
            text: "You need to be logged in to manage your wishlist.",
            confirmButtonText: "Login",
            confirmButtonColor: "#8b1e2d",
            showCancelButton: true,
            cancelButtonText: "Cancel",
        });

        if (result.isConfirmed) {
            openWishlistLoginModal();
        }

        return result;
    }

    openWishlistLoginModal();
}

function setWishlistButtonState(button, inWishlist) {
    button.toggleClass("active", inWishlist).attr("data-in-wishlist", inWishlist ? "1" : "0");
    button.find("i").removeClass("fa-regular").addClass("fa-solid");
}

function removeWishlistCard(button) {
    const card = button.closest(".product-item");

    card.fadeOut(250, function () {
        $(this).remove();

        const productsContainer = $(".products-conatiner");
        if (productsContainer.find(".product-item").length === 0) {
            productsContainer.html('<p class="shop-no-products text-center">There are no products to display.</p>');
        }
    });
}

async function toggleWishlist(button) {
    const config = getWishlistConfig();
    const options = getWishlistOptions();

    if (!config.addUrl || !config.removeUrl) {
        return;
    }

    const isAuthenticated = config.isAuthenticated ?? window.authConfig?.isAuthenticated;
    if (isAuthenticated === false) {
        await showWishlistLoginPrompt();
        return;
    }

    const productId = button.attr("data-product-id");
    const isInWishlist = String(button.attr("data-in-wishlist")) === "1";
    const url = isInWishlist ? config.removeUrl : config.addUrl;
    const Swal = await loadWishlistSweetAlert();

    button.prop("disabled", true);

    $.ajax({
        url,
        method: "POST",
        data: {
            _token: config.csrfToken,
            product_id: productId,
        },
        success: async function (response) {
            setWishlistButtonState(button, response.in_wishlist);

            await Swal.fire({
                iconHtml: '<i class="fa-regular fa-circle-check fa-2x"></i>',
                title: response.in_wishlist ? "Added to Wishlist" : "Removed from Wishlist",
                text: response.message,
                confirmButtonColor: "#8b1e2d",
                timer: 1800,
                showConfirmButton: false,
            });

            if (options.isWishlistPage && !response.in_wishlist) {
                removeWishlistCard(button);
            }
        },
        error: async function (xhr) {
            if (xhr.status === 401) {
                await showWishlistLoginPrompt();
                return;
            }

            const result = await Swal.fire({
                icon: "error",
                title: "Oops!",
                text: xhr.responseJSON?.message ?? "Something went wrong. Please try again.",
                confirmButtonColor: "#8b1e2d",
            });

            if (options.reloadOnError && (result.isConfirmed || result.isDismissed)) {
                location.reload();
            }
        },
        complete: function () {
            button.prop("disabled", false);
        },
    });
}

document.addEventListener(
    "click",
    function (event) {
        const button = event.target.closest(".wishlist-btn");

        if (!button) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        toggleWishlist($(button));
    },
    true
);
