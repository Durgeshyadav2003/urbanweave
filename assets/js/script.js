/* =========================================
   URBANWEAVE JAVASCRIPT
========================================= */


/* =========================================
   SEARCH
========================================= */

const searchBtn = document.getElementById("searchBtn");
const searchOverlay = document.getElementById("searchOverlay");
const closeSearch = document.getElementById("closeSearch");

if (searchBtn) {

    searchBtn.addEventListener("click", function () {

        searchOverlay.classList.add("active");

    });

}


if (closeSearch) {

    closeSearch.addEventListener("click", function () {

        searchOverlay.classList.remove("active");

    });

}


if (searchOverlay) {

    searchOverlay.addEventListener("click", function (event) {

        if (event.target === searchOverlay) {

            searchOverlay.classList.remove("active");

        }

    });

}


/* =========================================
   MOBILE MENU
========================================= */

const menuBtn = document.getElementById("menuBtn");
const navLinks = document.querySelector(".nav-links");

if (menuBtn) {

    menuBtn.addEventListener("click", function () {

        if (navLinks.style.display === "flex") {

            navLinks.style.display = "";

        } else {

            navLinks.style.display = "flex";
            navLinks.style.position = "absolute";
            navLinks.style.top = "65px";
            navLinks.style.left = "0";
            navLinks.style.right = "0";
            navLinks.style.background = "#f5f3ef";
            navLinks.style.padding = "25px";
            navLinks.style.flexDirection = "column";

        }

    });

}


/* =========================================
   NEWSLETTER
========================================= */

const newsletterForm =
    document.getElementById("newsletterForm");

const newsletterEmail =
    document.getElementById("newsletterEmail");

const newsletterMessage =
    document.getElementById("newsletterMessage");


if (newsletterForm) {

    newsletterForm.addEventListener("submit", function (event) {

        event.preventDefault();

        const email = newsletterEmail.value.trim();

        if (email === "") {

            newsletterMessage.textContent =
                "Please enter your email.";

            return;

        }

        newsletterMessage.textContent =
            "Thanks for joining UrbanWeave!";

        newsletterEmail.value = "";

    });

}


/* =========================================
   CART COUNT
========================================= */

function updateCartCount() {

    const cartCount =
        document.getElementById("cartCount");

    if (!cartCount) return;

    const cart =
        JSON.parse(localStorage.getItem("urbanweave_cart")) || [];

    let total = 0;

    cart.forEach(function (item) {

        total += Number(item.quantity) || 1;

    });

    cartCount.textContent = total;

}


updateCartCount();
/* =========================================
   WISHLIST COUNT
========================================= */

function updateWishlistCount() {

    const wishlistCount =
        document.getElementById("wishlistCount");

    if (!wishlistCount) return;

    const wishlist =
        JSON.parse(localStorage.getItem("urbanweave_wishlist")) || [];

    wishlistCount.textContent = wishlist.length;

}

updateWishlistCount();


/* =========================================
   SCROLL REVEAL
========================================= */

const revealElements =
    document.querySelectorAll(
        ".category-card, .product-card, .story-content"
    );


const revealObserver =
    new IntersectionObserver(

        function (entries) {

            entries.forEach(function (entry) {

                if (entry.isIntersecting) {

                    entry.target.style.opacity = "1";
                    entry.target.style.transform =
                        "translateY(0)";

                }

            });

        },

        {
            threshold: 0.1
        }

    );


revealElements.forEach(function (element) {

    element.style.opacity = "0";
    element.style.transform = "translateY(30px)";
    element.style.transition =
        "opacity 0.7s ease, transform 0.7s ease";

    revealObserver.observe(element);

});