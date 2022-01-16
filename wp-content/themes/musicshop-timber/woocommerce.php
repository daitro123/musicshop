<?php

use Theme\Lib\Woo\CartSetup;

$context            = Timber::context();
$context['sidebar'] = Timber::get_widgets('shop-sidebar');

if (is_singular('product')) {
    $context['post']    = Timber::get_post();
    $product            = wc_get_product($context['post']->ID);
    $context['product'] = $product;
    $context['price_html'] = $product->get_price_html();
    $context['isVariable'] = $product->get_type() === 'variable';
    $context['sku'] = $product->get_sku();
    $context['isOnSale'] = $product->is_on_sale();

    // Preparing gallery images
    $gallery_ids = $product->get_gallery_image_ids();
    $gallery = [];
    foreach ($gallery_ids as $id) {
        $gallery[] = new Timber\Image($id);
    };

    if (count($gallery) < 2) {
        $gallery[] = new Timber\Image($product->get_image_id());
    }

    $context['images'] = $gallery;

    // Get cart contents
    $context['cartItems'] = CartSetup::getCartItems();
    $context['cartTotal'] = WC()->cart->get_total();


    // Get related products
    $related_limit               = wc_get_loop_prop('columns');
    $related_ids                 = wc_get_related_products($context['post']->id, $related_limit);
    $context['related_products'] =  Timber::get_posts($related_ids);

    // Restore the context and loop back to the main query loop.
    wp_reset_postdata();

    Timber::render('views/woo/single-product.twig', $context);
} else {
    $posts = Timber::get_posts();
    $context['products'] = $posts;

    if (is_product_category()) {
        $queried_object = get_queried_object();
        $term_id = $queried_object->term_id;
        $context['category'] = get_term($term_id, 'product_cat');
        $context['title'] = single_term_title('', false);
    }

    Timber::render('views/woo/archive.twig', $context);
}
