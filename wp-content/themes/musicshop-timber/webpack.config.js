const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
// const ImageMinimizerPlugin = require("image-minimizer-webpack-plugin");

module.exports = {
	entry: {
		main: "./assets/js/main.js",
		// vendor: "./assets/js/vendor.js",
	},
	output: {
		path: path.resolve(__dirname, "dist"),
		filename: "[name].min.js",
		clean: true,
	},
	module: {
		rules: [
			{
				test: /\.scss$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					{
						// translates CSS into CommonJS modules
						loader: "css-loader",
						options: {
							sourceMap: true,
						},
					},
					{
						// Run postcss actions
						loader: "postcss-loader",
						options: {
							sourceMap: true,
							postcssOptions: {
								plugins: [
									[
										"autoprefixer",
										{
											grid: "autoplace",
										},
									],
								],
							},
						},
					},
					{
						// compiles Sass to CSS
						loader: "sass-loader",
						options: {
							sourceMap: true,
						},
					},
				],
			},
			{
				test: /\.(png|jpe?g|gif|webp)$/i,
				type: "asset/resource",
				generator: {
					filename: "images/[name][ext]",
				},
			},
		],
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: "style.min.css",
		}),
		// new ImageMinimizerPlugin({
		//     minimizerOptions: {
		//         // Lossless optimization with custom option
		//         // Feel free to experiment with options for better result for you
		//         plugins: [
		//             ["gifsicle", { interlaced: true }],
		//             ["jpegtran", { progressive: true }],
		//             ["optipng", { optimizationLevel: 5 }],
		//         ],
		//     },
		// }),
	],
};
