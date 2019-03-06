<?php


// Проверяем включен ли чекбокс - скрыть div class=woocommerce-additional-fields в checkuot. Скрываем содержимое если надо
if ( isset(get_option( 'kristall_options_array' ) ["hide_woocommerce_additional_fields_in_checkout"]) && get_option( 'kristall_options_array' ) ["hide_woocommerce_additional_fields_in_checkout"] == true ) {

    //wp-content/plugins/woocommerce/templates/checkout/form-shipping.php

    add_action( 'woocommerce_before_order_notes', 'open_div' );
    function open_div() {
        ?><div style="display: none"><?php
    }

    add_action( 'woocommerce_after_order_notes', 'close_div' );
    function close_div() {
        ?></div><?php
    }

}