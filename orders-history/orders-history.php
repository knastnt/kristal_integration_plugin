<?php

//Делаем шорткод для вставки на страницу. Он будет отображать историю заказов
//
//Заказы берутся из куков браузера. Куки пишет javascript в файле wp-content/plugins/kristal_integration/checkout/checkout_tuning.php

function orders_cookies_history_func(){
    if( isset( $_COOKIE['previousorders'])) {
        $orderIds = explode(',', $_COOKIE['previousorders']);

        foreach ($orderIds as $orderId) {
            
            // Get an instance of the WC_Order object (same as before)
            $order = wc_get_order( $orderId );

            if ($order == false) continue;

            echo $order->get_id() . "<br>";
            echo $order->get_date_created();

        }

    }else{
        echo "Нет сохраненной истории заказов.";
    }
}
add_shortcode('orders_cookies_history', 'orders_cookies_history_func');