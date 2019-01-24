<?php

// https://catapultthemes.com/add-custom-fields-woocommerce-product/
// https://wpruse.ru/woocommerce/custom-fields-in-products/


 


/**
 * Display custom fields on the front end
 * @since 1.0.0
 */
function kristall_display_custom_field() {
	global $post;
	
	 // Check for the custom field value
	 $product = wc_get_product( $post->ID );
	 
	 $is_service = $product->get_meta( 'is_service' );
	 $country = $product->get_meta( 'country' );
	 $customs_declaration = $product->get_meta( 'customs_declaration' );
	 $unit = $product->get_meta( 'unit' );
	 $single_in_cart = $product->get_meta( 'single_in_cart' );
	 

		 printf(
		 '<div class="kristall-custom-field-wrapper">Тип: %s</div>',
		 esc_html( $is_service == 1 ? 'Услуга' : 'Товар' )
		 );

	 if( $country ) {
		 // Only display our field if we've got a value for the field country
		 printf(
		 '<div class="kristall-custom-field-wrapper">Страна-производитель: %s</div>',
		 esc_html( $country )
		 );
	 }
	 if( $customs_declaration ) {
		 // Only display our field if we've got a value for the field customs_declaration
		 printf(
		 '<div class="kristall-custom-field-wrapper">Номер таможенной декларации: %s</div>',
		 esc_html( $customs_declaration )
		 );
	 }
	 if( $unit ) {
		 // Only display our field if we've got a value for the field unit
		 printf(
		 '<div class="kristall-custom-field-wrapper">Ед.изм.: %s</div>',
		 esc_html( $unit )
		 );
	 }
	 if( $single_in_cart ) {//== 1 || $single_in_cart == "yes") {
		 // Only display our field if we've got a value for the field single_in_cart
		 print(
		 '<div class="kristall-custom-field-wrapper">Не может быть приобретен с другими товарами</div>'
		 );
	 }
}
add_action( 'woocommerce_before_add_to_cart_button', 'kristall_display_custom_field' );


/*
 * Если товар Не может быть приобретен с другими товарами, то и количество его должно быть 1 по логике кристалла.
 * Поэтому меняем функцию проверки woocommerce_is_sold_individually
*/
add_filter( 'woocommerce_is_sold_individually', 'wc_remove_all_quantity_fields', 10, 2 );
function wc_remove_all_quantity_fields( $return, $product ){
		 
	 $single_in_cart = $product->get_meta( 'single_in_cart' );

	return $return || $single_in_cart;
}