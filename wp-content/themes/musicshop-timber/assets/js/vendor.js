import Swiper, { Pagination } from "swiper";

const swiper = new Swiper(".hero-swiper", {
	modules: [Pagination],
	speed: 800,
	pagination: {
		el: ".swiper-pagination",
		clickable: true,
	},
});
