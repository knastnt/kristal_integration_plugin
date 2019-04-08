<?php

//Делаем шорткод для вставки на страницу. Он будет отображать историю заказов
//
//Заказы берутся из куков браузера. Куки пишет javascript в файле wp-content/plugins/kristal_integration/checkout/checkout_tuning.php

function orders_cookies_history_func(){
    return "foo = ";
}
add_shortcode('orders_cookies_history', 'orders_cookies_history_func');