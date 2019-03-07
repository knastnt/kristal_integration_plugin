<?php

add_action( 'woocommerce_new_order', 'create_invoice_for_wc_order' );

function create_invoice_for_wc_order($order_id) {
	
	// Проверяем включена ли доставка
	if ( !get_option( 'kristall_options_array' ) ["send_new_orders_to_kristall"] ) return;

    $order = wc_get_order( $order_id);

        /*// Iterating through each "line" items in the order
        foreach ($order->get_items() as $item_id => $item_data) {

            // Get an instance of corresponding the WC_Product object
            $product = $item_data->get_product();
            $product_name = $product->get_name(); // Get the product name
            $item_quantity = $item_data->get_quantity(); // Get the item quantity
            $all_qua = $product->get_stock_quantity();

            $diff = $all_qua - $item_quantity;

            wc_update_product_stock($product, $diff);

            $item_total = $item_data->get_total(); // Get the item line total

            // Displaying this data (to check)
            echo 'Product name: '.$product_name.' | Quantity: '.$item_quantity.' | All_Quantity: '.$all_qua.' | Item total: '. number_format( $item_total, 2 );
            // $txt = fopen( "C:\\ospanel\\domains\\joke.fuu\\wp-content\\themes\\storefront\\hhh.txt" , "w");
            // fwrite($txt, $new_status);

        }*/
		
	//$email = $order->billing_email;
    $phone = substr($order->billing_phone, 2);
	$first_name = $order->billing_first_name;
	$last_name = $order->billing_last_name;
	
	$data = array(
            'order_id' => $order_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone' => $phone
        );
	//$data = print_r($order, 1) . PHP_EOL;
    //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/dump.txt', $data, FILE_APPEND);
	//$url = "https://beta.rollbox.su/reciver.php";
    $url = get_option('kristall_options_array');
    $url = isset($url['send_new_orders_to_kristall_url']) ? $url['send_new_orders_to_kristall_url'] : 'http://kristal-online.ru/wordpress-integration.php';
	
	/*wp_remote_post( $url, array(
		'body'        => array('foo'=>'val', 'bar'=>'val'), // параметры запроса в массиве
	) );*
	
	$url = 'http://beta.rollbox.su/reciver.php';
	$args = array(
		'timeout'     => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers' => array(),
		'body'    => array( 'username' => 'bob', 'password' => '1234xyz' ),
		'cookies' => array()
	);
	$response = wp_remote_post( $url, $args );

	// проверка ошибки
	if ( is_wp_error( $response ) ) {
	   $error_message = $response->get_error_message();
	   echo "Что-то пошло не так: $error_message";
	} else {
	   echo 'Ответ: <pre>';
	   print_r( $response );
	   echo '</pre>';
	}
	
	$t=ob_get_contents();
ob_clean();

	
	$data = print_r($t, 1) . PHP_EOL;
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/errors_kristal-post.txt', $data);//, FILE_APPEND);*/
	
	 $ch = curl_init();

        /* set the complete URL, to process the order on the external system. Let’s consider http://example.com/buyitem.php is the URL, which invokes the API */
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    
        $response = curl_exec ($ch);
    
        curl_close ($ch);
}

