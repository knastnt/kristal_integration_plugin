<?php


// Проверяем включен ли чекбокс настроек плагина - скрыть div class=woocommerce-additional-fields в checkuot. Скрываем содержимое если надо
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

            #payment .place-order {
                margin-top: 0;
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

            .woocommerce-order h2.woocommerce-column__title{
                display: none;
            }

            .woocommerce-order > p:not(.woocommerce-notice) {
                display: none;
            }
        </style>
        <?php
        return $content;
    }
}


//создаем и выводим логику перенаправления в кристалл
//wp-content/plugins/woocommerce/templates/checkout/thankyou.php
add_filter( 'woocommerce_thankyou_order_received_text', 'generate_kristall_redirect_content' );
function generate_kristall_redirect_content($content ){
    echo $content;

    global $wp;
    //будем оборачивать button в a с отменой действия onclick
    //такая хрень потому что если делать через form action, то, в случае если кристалл на http,
    //браузер спросит пользователя - уверин ли он что разрешает передачу данных на незащищенный
    //сайт и то что их могут перехватить. в вопросе оплаты это стремно.
    //Да, это ху*во, но кристалл покачто на http :(((

    //Генерируем ссылку
    $kristall_options_array = get_option('kristall_options_array');
    $link = isset($kristall_options_array['redirect_user_to_kristall_url']) ? $kristall_options_array['redirect_user_to_kristall_url'] : 'http://www.kristal-online.ru/api/api.php?data=aplyOrderWc&order_id=%ID%&shopId=%ShopID%';
    $link = str_replace("%ID%", $wp->query_vars['order-received'], $link);

    $shopID = isset($kristall_options_array['shopId']) ? $kristall_options_array['shopId'] : '0';
    $link = str_replace("%ShopID%", $shopID, $link);
    ?>
    <div class="redirect2kristall">
        <p>Через <span id="time" style="font-weight: 700;">10</span> секунд Вы будете перенаправлены в Кристалл для оплаты заказа... &nbsp;Если перенаправления не произошло, нажмите на кнопку ниже</p>

        <script type="text/javascript">
            var i = 10;//время в сек.
            function time(){
                document.getElementById("time").innerHTML = i;//визуальный счетчик
                i--;//уменьшение счетчика
                if (i < 0) {
                    clearInterval(myTimer);
                    location.href = "<?php echo $link; ?>";
                }//редирект
            }
            time();
            var myTimer = setInterval(time, 1000);
        </script>

        <a href="<?php echo $link; ?>">
            <button onlick="return false;" type="submit" class="single_add_to_cart_button button alt">Перейти в Кристалл для оплаты</button>
        </a>
    </div>
    <?php
}
