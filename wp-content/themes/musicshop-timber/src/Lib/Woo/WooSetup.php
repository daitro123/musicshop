<?php

namespace Theme\Lib\Woo;

class WooSetup
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this,'dequeue_select2_selectwoo'], 100);
    }

    public function dequeue_select2_selectwoo()
    {
        if (class_exists('woocommerce')) {
            wp_dequeue_style('select2');
            wp_deregister_style('select2');
    
            wp_dequeue_script('selectWoo');
            wp_deregister_script('selectWoo');
        }
    }
}
