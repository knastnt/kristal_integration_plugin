<?php
/*
 * Plugin Name: Кристалл интеграция
 * Description: Дополнение к woocommerce, позволяющее осуществлять продажу Ваших услуг из личного кабинера Кристалл
 * Plugin URI:  po365.ru
 * Version:     2020.07.05
 */
 
 
// страница настроек плагина
require_once( plugin_dir_path(__FILE__ ) . '/options/options-page.php' ); 

// кастомные метаполя в настройках товара
require_once( plugin_dir_path(__FILE__ ) . '/custom-meta-fields/custom-meta-fields.php' );

// отправка новых ордеров в кристалл
require_once( plugin_dir_path(__FILE__ ) . '/send_new_orders_to_kristall/send_new_orders_to_kristall.php' );
 
// убираем ненужные поля на странице оформления заказа
require_once( plugin_dir_path(__FILE__ ) . '/views/remove_some_fields_on_checkout/remove_some_fields_on_checkout.php' );
 
// валидация телефона при оформлении заказа
require_once( plugin_dir_path(__FILE__ ) . '/views/validate_billing_phone_number/validate_billing_phone_number.php' );

// кастомные метаполя в отображении товара на странице
require_once( plugin_dir_path(__FILE__ ) . '/views/custom-meta-fields-view/custom-meta-fields-view.php' );

// скрыть товары дочерних категорий на странице родительской категории. и развернуть содерджимое Uncategirized
require_once( plugin_dir_path(__FILE__ ) . '/views/categories_and_products/categories_and_products.php' );

// кастомизация страницы оформления заказа
//скрытие ненужных полей
require_once( plugin_dir_path(__FILE__ ) . '/checkout/checkout_tuning.php' );
//функция физ.лицо / организация
require_once( plugin_dir_path(__FILE__ ) . '/checkout/fizlico_vs_organizaciya.php' );

// добавление +/- для количества товаров в корзине и автообновление
require_once( plugin_dir_path(__FILE__ ) . '/checkout/woocommerce_ajax_change_quantity/woocommerce_ajax_change_quantity.php' );


//шорткод для отображения истории заказов из кукисов
require_once( plugin_dir_path(__FILE__ ) . '/shortcodes/orders-history.php' );

//Шорткоды заполнения блоков Торговая площадка, Вебинары, Частные объявления
require_once( plugin_dir_path(__FILE__ ) . '/shortcodes/getter-blocks-content.php' );



//Функция вызываемая при активации плагина в Wordpress
register_activation_hook( __FILE__, function(){

    //Получаем все товары и для тех у кого only_for_fiz_lico, устанавливаем соответствующие allow_client_types_...
    $args = array(
        'post_type'      => 'product'
    );

    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) {
        $loop->the_post();
        global $product;
        $is_only_for_fiz_lico = (bool)$product->get_meta( 'only_for_fiz_lico' );


        if ($is_only_for_fiz_lico) {
            //не работает ни так, ни так. Странно, ну да ладно
//            $product->update_meta_data( 'allow_client_types_fiz', 1 );
//            $product->update_meta_data( 'allow_client_types_ip', 0 );
//            $product->update_meta_data( 'allow_client_types_yur', 0 );
//            $product->set_meta_data(array(
//                'allow_client_types_fiz'=>1,
//                'allow_client_types_ip'=>0,
//                'allow_client_types_yur'=>0
//                ));
            //Вот так - работает
            update_post_meta( $product->get_id(), 'allow_client_types_fiz', 1 );
            update_post_meta( $product->get_id(), 'allow_client_types_ip', 0 );
            update_post_meta( $product->get_id(), 'allow_client_types_yur', 0 );
        }


    }

//    wp_reset_query();

} );