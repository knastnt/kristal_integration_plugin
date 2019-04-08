<?php

//Делаем шорткод для вставки на страницу. Он будет отображать историю заказов
//
//Заказы берутся из куков браузера. Куки пишет javascript в файле wp-content/plugins/kristal_integration/checkout/checkout_tuning.php

function orders_cookies_history_func(){
    echo '<div class="orders_cookies_history">';
    if( isset( $_COOKIE['previousorders'])) {
        $orderIds = explode(',', $_COOKIE['previousorders']);

        foreach ($orderIds as $orderId) {

            // Get an instance of the WC_Order object (same as before)
            $order = wc_get_order( $orderId );

            if ($order == false) continue;

            ?>

            <div class="single_order">
                <div class="header">
                    <div class="orderid">Заказ № <?php echo $order->get_id(); ?> </div>
                    <div class="orderdate"><?php echo $order->get_date_created(); ?> </div>
                    <div class="ordertotal"><?php echo $order->get_total() . " " . $order->get_currency(); ?> </div>
                </div>
                <div class="details">
                    <?php
                        $items = $order->get_items();
                        foreach ($items as $item) {
                            ?>
                            <div class="item">
                                <div class="orderdate"><?php echo $item->get_name(); ?> </div>
                                <div class="ordertotal"><?php echo $item->get_data()['total']; ?> </div>
                            </div>
                            <?php
                        }
                        ?>
                </div>

            </div>

            <?php
        }

    }else{
        echo "Нет сохраненной истории заказов.";
    }
    echo '</div>';
}
add_shortcode('orders_cookies_history', 'orders_cookies_history_func');