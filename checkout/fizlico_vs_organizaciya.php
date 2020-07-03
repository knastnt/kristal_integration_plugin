<?php

//Подключение класса проверки введенных данных
require_once( plugin_dir_path(__FILE__ ) . '/../utils/datavalidation.php' );


//https://stackoverflow.com/questions/39336970/wordpress-radio-button-checkout-woocommerce-show-hide-required-field
/**
 * Add custom question field after the billing form fields
 *
 * @param Integer $order_id New order id
 * @author Tameem Safi <tameem@safi.me.uk>
 */
function custom_checkout_question_field( $checkout ) {

    echo "<div class='custom-question-field-wrapper custom-question-1'>";

    //echo sprintf( '<p>%s</p>', __( "Выберите: Физ.лицо или Организация" ) );

    /* Отлавливаем ограничения allow_client_types и убираем тех кому покупать запрещено Физ, ИП или Юр.*/
    global $woocommerce;



    $cart_allow_client_types_fiz = 1;
    $cart_allow_client_types_ip = 1;
    $cart_allow_client_types_yur = 1;

    foreach( $woocommerce->cart->get_cart() as $cart_item ){
        $product_id = $cart_item['product_id'];
        $product = wc_get_product( $product_id );


        $allow_client_types_fiz = (int)$product->get_meta( 'allow_client_types_fiz' );
        $allow_client_types_ip = (int)$product->get_meta( 'allow_client_types_ip' );
        $allow_client_types_yur = (int)$product->get_meta( 'allow_client_types_yur' );
        if ($allow_client_types_fiz + $allow_client_types_ip + $allow_client_types_yur > 0) {
            $cart_allow_client_types_fiz *= $allow_client_types_fiz;
            $cart_allow_client_types_ip *= $allow_client_types_ip;
            $cart_allow_client_types_yur *= $allow_client_types_yur;
        }

    }

    $options = array(
        'fiz_lico'         => 'Физ.лицо',
        'individ_predprin'    => 'ИП',
        'yur_lico'    => 'Юр.Лицо'
    );
    if ($cart_allow_client_types_fiz == 0) unset($options['fiz_lico']);
    if ($cart_allow_client_types_ip == 0) unset($options['individ_predprin']);
    if ($cart_allow_client_types_yur == 0) unset($options['yur_lico']);



    if(count($options)==0) {
        print(
            '<div class="kristall-custom-field-wrapper woocommerce-error"><span>В вашей корзине находятся тованы предназначенные для разнах клиентов. Одновременная покупка невозможна.</span></div>'
        );
    }else {
        woocommerce_form_field('custom_question_field', array(
            'type' => 'radio',
            'required' => true,
            'class' => array('custom-question-field', 'form-row-wide'),
            'options' => $options,
            'default' => array_keys($options)[0],
        ), $checkout->get_value('custom_question_field'));

        woocommerce_form_field( 'custom_question_text_p_naimenovanie', array(
            'type'            => 'text',
            'label'           => 'Наименование организации',
            'required'        => true,
            'class'           => array('custom-question-p-naimenovanie-field', 'form-row-wide'),
        ), $checkout->get_value( 'custom_question_text_p_naimenovanie' ) );

        woocommerce_form_field( 'custom_question_text_p_inn', array(
            'type'            => 'text',
            'label'           => 'ИНН',
            'required'        => true,
            'class'           => array('custom-question-p-inn-field', 'form-row-wide'),
        ), $checkout->get_value( 'custom_question_text_p_inn' ) );

        woocommerce_form_field( 'custom_question_text_ogrnip', array(
            'type'            => 'text',
            'label'           => 'ОГРНИП',
            'required'        => true,
            'class'           => array('custom-question-ogrnip-field', 'form-row-wide'),
        ), $checkout->get_value( 'custom_question_text_ogrnip' ) );

        woocommerce_form_field( 'custom_question_text_ogrn', array(
            'type'            => 'text',
            'label'           => 'ОГРН',
            'required'        => true,
            'class'           => array('custom-question-ogrn-field', 'form-row-wide'),
        ), $checkout->get_value( 'custom_question_text_ogrn' ) );
    }



    echo "</div>";

}

