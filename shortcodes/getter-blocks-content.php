<?php

//Шорткоды заполнения блоков Торговая площадка, Вебинары, Частные объявления

function get_kristal_trade_place_func(){


}
add_shortcode('kristal_trade_place', 'get_kristal_trade_place_func');


function get_kristal_vebinars_func(){


}
add_shortcode('kristal_vebinars', 'get_kristal_vebinars_func');


function get_kristal_private_ads_func(){


}
add_shortcode('kristal_private_ads', 'get_kristal_private_ads_func');





/**
 * Include CSS file
 *
function myplugin_scripts() {
    wp_register_style( 'orders-history',  plugin_dir_url( __FILE__ ) . 'orders-history.css' );
    wp_enqueue_style( 'orders-history' );
}
add_action( 'wp_enqueue_scripts', 'myplugin_scripts' );
*/