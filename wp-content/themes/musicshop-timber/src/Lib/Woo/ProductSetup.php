<?php

namespace Theme\Lib\Woo;

class ProductSetup
{
    public function __construct()
    {
        add_action('init', [$this, 'removeActions']);
        add_action('init', [$this, 'addActions']);
    }

    public function removeActions()
    {
        /*
        * Removing hooked functions from PRODUCT (within loops)
        */
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash');
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);

        /*
        * Removing hooked functions from SINGLE PRODUCT
        */
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);

        //removes gallery thumbnails
        remove_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20);

        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

        remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

        // remove_action('woocommerce_single_variation', 'woocommerce_single_variation', 10);
        remove_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);
    }

    public function addActions()
    {
        // PRODUCT LOOP
        add_action('woocommerce_before_main_content', 'woocommerce_output_all_notices');
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'custom_thumbnail']);
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'custom_onsale']);

        add_action('woocommerce_after_shop_loop_item_title', [$this, 'custom_price']);

        // SINGLE PRODUCT
        // add title before gallery
        add_action('woocommerce_before_single_product_summary', 'woocommerce_template_single_title');

        // custom hook in single-product.twig
        add_action("musicshop_single_sidebar", 'woocommerce_template_single_add_to_cart');

        // related product added after single product
        // add_action("woocommerce_after_single_product", 'woocommerce_output_related_products');

        add_action('woocommerce_after_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);


        add_action("rest_api_init", function () {
            register_rest_route('musicshop/v1', 'variations/(?P<id>[\d]+)', array(
                'methods' => 'GET',
                'callback' => [$this, 'variations_API_route']
            ));
        });


        // https://stackoverflow.com/questions/24040262/display-variation-price-woocommerce-when-all-prices-are-equal
        add_filter('woocommerce_available_variation', function ($available_variations, \WC_Product_Variable $variable, \WC_Product_Variation $variation) {
            if (empty($available_variations['price_html'])) {
                $available_variations['price_html'] = '<span class="price">' . $variation->get_price_html() . '</span>';
            }

            return $available_variations;
        }, 10, 3);
    }

    public function custom_thumbnail()
    {
        global $product;

        ob_start(); ?>

        <div class="product__thumbnail">
            <?php echo get_the_post_thumbnail($product->id); ?>
        </div>
        
        <?php $output = ob_get_contents();
        ob_end_flush();

        return $output;
    }

    public function custom_price()
    {
        global $product;

        if ($product->is_type('simple')) {
            // simple product
            if ($product->get_regular_price() != 0 && !$product->is_on_sale()) {
                $price = wc_price($product->get_regular_price());
            } elseif ($product->get_regular_price() != 0 && $product->is_on_sale()) {
                $price = wc_price($product->get_sale_price());
            } else {
                $price = __("Price not set");
            }
        }

        if ($product->is_type('variable')) {
            if ($product->get_regular_price() == 0 && !$product->is_on_sale()) {
                $price = wc_price($product->get_variation_regular_price('min'));
            } elseif ($product->get_regular_price() == 0 && $product->is_on_sale()) {
                $price = wc_price($product->get_variation_sale_price('min'));
            } else {
                $price = __("Price not set");
            }
        }

        ob_start(); ?>
        <div class="product__price">
            <span> <?php echo $price; ?> </span>
        </div>
        <?php
        $output = ob_get_contents();
        ob_end_flush();

        return $output;
    }

    public function custom_onsale()
    {
        global $product;

        if ($product->is_on_sale() && $product->get_type() != 'variable') {
            $regularPrice = $product->get_regular_price();
            $salePrice = $product->get_sale_price();
            $percentage = round((($salePrice / $regularPrice) - 1) * 100);


            ob_start(); ?>
            <div class="product__onsale">

                <span class="sale-tag">
                    <?php echo $percentage . '%' ?>
                </span>

            </div>
            
            <?php $output = ob_get_contents();
            ob_end_flush();

            return $output;
        }
    }

    public function variations_API_route($request)
    {
        $id = $request['id'];
        $product = wc_get_product($id);

        $variations = $product->get_available_variations();

        foreach ($variations as $variation) {
            $variationImages[] = array(
                'variation_id' => $variation['variation_id'],
                'sku' => $variation['sku'],
                'attribute_color' => $variation['attributes']['attribute_color'],
                "images" => array(
                    'thumbnails' => get_variation_gallery_images($variation['variation_id']),
                    'large' => get_variation_gallery_images($variation['variation_id'], "large"),
                    'full' => get_variation_gallery_images($variation['variation_id'], "full")
                )
            );
        }

        return $variationImages;
    }
}
