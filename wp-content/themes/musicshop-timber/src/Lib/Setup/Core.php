<?php

namespace Theme\Lib\Setup;

class Core
{
    public function __construct()
    {
        add_action(
            'wp_enqueue_scripts',
            [
                $this,
                'loadStyles'
            ]
        );

        add_action(
            'wp_enqueue_scripts',
            [
                $this,
                'loadScripts'
            ]
        );

        add_action(
            'after_setup_theme',
            [
                $this,
                'themeSupports'
            ]
        );
    }

    public function loadStyles()
    {
        wp_enqueue_style('icons', get_stylesheet_directory_uri() . '/assets/icons/bootstrap-icons.css', [], filemtime(get_stylesheet_directory() . '/assets/icons/bootstrap-icons.css'));
        wp_enqueue_style('theme-styles', get_stylesheet_directory_uri() . '/dist/style.min.css', [], filemtime(get_stylesheet_directory() . '/dist/style.min.css'));
    }

    public function loadScripts()
    {
        wp_enqueue_script('vendor-scripts', get_stylesheet_directory_uri() . '/dist/vendor.min.js', [], filemtime(get_stylesheet_directory() . '/dist/vendor.min.js'), TRUE);
        wp_enqueue_script('theme-scripts', get_stylesheet_directory_uri() . '/dist/main.min.js', [], filemtime(get_stylesheet_directory() . '/dist/main.min.js'), TRUE);
    }

    public function themeSupports()
    {
        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
        add_theme_support('title-tag');

        /*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
        add_theme_support('post-thumbnails');

        /*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
        add_theme_support(
            'html5',
            array(
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            )
        );

        /*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
        add_theme_support(
            'post-formats',
            array(
                'aside',
                'image',
                'video',
                'quote',
                'link',
                'gallery',
                'audio',
            )
        );

        add_theme_support('menus');

        add_theme_support('woocommerce');

        // add_theme_support('wc-product-gallery-zoom');
        // add_theme_support('wc-product-gallery-lightbox');
        // add_theme_support('wc-product-gallery-slider');
    }
}
