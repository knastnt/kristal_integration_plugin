<?php

//Делаем шорткод для вставки на страницу. Он будет отображать историю заказов
//
//Заказы берутся из куков браузера. Куки пишет javascript в файле wp-content/plugins/kristal_integration/checkout/checkout_tuning.php

function orders_cookies_history_func(){
    echo '<div class="orders_cookies_history">';
    if( isset( $_COOKIE['previousorders'])) {
        $orderIds = explode(',', $_COOKIE['previousorders']);

        $orderIds = array_reverse($orderIds);

        foreach ($orderIds as $orderId) {

            // Get an instance of the WC_Order object (same as before)
            $order = wc_get_order( $orderId );

            if ($order == false) continue;

            ?>

            <div class="single_order">
                <div class="header">
                    <div class="orderid">Заказ № <?php echo $order->get_id(); ?> </div>
                    <div class="orderdate">от <?php echo $order->get_date_created()->date('d.m.Y g:i'); ?> </div>
                    <div class="ordertotal">На сумму: <?php echo $order->get_total() . " " . $order->get_currency(); ?> </div>
                </div>
                <div class="details">
                    <?php
                        $items = $order->get_items();
                        foreach ($items as $item) {
                            //var_dump($item->get_data());
                            ?>
                            <div class="item">
                                <div class="orderdate"><?php echo $item->get_name(); ?> </div>
                                <div class="ordertotal"><?php echo $item->get_data()['total'] . " " . $order->get_currency() . ($item->get_data()['quantity'] == 1 ? "" : " (" . $item->get_data()['quantity'] . " шт.)"); ?> </div>
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


/**
 * Include CSS file for MyPlugin.
 */
function myplugin_scripts() {
    wp_register_style( 'orders-history',  plugin_dir_url( __FILE__ ) . 'orders-history.css' );
    wp_enqueue_style( 'orders-history' );
}
add_action( 'wp_enqueue_scripts', 'myplugin_scripts' );