add_action( 'woocommerce_before_checkout_billing_form', 'custom_checkout_question_field' );


add_filter( 'woocommerce_checkout_fields','checkout_extra_fields');
function checkout_extra_fields($fields){
    $fields['billing']['custom_patronymic'] = array( //Добавляем поле Отчество
        'label'       => 'Отчество',
        'priority'        => 15,
        'required'    => true,
        'type'        => 'text',
        'class'           => array('form-row-last'),
    );
    $fields['billing']['billing_last_name']['class'] =  array('form-row'); //Делаем поле Фамилия - широким

    return $fields;
}






function custom_question_conditional_javascript() {
    ?>
    <script type="text/javascript">
        (function() {

            // Check if jquery exists
            if(!window.jQuery) {
                return;
            };

            var $ = window.jQuery;

            $(document).ready(function() {

                var questionField       = $('.custom-question-field'),
                    pNaimenovanieField           = $('.custom-question-p-naimenovanie-field'),
                    pINNField           = $('.custom-question-p-inn-field'),
                    ogrnipField = $('.custom-question-ogrnip-field '),
                    ogrnField = $('.custom-question-ogrn-field ');

                // Check that all fields exist
                if(
                    !questionField.length ||
                    !pNaimenovanieField.length ||
                    !pINNField.length ||
                    !ogrnipField.length ||
                    !ogrnField.length
                ) {
                    return;
                }

                function toggleVisibleFields() {
                    var selectedAnswer = questionField.find('input:checked').val();

                    if(selectedAnswer === 'individ_predprin') {
                        pNaimenovanieField.show();
                        pINNField.show();
                        ogrnipField.show();
                        ogrnField.hide();
                    } else if(selectedAnswer === 'yur_lico') {
                        pNaimenovanieField.show();
                        pINNField.show();
                        ogrnipField.hide();
                        ogrnField.show();
                    } else {
                        pNaimenovanieField.hide();
                        pINNField.hide();
                        ogrnipField.hide();
                        ogrnField.hide();
                    }
                }

                $(document).on('change', 'input[name=custom_question_field]', toggleVisibleFields);
                $(document).on('updated_checkout', toggleVisibleFields);

                toggleVisibleFields();

            });
        })();
    </script>
    <?php
}
add_action( 'wp_footer', 'custom_question_conditional_javascript', 1000 );





if( !function_exists( 'custom_checkout_question_get_field_values' ) ) {
    /**
     * Get all form field values based on user submitted post data
     *
     * @return Array Key/value pair of field values based on $_POST data
     * @author Tameem Safi <tameem@safi.me.uk>
     */
    function custom_checkout_question_get_field_values() {
        $fields = [
            'custom_question_field'                       => '',
            'custom_question_text_p_naimenovanie'                => '',
            'custom_question_text_p_inn'                => '',
            'custom_question_text_ogrnip'    => '',
            'custom_question_text_ogrn'    => '',
            'custom_patronymic'    => '',
        ];

        foreach( $fields as $field_name => $value ) {
            if( !empty( $_POST[ $field_name ] ) ) {
                $fields[ $field_name ] = sanitize_text_field( $_POST[ $field_name ] );
            } else {
                unset( $fields[ $field_name ] );
            }
        }

        return $fields;
    }
}




/**
 * Custom woocommerce field validation to prevent user for completing checkout
 *
 * @param Integer $order_id New order id
 * @author Tameem Safi <tameem@safi.me.uk>
 */
