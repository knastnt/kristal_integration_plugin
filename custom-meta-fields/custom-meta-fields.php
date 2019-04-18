<?php

// https://catapultthemes.com/add-custom-fields-woocommerce-product/
// https://wpruse.ru/woocommerce/custom-fields-in-products/


 
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
	  
	 // В корзине может быть только этот товар
	 $args = array(
	 'id' => 'single_in_cart',
	 'label' => __( 'В корзине может быть только этот товар', 'kristall' ),
	 'class' => 'kristall-custom-checkbox',
	 'value' => get_post_meta( $post->ID, 'single_in_cart', true ) == 1 ? 'yes' : get_post_meta( $post->ID, 'single_in_cart', true ),
	 );
	 woocommerce_wp_checkbox( $args );

	 // Только для физ.лиц. (нельзя покупать ЮРикам и ИПэшникам)
	 $args = array(
	 'id' => 'only_for_fiz_lico',
	 'label' => __( 'Только для физ.лиц. (нельзя покупать ЮРикам и ИПэшникам)', 'kristall' ),
	 'class' => 'kristall-custom-checkbox',
	 'value' => get_post_meta( $post->ID, 'only_for_fiz_lico', true ) == 1 ? 'yes' : get_post_meta( $post->ID, 'only_for_fiz_lico', true ),
	 );
	 woocommerce_wp_checkbox( $args );
 
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
	 $single_in_cart = isset( $_POST['single_in_cart'] ) ? $_POST['single_in_cart'] : '';
	 if ($single_in_cart == 'yes' || $single_in_cart == 1){
		 $single_in_cart = 1;
	 }else{
		 $single_in_cart = 0;
	 }
	 $only_for_fiz_lico = isset( $_POST['only_for_fiz_lico'] ) ? $_POST['only_for_fiz_lico'] : '';
	 if ($only_for_fiz_lico == 'yes' || $only_for_fiz_lico == 1){
         $only_for_fiz_lico = 1;
	 }else{
         $only_for_fiz_lico = 0;
	 }
	 
	 $product->update_meta_data( 'is_service', sanitize_text_field( $is_service ) );
	 $product->update_meta_data( 'country', sanitize_text_field( $country ) );
	 $product->update_meta_data( 'customs_declaration', sanitize_text_field( $customs_declaration ) );
	 $product->update_meta_data( 'unit', sanitize_text_field( $unit ) );
	 $product->update_meta_data( 'single_in_cart', sanitize_text_field( $single_in_cart ) );
	 $product->update_meta_data( 'only_for_fiz_lico', sanitize_text_field( $only_for_fiz_lico ) );

	 $product->save();
}
add_action( 'woocommerce_process_product_meta', 'kristall_save_custom_field' );


