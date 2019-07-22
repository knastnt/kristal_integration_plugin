<?php

//Шорткоды заполнения блоков Торговая площадка, Вебинары, Частные объявления

function get_kristal_trade_place_func(){


}
add_shortcode('kristal_trade_place', 'get_kristal_trade_place_func');


function get_kristal_vebinars_func(){


}
add_shortcode('kristal_vebinars', 'get_kristal_vebinars_func');


function get_kristal_private_ads_func( $atts ){
    $kristall_options_array = get_option('kristall_options_array');
    $apiLink = isset($kristall_options_array['kristall_api_url']) ? $kristall_options_array['kristall_api_url'] : 'https://www.kristal-online.ru/api/api.php';


    $apiLink .= '?data=getObData';

    if (isset($atts['count']) && intval($atts['count']) > 0) {
        $apiLink .= '&count=' . intval($atts['count']);
    }
    if (isset($atts['type']) && strtolower($atts['type']) == 'all') {
        $apiLink .= '&type=all';
    }

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {

            // Запустим ajax-запрос, установим обработчики его выполнения и
            // сохраним объект jqxhr данного запроса для дальнейшего использования.
            var jqxhr = jQuery.getJSON("<?php echo $apiLink; ?>")
                .success(function() { console.log("Успешное выполнение"); })
                .error(function() { console.log("Ошибка выполнения. Возможно, Ваш запрос кроссдоменный - см. CORS и Access-Control-Allow-Origin"); })
                .complete(function() { /*alert("Завершение выполнения");*/ });



        });
    </script>
    <?php

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