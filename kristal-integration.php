<?php
/*
 * Plugin Name: Кристалл интеграция
 * Description: Дополнение к woocommerce, позволяющее осуществлять продажу Ваших услуг из личного кабинера Кристалл
 * Plugin URI:  po365.ru
 * Version:     0.1
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