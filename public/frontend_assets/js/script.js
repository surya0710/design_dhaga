'use strict';

const textData = [
  "<span>Hand-painted fashion</span> <img src='/frontend_assets/images/emoji/Hand-painted-fashion.png' class='emoji' alt='Hand Painted Fashion'>",
  "<span>Custom design</span> <img src='/frontend_assets/images/emoji/Custom-design.png' class='emoji' alt='Custom Design'>",
  "<span>Premium branding services</span> <img src='/frontend_assets/images/emoji/Premium.png' class='emoji' alt='Premium Branding Services'>",
  "<span>Made In India</span> <img src='/frontend_assets/images/emoji/india-flag.png' class='emoji' alt='Made in India'>",
  "<span>Made with heart</span> <img src='/frontend_assets/images/emoji/heart.png' class='emoji' alt='Made with Heart'>",
  "<span>Loved by 400+ Customers</span> <img src='/frontend_assets/images/emoji/Customers.png' class='emoji' alt='Loved by 400+ Customers'>",
];


let index = 0;
const textElement = document.querySelector(".rotating-text p");

const fadeDuration = 500;    // fade out duration
const switchInterval = 3000; // total interval

// Initial text
textElement.innerHTML = textData[index];
textElement.classList.add("active");

setInterval(() => {
  // Fade out
  textElement.classList.remove("active");

  setTimeout(() => {
    index = (index + 1) % textData.length;
    textElement.innerHTML = textData[index];
    textElement.classList.add("active");
  }, fadeDuration);

}, switchInterval);


// Side Menu
const openMenu = document.getElementById("openMenu");
const closeMenu = document.getElementById("closeMenu");
const sideMenu = document.getElementById("sideMenu");
const overlay = document.getElementById("menuOverlay");

function closeSideMenu() {
  sideMenu.classList.remove("active");
  overlay.classList.remove("active");
  closeMenu.classList.add("d-none");
}

openMenu.onclick = () => {
  sideMenu.classList.add("active");
  overlay.classList.add("active");
  closeMenu.classList.remove("d-none");
};

if (closeMenu) {
  closeMenu.onclick = closeSideMenu;
}

/* ✅ Close when clicking outside */
overlay.onclick = closeSideMenu;

/* Submenu Toggle */
document.querySelectorAll(".menu-title").forEach(title => {
  title.addEventListener("click", function () {

    let target = this.getAttribute("data-toggle");
    let submenu = document.getElementById(target);
    let icon = this.querySelector(".toggle-icon");

    // Close other submenus
    // document.querySelectorAll(".submenu").forEach(sm => {
    //   if (sm !== submenu) sm.style.display = "none";
    // });

    // Reset icons
    document.querySelectorAll(".toggle-icon").forEach(ic => {
      if (ic !== icon) ic.textContent = "+";
    });

    // Toggle current submenu
    if (submenu.style.display === "block") {
      submenu.style.display = "none";
      icon.textContent = "+";
    } else {
      submenu.style.display = "block";
      icon.textContent = "-";
    }
  });
});


document.addEventListener("DOMContentLoaded", function () {

    // HOME SLIDER
    const homeSlider = document.querySelector('#homeSlider');

    if (homeSlider) {
        new bootstrap.Carousel(homeSlider, {
            // interval: 3000,
            ride: "carousel",
            pause: "hover",
            wrap: true,
            keyboard: true,
            touch: true
        });
    }
});

document.addEventListener("scroll", () => {
    document.querySelectorAll(".timeline-item").forEach((item) => {
        const rect = item.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            item.classList.add("show");
        }
    });
});

const goToTopBtn = document.getElementById("goToTop");

// show / hide button
window.addEventListener("scroll", () => {
  if (window.scrollY > 300) {
    goToTopBtn.style.display = "block";
  } else {
    goToTopBtn.style.display = "none";
  }
});

// scroll to top
goToTopBtn.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth"
  });
});
