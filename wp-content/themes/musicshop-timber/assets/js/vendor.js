import Swiper, { Pagination, Autoplay } from "swiper";

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