function custom_checkout_question_field_validate() {
    $field_values = custom_checkout_question_get_field_values();

    if ( empty( $field_values['custom_question_field'] ) ) {
        wc_add_notice( 'Выберите: Физ.лицо, ИНН или Юр.лицо', 'error' );
    }

    ///////////////////////////////////////
    if ( $field_values['custom_question_field'] === 'individ_predprin' ) {

        if ( empty($field_values['custom_question_text_p_naimenovanie']) ) {
            wc_add_notice('<b>Поле Наименование организации</b> является обязательным полем.', 'error');
        }

        if ( empty($field_values['custom_question_text_p_inn']) ) {
            wc_add_notice('<b>Поле ИНН</b> является обязательным полем.', 'error');
        }else{
            //Проверка корректности
            $ems = "";
            $ecode = "";
            DataValidation::validateInn($field_values['custom_question_text_p_inn'],$ems, $ecode);
            if ( $ems != "") wc_add_notice($ems, 'error');
        }

        if ( empty($field_values['custom_question_text_ogrnip']) ) {
            wc_add_notice('<b>Поле ОГРНИП</b> является обязательным полем.', 'error');
        }else{
            //Проверка корректности
            $ems = "";
            $ecode = "";
            DataValidation::validateOgrnip($field_values['custom_question_text_ogrnip'],$ems, $ecode);
            if ( $ems != "") wc_add_notice($ems, 'error');
        }


    }
    ///////////////////////////////////////
    if ( $field_values['custom_question_field'] === 'yur_lico' ) {

        if ( empty($field_values['custom_question_text_p_naimenovanie']) ) {
            wc_add_notice('<b>Поле Наименование организации</b> является обязательным полем.', 'error');
        }

        if ( empty($field_values['custom_question_text_p_inn']) ) {
            wc_add_notice('<b>Поле ИНН</b> является обязательным полем.', 'error');
        }else{
            //Проверка корректности
            $ems = "";
            $ecode = "";
            DataValidation::validateInn($field_values['custom_question_text_p_inn'],$ems, $ecode);
            if ( $ems != "") wc_add_notice($ems, 'error');
        }

        if ( empty($field_values['custom_question_text_ogrn']) ) {
            wc_add_notice('<b>Поле ОГРН</b> является обязательным полем.', 'error');
        }else{
            //Проверка корректности
            $ems = "";
            $ecode = "";
            DataValidation::validateOgrn($field_values['custom_question_text_ogrn'],$ems, $ecode);
            if ( $ems != "") wc_add_notice($ems, 'error');
        }


    }
    ///////////////////////////////////////
}

add_action( 'woocommerce_checkout_process', 'custom_checkout_question_field_validate' );







if( !function_exists( 'custom_checkout_question_field_save' ) ) {
    /**
     * Update order post meta based on submitted form values
     *
     * @param Integer $order_id New order id
     * @author Tameem Safi <tameem@safi.me.uk>
     */
    function custom_checkout_question_field_save( $order_id ) {
        $field_values = custom_checkout_question_get_field_values();


        /* оставляем только нужные ключи */
        if ($field_values['custom_question_field'] === 'fiz_lico') {

            $needs = array('custom_question_field'=>'');

        } elseif ($field_values['custom_question_field'] === 'individ_predprin') {

            $needs = array('custom_question_field'=>'', 'custom_question_text_p_naimenovanie'=>'', 'custom_question_text_p_inn'=>'', 'custom_question_text_ogrnip'=>'');

        } elseif ($field_values['custom_question_field'] === 'yur_lico') {

            $needs = array('custom_question_field'=>'', 'custom_question_text_p_naimenovanie'=>'', 'custom_question_text_p_inn'=>'', 'custom_question_text_ogrn'=>'');

        }

        $needs['custom_patronymic'] = '';

        $field_values = array_intersect_key($field_values, $needs);



        foreach( $field_values as $field_name => $value ) {
            if( !empty( $field_values[ $field_name ] ) ) {
                update_post_meta( $order_id, $field_name, $value );
            }
        }
    }

    add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_question_field_save' );
}