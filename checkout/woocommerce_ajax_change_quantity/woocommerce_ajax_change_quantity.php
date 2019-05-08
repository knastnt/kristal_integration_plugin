<?php
/*
к этому функционалу относится файл
ПАПКА_ТЕМЫ\woocommerce\global\quantity-input.php

в нем задается создание кнопок +/-
*/

//подмена core js в woocommerce для ajax автообновления корзины при изменении количества товаров
add_action( 'wp_enqueue_scripts', 'load_theme_scripts', 100 );

    function load_theme_scripts() {
        global $wp_scripts; 
        $wp_scripts->registered[ 'wc-cart' ]->src = plugins_url('/js/cart.js', __FILE__);
    }


 ?>