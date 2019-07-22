<?php

function get_kristal_api_url()
{
    $kristall_options_array = get_option('kristall_options_array');
    $apiLink = isset($kristall_options_array['kristall_api_url']) ? $kristall_options_array['kristall_api_url'] : 'https://www.kristal-online.ru/api/api.php';
    return $apiLink;
}

function extract_domain_from_url( $url )
{
    $parsed_url = parse_url($url);
    $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    return $scheme . $host . $port;
}


//Шорткоды заполнения блоков Торговая площадка, Вебинары, Частные объявления

function get_kristal_trade_place_func( $atts ){

    ob_start();

    //Адрес API кристала
    $kristalApiUrl = get_kristal_api_url();

    //Извлекаем адрес домена из адреса API
    $kristalHomeUrl = extract_domain_from_url($kristalApiUrl);

    //URL запроса
    $requestUrl = $kristalApiUrl . '?data=getBirzaData';
    if (isset($atts['count']) && intval($atts['count']) > 0) {
        $requestUrl .= '&count=' . intval($atts['count']);
    }

    ?>

    <div id="kristal_trade_place">

    </div>

    <script type="text/javascript">
        jQuery(document).ready(function() {

            //Проверяем есть ли блок id = kristal_trade_place
            if( jQuery("#kristal_trade_place").length ) {

                // Запустим ajax-запрос, установим обработчики его выполнения
                jQuery.getJSON("<?php echo $requestUrl; ?>")
                    .success(function (data) {

                        var html = "";

                        for (var item in data) {
                            var entry = data[item];

                            html += '<div class = "block_entry">';

                            html += '<div class = "head">' + entry['startTime'] + '</div>';
                            html += '<div class = "text">' + entry['uslName'] + '</div>';
                            html += '<div class = "text">' + entry['description'] + '</div>';
                            html += '<div class = "text">' + entry['status'] + '</div>';

                            html += '</div>';

                            console.log(entry);
                        }

                        jQuery("#kristal_trade_place").html(html);
                        //console.log("Успешное выполнение");
                    })
                    .error(function () {
                        console.log("Ошибка AJAX запроса элемента kristal_private_ads. Возможно, Ваш запрос кроссдоменный - гугли CORS и Access-Control-Allow-Origin");
                    })
                    .complete(function () { /*alert("Завершение выполнения");*/
                    });
            }

        });
    </script>
    <?php

    return ob_get_clean();

}
add_shortcode('kristal_trade_place', 'get_kristal_trade_place_func');


function get_kristal_vebinars_func( $atts ){

    ob_start();
    return ob_get_clean();

}
add_shortcode('kristal_vebinars', 'get_kristal_vebinars_func');


function get_kristal_private_ads_func( $atts ){

    ob_start();

    //Адрес API кристала
    $kristalApiUrl = get_kristal_api_url();

    //Извлекаем адрес домена из адреса API
    $kristalHomeUrl = extract_domain_from_url($kristalApiUrl);

    //URL запроса
    $requestUrl = $kristalApiUrl . '?data=getObData';
    if (isset($atts['count']) && intval($atts['count']) > 0) {
        $requestUrl .= '&count=' . intval($atts['count']);
    }
    if (isset($atts['type']) && strtolower($atts['type']) == 'all') {
        $requestUrl .= '&type=all';
    }

    ?>

    <div id="kristal_private_ads">

    </div>

    <script type="text/javascript">
        jQuery(document).ready(function() {

            //Проверяем есть ли блок id = kristal_private_ads
            if( jQuery("#kristal_private_ads").length ) {

                // Запустим ajax-запрос, установим обработчики его выполнения
                jQuery.getJSON("<?php echo $requestUrl; ?>")
                    .success(function (data) {

                        var html = "";

                        for (var item in data) {
                            var entry = data[item];

                            html += '<div class = "block_entry"><a href="<?php echo $kristalHomeUrl; ?>' + entry['link'] + '">';

                            html += '<div class = "head">' + entry['head'] + '</div>';
                            html += '<div class = "text">' + entry['text'] + '</div>';

                            html += '</a></div>';

                            //console.log(entry);
                        }

                        jQuery("#kristal_private_ads").html(html);
                        //console.log("Успешное выполнение");
                    })
                    .error(function () {
                        console.log("Ошибка AJAX запроса элемента kristal_private_ads. Возможно, Ваш запрос кроссдоменный - гугли CORS и Access-Control-Allow-Origin");
                    })
                    .complete(function () { /*alert("Завершение выполнения");*/
                    });
            }

        });
    </script>
    <?php

    return ob_get_clean();

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