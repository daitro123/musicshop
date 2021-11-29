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
    }

    public function addActions()
    {
        // add custom thumbnail
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'custom_thumbnail']);
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'custom_onsale']);

        // add title before gallery
        add_action('woocommerce_before_single_product_summary', 'woocommerce_template_single_title');

        // custom hook in single-product.twig
        add_action("musicshop_single_sidebar", 'woocommerce_template_single_add_to_cart');

        // related product added after single product
        add_action("woocommerce_after_single_product", 'woocommerce_output_related_products');
    }

    public function custom_thumbnail()
    {
        global $product;

        ob_start();
?>
        <div class="product__thumbnail">
            <?php echo get_the_post_thumbnail($product->id); ?>
        </div>
    <?php
        $output = ob_get_contents();
        ob_end_flush();

        return $output;
    }

    public function custom_onsale()
    {
        global $product;

        $regularPrice = $product->get_regular_price();
        $salePrice = $product->get_sale_price();
        $percentage = round((($salePrice / $regularPrice) - 1) * 100);

        ob_start();
    ?>
        <?php if ($product->is_on_sale()) : ?>
            <div class="product__onsale">

                <span class="sale-tag">
                    <?php echo $percentage . '%' ?>
                </span>

            </div>
        <?php endif; ?>
<?php
        $output = ob_get_contents();
        ob_end_flush();

        return $output;
    }
}
