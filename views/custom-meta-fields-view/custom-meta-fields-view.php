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

	 echo "<div class=\"kristall-custom-fields-wrapper\">";

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
		 '<div class="kristall-custom-field-wrapper single_in_cart">Не может быть приобретен с другими товарами</div>'
		 );
	 }

    echo "</div>";
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


/*
 * Чтобы нельзя было добавлять любой товар если в корзине есть товар со свойством единственный на заказ
*/

function so_validate_add_cart_item( $passed, $product_id ) { 
	
	global $woocommerce;
	
	$this_product = wc_get_product( $product_id );
	
	$cart_is_empty = true;
	$obuchenie_in_cart = false;
	
	$this_product_is_obuchenie = $this_product->get_meta( 'single_in_cart' );
	$this_product_alredy_in_cart = false;
	
    foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
		$cart_is_empty = false;
		
        $product_id_in_cart = $values['product_id'];
		
		$product_in_cart = wc_get_product( $product_id_in_cart );
		
		$product_is_obuchenie = $product_in_cart->get_meta( 'single_in_cart' );
		
		if ( $product_id_in_cart == $product_id ) {
            $this_product_alredy_in_cart = true;
        }  
		
		if ( $product_is_obuchenie ) {
            $obuchenie_in_cart = true;
        }       
    }
	

	if ( (!$cart_is_empty && $obuchenie_in_cart && !$this_product_alredy_in_cart) ){

		$passed = false;

		wc_add_notice( __('Вы не можете добавить это. Товар, находящейся сейчас в корзине, оформляться только отдельным заказом', 'textdomain' ), 'error' );
		
	}
	
	if ( (!$cart_is_empty && !$obuchenie_in_cart && $this_product_is_obuchenie) ){

		$passed = false;

		wc_add_notice( __('Вы не можете добавить это в корзину, т.к. данный товар оформляться только отдельным заказом', 'textdomain' ), 'error' );
		
	}

	return $passed;
}

add_filter( 'woocommerce_add_to_cart_validation', 'so_validate_add_cart_item', 10, 2 );