<?php

// https://wp-kama.ru/id_3773/api-optsiy-nastroek.html

/**
 * Регистрируем страницу настроек плагина
 */
add_action('admin_menu', 'add_plugin_page');
function add_plugin_page(){
	add_options_page( 'Кристалл интеграция. Настройки.', 'Кристалл интеграция', 'manage_options', 'kristal_integration', 'kristall_options_page_output' );
}

function kristall_options_page_output(){
	?>
	<div class="wrap">
		<h2><?php echo get_admin_page_title() ?></h2>

		<form action="options.php" method="POST">
			<?php
				// скрытые защитные поля
				settings_fields( 'option_group' );     
				
				// Секции с настройками (опциями). (section_id_1, ...)
				do_settings_sections( 'kristall_page' );
				
				//Кнопка сохранить
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Регистрируем настройки.
 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
 */
add_action('admin_init', 'plugin_settings');
function plugin_settings(){
	// параметры: $option_group, $kristall_options_array, $sanitize_callback
	register_setting( 'option_group', 'kristall_options_array', 'sanitize_callback' );

	/*// параметры: $id, $title, $callback, $page
	add_settings_section( 'section_id_1', 'Основные настройки', '', 'kristall_page' ); 

	// параметры: $id, $title, $callback, $page, $section, $args
	add_settings_field('primer_field1', 'Название опции', 'fill_primer_field1', 'kristall_page', 'section_id_1' );
	add_settings_field('primer_field2', 'Другая опция', 'fill_primer_field2', 'kristall_page', 'section_id_1' );*/
	
	
	// Раздел
	add_settings_section( 'section_id_2', 'Отправка новых заказов в кристалл', '', 'kristall_page' ); 
	
	// Чекбокс отправлять корзину
	add_settings_field('send_new_orders_to_kristall', 'Отправлять новые заказы в кристалл', 'fill_send_new_orders_to_kristall', 'kristall_page', 'section_id_2' );
	
	// URL для отправки POST
	add_settings_field('send_new_orders_to_kristall_url', 'Адрес для отправки POST', 'fill_send_new_orders_to_kristall_url', 'kristall_page', 'section_id_2' );



    // Раздел Настройка checkuot
    add_settings_section( 'section_id_3', 'Настройка checkuot', '', 'kristall_page' );

    // Чекбокс скрыть div class=woocommerce-additional-fields в checkuot
    add_settings_field('hide_woocommerce_additional_fields_in_checkout', 'Скрыть контейнер Детали (woocommerce-additional-fields)', 'fill_hide_woocommerce_additional_fields_in_checkout', 'kristall_page', 'section_id_3' );

}

/*## Заполняем опцию 1
function fill_primer_field1(){
	$val = get_option('kristall_options_array');
	$val = isset($val['input']) ? $val['input'] : null;
	?>
	<input type="text" name="kristall_options_array[input]" value="<?php echo esc_attr( $val ) ?>" />
	<?php
}

## Заполняем опцию 2
function fill_primer_field2(){
	$val = get_option('kristall_options_array');
	$val = isset($val['checkbox']) ? $val['checkbox'] : null;
	?>
	<label><input type="checkbox" name="kristall_options_array[checkbox]" value="1" <?php checked( 1, $val ) ?> /> отметить</label>
	<?php
}*/

## Заполняем опцию Отправлять новые заказы в кристалл
function fill_send_new_orders_to_kristall(){
	$val = get_option('kristall_options_array');
	$val = isset($val['send_new_orders_to_kristall']) ? $val['send_new_orders_to_kristall'] : 0;
	?>
	<label><input type="checkbox" name="kristall_options_array[send_new_orders_to_kristall]" value="1" <?php checked( 1, $val ) ?> /> отправлять</label>
	<?php
}

## Заполняем опцию Адрес для отправки POST
function fill_send_new_orders_to_kristall_url(){
	$val = get_option('kristall_options_array');
	$val = isset($val['send_new_orders_to_kristall_url']) ? $val['send_new_orders_to_kristall_url'] : 'http://kristal-online.ru/wordpress-integration.php';
	?>
	<input type="text" name="kristall_options_array[send_new_orders_to_kristall_url]" value="<?php echo esc_attr( $val ) ?>" style="width: 30%;" />
	<?php
}





## Заполняем опцию скрыть div class=woocommerce-additional-fields в checkuot
function fill_hide_woocommerce_additional_fields_in_checkout(){
    $val = get_option('kristall_options_array');
    $val = isset($val['hide_woocommerce_additional_fields_in_checkout']) ? $val['hide_woocommerce_additional_fields_in_checkout'] : 0;
    ?>
    <label><input type="checkbox" name="kristall_options_array[hide_woocommerce_additional_fields_in_checkout]" value="1" <?php checked( 1, $val ) ?> /> скрыть</label>
    <?php
}

## Очистка данных
function sanitize_callback( $options ){ 
	// очищаем
	foreach( $options as $name => & $val ){
		/*if( $name == 'input' )
			$val = strip_tags( $val );

		if( $name == 'checkbox' )
			$val = intval( $val );*/
		
		if( $name == 'send_new_orders_to_kristall' ){
			$val = intval( $val );
		}
		
		if( $name == 'send_new_orders_to_kristall_url' ){
			//$val = intval( $val );
		}



        if( $name == 'hide_woocommerce_additional_fields_in_checkout' ){
            $val = intval( $val );
        }
	}

	//die(print_r( $options )); // Array ( [input] => aaaa [checkbox] => 1 )

	return $options;
}