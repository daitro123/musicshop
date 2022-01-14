<?php

use Theme\Lib\Setup;
use Theme\Lib\Woo;

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composer_autoload)) {
    require_once $composer_autoload;
    new Timber\Timber();
    new Setup\Core();
    new Setup\Site();
    new Woo\WooSetup();
    new Woo\ProductSetup();
    new Woo\CartSetup();
}


/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array('templates', 'views');

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * {{ fn('timber_set_product', post) }} in tease-product.twig
 *
 * Without this, some elements of the listed products
 * would show the same information as the first product in the loop.
 * If you see an error like Warning: call_user_func_array()
 * expects parameter 1 to be a valid callback, no array or string given,
 * this is your problem.
 *
 * @param object $post
 * @return void
 */
function timber_set_product($post)
{
    global $product;

    if (is_woocommerce()) {
        $product = wc_get_product($post->ID);
    }
}
