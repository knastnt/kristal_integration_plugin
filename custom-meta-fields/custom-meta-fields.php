<?php

// https://catapultthemes.com/add-custom-fields-woocommerce-product/
// https://wpruse.ru/woocommerce/custom-fields-in-products/


// Метачекбокс, дефолтное значение и описание
const allow_client_types_paramNames = array(
    'single_in_cart' => [0, 'В корзине может быть только этот товар'],
    'allow_client_types_fiz' => [1, 'Разрешить покупать физическим лицам'],
    'allow_client_types_ip' => [1, 'Разрешить покупать индивидуальным предпринимателям'],
    'allow_client_types_yur' => [1, 'Разрешить покупать юридическим лицам']
);

 
/**
 * Display the custom text fields
 * @since 1.0.0
 */
function kristall_create_custom_fields() {
	global $post;
	 
	echo '<div class="kristall_options_group">';// Группировка полей 
	
	// Товар или услуга
	 $args = array(
	 'id' => 'is_service',
	 'label' => __( 'Тип', 'kristall' ),
	 'class' => 'kristall-custom-radio',
	 'style'         => 'margin-left: 35px;',
	 'options'       => array(
		  '0'   => 'Товар',
		  '1'   => 'Услуга',
	   ),
	 'value' => get_post_meta( $post->ID, 'is_service', true ) == '' ? '0' : get_post_meta( $post->ID, 'is_service', true ),
	 );
	 woocommerce_wp_radio( $args );
		
	// Страна-производитель	
	 $args = array(
	 'id' => 'country',
	 'label' => __( 'Страна-производитель', 'kristall' ),
	 'class' => 'kristall-custom-field',
	 'desc_tip' => true,
	 'description' => __( 'Вводится свободным текстом', 'kristall' ),
	 );
	 woocommerce_wp_text_input( $args );
	 
	// Единица измерения
	 $args = array(
	 'id' => 'unit',
	 'label' => __( 'Единица измерения (сокращенно)', 'kristall' ),
	 'class' => 'kristall-custom-field',
	 );
	 woocommerce_wp_text_input( $args );
	 
	 // Номер таможенной декларации
	 $args = array(
	 'id' => 'customs_declaration',
	 'label' => __( 'Номер таможенной декларации', 'kristall' ),
	 'class' => 'kristall-custom-field',
	 );
	 woocommerce_wp_text_input( $args );


	 // Отрисовываем чекбоксы для метаполей.
     // В корзине может быть только этот товар
     // Только для физ.лиц. (нельзя покупать ЮРикам и ИПэшникам)
     // Разрешать покупать ФИЗикам
     // Разрешать покупать ИПшникам
     // Разрешать покупать ЮРикам
     foreach (allow_client_types_paramNames as $key => $description) {
        $args = array(
            'id' => $key,
            'label' => __( $description[1], 'kristall' ),
            'class' => 'kristall-custom-checkbox',
            'value' => get_post_meta( $post->ID, $key, true ) == 1 ? 'yes' : get_post_meta( $post->ID, $key, true ),
        );
        woocommerce_wp_checkbox( $args );
     }

 
	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'kristall_create_custom_fields' );




/**
 * Save the custom fields
 * @since 1.0.0
 */
function kristall_save_custom_field( $post_id ) {
	 $product = wc_get_product( $post_id );
	 
	 $is_service = isset( $_POST['is_service'] ) ? $_POST['is_service'] : '';
	 $country = isset( $_POST['country'] ) ? $_POST['country'] : '';
	 $customs_declaration = isset( $_POST['customs_declaration'] ) ? $_POST['customs_declaration'] : '';
	 $unit = isset( $_POST['unit'] ) ? $_POST['unit'] : '';

     $product->update_meta_data( 'is_service', sanitize_text_field( $is_service ) );
     $product->update_meta_data( 'country', sanitize_text_field( $country ) );
     $product->update_meta_data( 'customs_declaration', sanitize_text_field( $customs_declaration ) );
     $product->update_meta_data( 'unit', sanitize_text_field( $unit ) );




	 //Устанавливаем значения метаполей при сохранении товара
     foreach (allow_client_types_paramNames as $key => $description) {

         //устанавливаем значения по умолчанию, если таковых ещё не имеется
         $paramValue = (int)(isset($_POST[$key]) && ($_POST[$key]=='yes' || $_POST[$key]==1));
         $product->update_meta_data( $key, sanitize_text_field( $paramValue ) );
     }


	 $product->save();
}
add_action( 'woocommerce_process_product_meta', 'kristall_save_custom_field' );


