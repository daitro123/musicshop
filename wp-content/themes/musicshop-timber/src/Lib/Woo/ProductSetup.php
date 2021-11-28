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
        // remove product thumbnail
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);

        //removes gallery thumbnails
        // remove_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20);

        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    }

    public function addActions()
    {
        // add custom thumbnail
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'custom_thumbnail']);
        add_action('woocommerce_before_single_product_summary', 'woocommerce_template_single_title');
        add_action('woocommerce_before_single_product_summary', [$this, 'custom_single_gallery']);
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

    public function custom_single_gallery()
    {
        global $product;

        $columns           = apply_filters('woocommerce_product_thumbnails_columns', 4);
        $post_thumbnail_id = $product->get_image_id();
        $wrapper_classes   = apply_filters(
            'woocommerce_single_product_image_gallery_classes',
            array(
                'woocommerce-product-gallery',
                'woocommerce-product-gallery--' . ($post_thumbnail_id ? 'with-images' : 'without-images'),
                'woocommerce-product-gallery--columns-' . absint($columns),
                'images',
            )
        );
    ?>
        <div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>" data-columns="<?php echo esc_attr($columns); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
            <figure class="woocommerce-product-gallery__wrapper">
                <?php
                do_action('woocommerce_product_thumbnails');
                ?>
            </figure>
        </div>
<?php
    }
}
