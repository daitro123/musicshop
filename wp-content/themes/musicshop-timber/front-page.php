<?php

use Timber\Timber;
use Timber\Post;

$context = Timber::context();

$context['post'] = new Post();

// https://stackoverflow.com/questions/46246614/get-woocommerce-featured-products-in-a-wp-query
$taxquery[] = array(
    'taxonomy' => 'product_visibility',
    'field'    => 'name',
    'terms'    => 'featured',
    'operator' => 'IN', // or 'NOT IN' to exclude feature products
);

$featured = Timber::get_posts(array(
    'post_type'           => 'product',
    'post_status'         => 'publish',
    'posts_per_page'      => 2,
    'tax_query'           => $taxquery
));

$context['featured'] = $featured;

Timber::render(array('views/front-page.twig', 'page.twig'), $context);
