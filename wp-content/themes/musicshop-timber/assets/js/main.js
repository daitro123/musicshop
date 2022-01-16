import "../sass/main.scss";
import Swiper, { Pagination, Autoplay, Thumbs } from "swiper";
import "fslightbox";
import "popper.js";
import MmenuLight from "mmenu-light";

/***************************
 * MMENU SETUP
 ***************************/
const menu = new MmenuLight(document.querySelector("#nav-main-mobile"), "(max-width: 992px)");
const navigator = menu.navigation({});
const drawer = menu.offcanvas({ position: "left" });

document.addEventListener("DOMContentLoaded", () => {
    document.querySelector("a[href='#nav-main-mobile']").addEventListener("click", (event) => {
        event.preventDefault();
        drawer.open();
    });
});

(function ($) {
    /**********************
     * Init hero swiper
     **********************/
    const swiper = new Swiper(".hero-swiper", {
        modules: [Pagination, Autoplay],
        speed: 800,
        loop: true,
        autoplay: {
            delay: 6500,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
            type: "bullets",
            // renderBullet: (index, className) => {
            // 	return `<span class="${className} haha${index}"></span>`;
            // },
        },
    });

    /**************************
     * Gallery init
     **************************/
    function swiperGalleryInit() {
        const thumbsSwiper = new Swiper(".gallery__thumbs", {
            freeMode: true,
            spaceBetween: 5,
            slidesPerView: 5,
            watchSlidesProgress: true,
        });

        const gallerySwiper = new Swiper(".gallery__main", {
            modules: [Thumbs],
            effect: "fade",
            spaceBetween: 10,
            controller: {
                control: thumbsSwiper,
            },
            thumbs: { swiper: thumbsSwiper, autoScrollOffset: 1 },
        });
    }

    // init gallery for other than variable product types
    if ($("body.single-product").length && !$("article.product-type-variable").length) {
        $(document).ready(function () {
            swiperGalleryInit();
        });
    }

    /********************************************
     * Init gallery for variable product types
     *
     * Fetch variation image URLs from custom API route,
     * listen to change event on select variation element and
     * update the gallery if event fires
     *******************************************/

    if ($("body.single-product").length && $("article.product-type-variable").length) {
        // holds all variations available
        let variations;
        const params = new URLSearchParams(location.search);

        // set select as disabled until ajax gets data from API
        $("select#color").attr("disabled", true);

        $(".single-product-content").on("DOMNodeInserted", function () {
            swiperGalleryInit();
            refreshFsLightbox();
        });

        $(document).ready(function () {
            // fetch variation images
            $.ajax({
                url: "https://musicshop.local/wp-json/musicshop/v1/variations/18",
                beforeSend: function () {
                    if (params.get("attribute_color")) {
                        $(".gallery-loading").css("display", "flex");
                    }
                },
            })
                .done(function (res) {
                    variations = res;

                    if (params.get("attribute_color")) {
                        updateHTML(params.get("attribute_color"), "attribute_color");
                    }

                    // allow selection since data has been received
                    $(".gallery-loading").css("display", "none");
                    $("select#color").attr("disabled", false);
                    console.log(variations);
                })
                .fail(function (res) {
                    console.log(res);
                });
        });

        // listen for change event on select element
        $("select#color").on("change", function (e) {
            const selectedVariation = $(this).val();
            if (selectedVariation !== "") {
                updateHTML(selectedVariation, "attribute_color");

                // const item = getVariationBasedOnAttributeName("attribute_color", selectedVariation);

                // createNewGallery("attribute_color", selectedVariation);
                // updateAddToCartAjaxButton(item);
                // updateSKU(item.sku);
            }
        });

        /**************************************
         * Create new gallery from API data
         *
         * @param {String} attribute
         * @param {String} selectedVariation
         **************************************/
        function createNewGallery(attribute, selectedVariation) {
            $(".gallery").html("");
            let galleryHTML;

            const variation = getVariationBasedOnAttributeName(attribute, selectedVariation);

            const slides = variation.images.large.map((largeImage, index) => {
                return `
						<div class="swiper-slide">
							<a data-fslightbox="" href="${variation.images.full[index]}}">
								<img src="${largeImage}" alt="">
							</a>
						</div>
						`;
            });

            const thumbs = variation.images.thumbnails.map((thumbnail) => {
                return `
					<div class="swiper-slide">
						<img src="${thumbnail}" alt="">
					</div>
					`;
            });

            galleryHTML = `
					
						<div class="gallery__main swiper">
							<div class="swiper-wrapper" >
								${slides.join("")}
							</div>
						</div>
						<div class="swiper gallery__thumbs">
							<div class="swiper-wrapper">
								${thumbs.join("")}
							</div>
						</div>
					
				`;

            $(".gallery").html(galleryHTML);
        }

        function updateHTML(selectedVariation, attribute) {
            const item = getVariationBasedOnAttributeName(attribute, selectedVariation);

            createNewGallery(attribute, selectedVariation);
            updateAddToCartAjaxButton(item);
            updateSKU(item.sku);
        }

        /*******************************************************
         * Filter the variation item based on passed attribute
         *
         * @param {String} attrName
         * @param {String | Number} value
         * @returns {Object}
         *******************************************************/
        function getVariationBasedOnAttributeName(attrName, value) {
            const variation = variations.filter((item) => item[attrName] === value);
            return variation[0];
        }

        /*****************************************************************
         * Update value and data-product_id attr for ajax add to cart btn
         *
         * @param {Object} item
         *****************************************************************/
        function updateAddToCartAjaxButton(item) {
            $(".add_to_cart_button")
                .attr("value", item.variation_id)
                .attr("data-product_id", item.variation_id)
                .attr("data-product_thumbnail", item.images.thumbnails[0]);
        }

        /**********************
         * Update SKU number
         *
         * @param {string | number} sku
         **********************/
        function updateSKU(sku) {
            $(".sku-value").html(sku);
        }
    }

    $(document).ready(function () {
        $("body").on("added_to_cart", function () {
            $("#cartToast").toast({ animation: true, autohide: true, delay: 4000 }).toast("show");
        });
    });
})(jQuery);
