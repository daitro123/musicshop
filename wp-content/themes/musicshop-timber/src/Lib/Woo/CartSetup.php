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
        // add_filter('woocommerce_cart_item_removed_notice_type', '__return_null');
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
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'wc_cart_hover_fragment']);
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

    public function wc_cart_hover_fragment($fragments)
    {
        $items = self::getCartItems();

        ob_start(); ?>
                <div class="cart-hover-box" id="cart-hover-box">
                    <?php if ($items) : ?>
                        <ul class="cart-hover-box__item-list">
                            <?php foreach ($items as $item) { ?> 
                                <li class="item">
                                    <div class="item__image">
                                        <img src="<?php echo $item['thumbnail_url']; ?>" alt="<?php echo $item['item_name']; ?>">
                                    </div>
                                    <div class="item__description">
                                        <p class="item__title">
                                            <?php echo $item['item_name']; ?>
                                        </p>
                                        <p class="item__price-quantity">
                                            <span class="item__quantity"><?php echo $item['quantity'] ?></span>
                                            <span>
                                                &nbsp;x&nbsp;
                                            </span>
                                            <span class="item__price"><?php echo $item['price'] ?></span>
                                        </p>
                                    </div>
                                    <a class="item__close" href="<?php echo $item['remove_item_url']; ?>" aria-label="<?php _e('Remove this item', 'woocommerce'); ?>" data-product_id="<?php echo $item['item_id']; ?>" data-product_sku="<?php echo $item['item_sku']; ?>" >
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewbox="0 0 16 16">
                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                        </span>
                                    </a>
                                </li>
                            <?php }; ?>
                        </ul>
                        <div class="cart-hover-box__subtotal">
                            <span>
                                <strong>Subtotal:
                                </strong>
                            </span>
                            <span class="subtotal__value"><?php echo WC()->cart->get_total(); ?></span>
                        </div>
                        <div class="cart-hover-box__links">
                            <a href="/cart" class="btn btn--primary">CART</a>
                            <a href="/checkout" class="btn btn--dark">CHECKOUT</a>
                        </div>
                    <?php else : ?>
                        <p class="empty-cart">No items in cart</p>
                    <?php endif ; ?>
                </div>

            <?php $fragments['div#cart-hover-box'] = ob_get_clean();

        return $fragments;
    }

    public static function getCartItems()
    {
        $cart_items = [];
        foreach (WC()->cart->get_cart() as $cart_item) {
            array_push($cart_items, [
                'item_id' => $cart_item['data']->get_id(),
                'item_sku' => $cart_item['data']->get_sku(),
                'item_name' => $cart_item['data']->get_title(),
                'quantity' => $cart_item['quantity'],
                'price' => wc_price($cart_item['data']->get_price()),
                'thumbnail_url' => wp_get_attachment_image_src($cart_item['data']->get_image_id(), 'thumbnail')[0],
                'cart_item_key' => $cart_item['key'],
                'remove_item_url' => wc_get_cart_remove_url($cart_item['key'])
            ]);
        }

        return $cart_items;
    }
}
