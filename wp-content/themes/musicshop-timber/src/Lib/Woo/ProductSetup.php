<?php

namespace Theme\Lib\Woo;

class ProductSetup
{
    function __construct()
    {
        add_action('init', [$this, 'removeActions']);
        add_action('init', [$this, 'addActions']);
    }

    function removeActions()
    {
        // remove product thumbnail
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
    }

    function addActions()
    {
        // add custom thumbnail
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'custom_thumbnail']);
    }

    function custom_thumbnail()
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
}
