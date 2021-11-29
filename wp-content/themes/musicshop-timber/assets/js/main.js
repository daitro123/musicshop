import "../sass/main.scss";
import { Swiper, Thumbs } from "swiper";
import "fslightbox";

const thumbsSwiper = new Swiper(".gallery__thumbs", {
	freeMode: true,
	spaceBetween: 5,
	slidesPerView: 5,
	watchSlidesProgress: true,
});

const gallerySwiper = new Swiper(".gallery__main", {
	modules: [Thumbs],
	spaceBetween: 10,
	controller: {
		control: thumbsSwiper,
	},
	thumbs: { swiper: thumbsSwiper, autoScrollOffset: 1 },
});
