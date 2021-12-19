import "../sass/main.scss";
import Swiper, { Pagination, Autoplay, Thumbs } from "swiper";
import "fslightbox";
import "popper.js";
import "bootstrap";

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
		let variations;

		$(".single-product-content").on("DOMNodeInserted", function () {
			swiperGalleryInit();
			refreshFsLightbox();
		});

		$(document).ready(function () {
			// fetch variation images
			$.ajax({
				url: "https://musicshop.local/wp-json/musicshop/v1/variations/18",
			})
				.done(function (res) {
					variations = res;
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
				createNewGallery(selectedVariation);
			}
		});

		// create new gallery from API data
		function createNewGallery(colorName) {
			$(".single-product-content").html("");
			let galleryHTML;

			variations.forEach((variation, index) => {
				if (variation.attribute_color === colorName) {
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
                        <div class="gallery">
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
                        </div>
                    `;
				}
			});

			$(".single-product-content").html(galleryHTML);
		}
	}
})(jQuery);
