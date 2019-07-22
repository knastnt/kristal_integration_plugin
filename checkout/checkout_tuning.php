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
    //$link = isset($kristall_options_array['redirect_user_to_kristall_url']) ? $kristall_options_array['redirect_user_to_kristall_url'] : 'http://www.kristal-online.ru/api/api.php?data=aplyOrderWc&order_id=%ID%&shopId=%ShopID%';
    $link = (isset($kristall_options_array['kristall_api_url']) ? $kristall_options_array['kristall_api_url'] : 'https://www.kristal-online.ru/api/api.php') . '?data=aplyOrderWc&order_id=%ID%&shopId=%ShopID%';
    $link = str_replace("%ID%", $wp->query_vars['order-received'], $link);

    $shopID = isset($kristall_options_array['shopId']) ? $kristall_options_array['shopId'] : '0';
    $link = str_replace("%ShopID%", $shopID, $link);
    ?>
    <div class="redirect2kristall">
        <p>Через <span id="time" style="font-weight: 700;">10</span> секунд Вы будете перенаправлены в Кристалл для оплаты заказа... &nbsp;Если перенаправления не произошло, нажмите на кнопку ниже</p>

        <script type="text/javascript">

            //Сохраняем прошлые заказы в кукисы:
            function getCookie(name) {
                console.log("get cookie " + name);
                var value = "; " + document.cookie;
                var parts = value.split("; " + name + "=");
                if (parts.length == 2) return parts.pop().split(";").shift();
            }

            var previousorders = getCookie('previousorders');
            console.log("cookie: " + previousorders);
            if (previousorders) {
                console.log("previousorders есть");
                var orders = previousorders.split(",");
                if ( orders.indexOf( '<?php echo $wp->query_vars['order-received']; ?>' ) != -1 ) {
                    //alert('contains');
                    console.log("contains");

                }else{
                    //alert('not contains. add...');
                    console.log("not contains. add...");
                    document.cookie =
                        'previousorders=' + previousorders + ',<?php echo $wp->query_vars['order-received']; ?>; expires=Fri, 3 Aug 2050 00:00:00 UTC; path=/';
                }
            }else{
                //Такого кука вообще нету
                console.log("Такого кука вообще нету");
                document.cookie =
                    'previousorders=<?php echo $wp->query_vars['order-received']; ?>; expires=Fri, 3 Aug 2050 00:00:00 UTC; path=/';
            }


            //Счётчик:
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


//Переименовываем Детали оплаты во Выберите, кого Вы представляете
function rog_billing_details( $translated_text, $text, $domain ) {

    if( 'woocommerce' === $domain ) {

        switch ( $translated_text ) {
            case "Детали оплаты" :
                $translated_text = "Выберите, кого Вы представляете";
                break;
        }

    }

    return $translated_text;
}
add_filter( 'gettext', 'rog_billing_details', 20, 3 );









/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Добавляем чекбокс публичной оферты при оформлении заказа
///
/// нужно создать страничку с договором по адресу /contract-offer

//https://businessbloomer.com/woocommerce-additional-acceptance-checkbox-checkout/

/**
 * @snippet       Add contract offer tick box at checkout
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=19854
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.3.4
 */

add_action( 'woocommerce_review_order_before_submit', 'bbloomer_add_checkout_contract_offer', 9 );

function bbloomer_add_checkout_contract_offer() {

    woocommerce_form_field( 'contract_offer', array(
        'type'          => 'checkbox',
        'class'         => array('form-row offer'),
        'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
        'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
        'required'      => true,
        'label'         => 'Я согласен с условиями <a href="/contract-offer" target="_blank">Договора оферты</a>',
    ));

}

// Show notice if customer does not tick

add_action( 'woocommerce_checkout_process', 'bbloomer_not_approved_offer' );

function bbloomer_not_approved_offer() {
    if ( ! (int) isset( $_POST['contract_offer'] ) ) {
        wc_add_notice( __( 'Для продолжения необходимо принять условия договора оферты' ), 'error' );
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////