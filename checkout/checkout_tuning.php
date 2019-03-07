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



    //подключаем css для скрытия #payment.woocommerce-checkout-payment ul выбора способа оплаты
    add_action( 'woocommerce_checkout_order_review', 'hiding_in_woocommerce_checkout_payment', 19 );

    function hiding_in_woocommerce_checkout_payment(){
        ?>
        <style>
            #payment ul{
                display: none;
            }
        </style>
        <?php
    }



    //подключаем css для скрытия способа оплаты в чеке, а также вызываем функцию для создания перенаправления в Кристалл
    //wp-content/plugins/woocommerce/templates/checkout/thankyou.php
    add_filter( 'woocommerce_thankyou_order_received_text', 'hiding_pay_method_and_change_text' );

    function hiding_pay_method_and_change_text($content){
        ?>
        <style>
            .woocommerce-order ul li.woocommerce-order-overview__payment-method{
                display: none;
            }
            .woocommerce-order ul li.woocommerce-order-overview__total{
                border: 0;
            }
        </style>
        <?php
        echo $content;
        generate_kristall_redirect_content();
        return "";
    }


    //создаем и выводим логику перенаправления в кристалл
    function generate_kristall_redirect_content(){
        ?>
        <div>Спасибочки :)</div>
        <?php
    }
}