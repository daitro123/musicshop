<?php

namespace Theme\Lib\Woo;

class CartSetup
{
    public function __construct()
    {
        add_action('init', [$this, 'removeActions']);
        add_action('init', [$this, 'addActions']);
        add_action('init', [$this, 'filters']);
        $this->addFragments();
    }

    public function filters()
    {
        add_filter('woocommerce_cart_item_removed_notice_type', '__return_null');
    }

    public function removeActions()
    {
    }

    public function addActions()
    {
    }

    public function addFragments()
    {
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'wc_cart_counter_fragment']);
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'wc_cart_toast_fragment']);
    }

    public function wc_cart_counter_fragment($fragments)
    {
        global $woocommerce;

        ob_start(); ?>
        <span class="cart-icon__count <?php echo $woocommerce->cart->cart_contents_count == 0 ? "cart-icon__count--empty" : ""; ?>"><?php echo $woocommerce->cart->cart_contents_count ?></span>
        
        <?php $fragments['span.cart-icon__count'] = ob_get_clean();
        return $fragments;
    }

    public function wc_cart_toast_fragment($fragments)
    {
        global $woocommerce;

        $items = $woocommerce->cart->get_cart();


        if ($items) {
            $product = end($items)['data'];

            ob_start(); ?>
            <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="6000">
                <div class="toast-header bg-primary">
                    <img src="<?php echo get_theme_file_uri(); ?>/assets/icons/cart-empty.png" class="rounded mr-2" height="16" width="16" alt="cart">
                    <strong class="mr-auto text-dark pr-2">Added to cart</strong>
                    <small class="text-dark">just now</small>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body p-3">
                    <div class="toast-image">
                        <?php echo wp_get_attachment_image($product->get_image_id()); ?>
                    </div>
                    <div class="toast-message">
                        <h6><?php echo $product->name; ?></h6>
                        <p class="toast-price"><?php echo wc_price($product->price); ?></p>
                    </div>

                </div>
            </div>

            <?php $fragments['div#cartToast'] = ob_get_clean();
        }

        return $fragments;
    }
}
