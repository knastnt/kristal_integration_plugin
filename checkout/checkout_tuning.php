<?php


// Проверяем включен ли чекбокс настроек плагина - скрыть div class=woocommerce-additional-fields в checkuot. Скрываем содержимое если надо
if ( isset(get_option( 'kristall_options_array' ) ["hide_woocommerce_additional_fields_in_checkout"]) && get_option( 'kristall_options_array' ) ["hide_woocommerce_additional_fields_in_checkout"] == true ) {

    //wp-content/plugins/woocommerce/templates/checkout/form-shipping.php

    add_action( 'woocommerce_before_order_notes', 'open_div' );
    function open_div() {
        ?><div style="display: none"><?php
    }

    add_action( 'woocommerce_after_order_notes', 'close_div' );
    function close_div() {
        ?></div><?php
    }



    //подключаем css для скрытия #payment.woocommerce-checkout-payment ul выбора способа оплаты
    add_action( 'woocommerce_checkout_order_review', 'hiding_in_woocommerce_checkout_payment', 19 );

    function hiding_in_woocommerce_checkout_payment(){
        ?>
        <style>
            #payment ul{
                display: none;
            }
        </style>
        <?php
    }



    //подключаем css для скрытия способа оплаты в чеке, а также вызываем функцию для создания перенаправления в Кристалл
    //wp-content/plugins/woocommerce/templates/checkout/thankyou.php
    add_filter( 'woocommerce_thankyou_order_received_text', 'hiding_pay_method_and_change_text' );

    function hiding_pay_method_and_change_text($content){
        ?>
        <style>
            .woocommerce-order ul li.woocommerce-order-overview__payment-method{
                display: none;
            }
            .woocommerce-order ul li.woocommerce-order-overview__total{
                border: 0;
            }

            .woocommerce-order h2.woocommerce-column__title{
                display: none;
            }
        </style>
        <?php
        return $content;
    }
}


//создаем и выводим логику перенаправления в кристалл
//wp-content/plugins/woocommerce/templates/checkout/thankyou.php
add_filter( 'woocommerce_thankyou_order_received_text', 'generate_kristall_redirect_content' );
function generate_kristall_redirect_content($content ){
    echo $content;

    global $wp;
    //будем оборачивать button в a с отменой действия onclick
    //такая хрень потому что если делать через form action, то, в случае если кристалл на http,
    //браузер спросит пользователя - уверин ли он что разрешает передачу данных на незащищенный
    //сайт и то что их могут перехватить. в вопросе оплаты это стремно.
    //Да, это ху*во, но кристалл покачто на http :(((

    //Генерируем ссылку
    $link = get_option('kristall_options_array');
    $link = isset($link['redirect_user_to_kristall_url']) ? $link['redirect_user_to_kristall_url'] : 'http://www.kristal-online.ru/api/api.php?data=aplyOrderWc&order_id=%ID%';
    $link = str_replace("%ID%", $wp->query_vars['order-received'], $link);
    ?>
    <div class="redirect2kristall">
        <p>Через <span id="time" style="font-weight: 700;">10</span> секунд Вы будете перенаправлены в Кристалл для оплаты заказа... &nbsp;Если перенаправления не произошло, нажмите на кнопку ниже</p>

        <script type="text/javascript">
            var i = 10;//время в сек.
            function time(){
                document.getElementById("time").innerHTML = i;//визуальный счетчик
                i--;//уменьшение счетчика
                if (i < 0) {
                    clearInterval(myTimer);
                    location.href = "<?php echo $link; ?>";
                }//редирект
            }
            time();
            var myTimer = setInterval(time, 1000);
        </script>

        <a href="<?php echo $link; ?>">
            <button onlick="return false;" type="submit" class="single_add_to_cart_button button alt">Перейти в Кристалл для оплаты</button>
        </a>
    </div>
    <?php
}



//https://stackoverflow.com/questions/39336970/wordpress-radio-button-checkout-woocommerce-show-hide-required-field
/**
 * Add custom question field after the billing form fields
 *
 * @param Integer $order_id New order id
 * @author Tameem Safi <tameem@safi.me.uk>
 */
function custom_checkout_question_field( $checkout ) {

    echo "<div class='custom-question-field-wrapper custom-question-1'>";

    echo sprintf( '<p>%s</p>', __( "Sei un privato cittadino, un'azienda o un libero professionista?" ) );

    woocommerce_form_field( 'custom_question_field', array(
        'type'            => 'radio',
        'required'        => true,
        'class'           => array('custom-question-field', 'form-row-wide'),
        'options'         => array(
            'privato_cittadino'         => 'Privato cittadino',
            'azienda_professionista'    => 'Azienda o libero professionista',
        ),
    ), $checkout->get_value( 'custom_question_field' ) );

    woocommerce_form_field( 'custom_question_text_codice_fiscale', array(
        'type'            => 'text',
        'label'           => 'Codice Fiscale',
        'required'        => true,
        'class'           => array('custom-question-codice-fiscale-field', 'form-row-wide'),
    ), $checkout->get_value( 'custom_question_text_codice_fiscale' ) );

    woocommerce_form_field( 'custom_question_text_p_iva', array(
        'type'            => 'text',
        'label'           => 'P.Iva',
        'required'        => true,
        'class'           => array('custom-question-p-iva-field', 'form-row-wide'),
    ), $checkout->get_value( 'custom_question_text_p_iva' ) );

    woocommerce_form_field( 'custom_question_text_ragione_sociale', array(
        'type'            => 'text',
        'label'           => 'Ragione sociale',
        'required'        => true,
        'class'           => array('custom-question-ragione-sociale-field', 'form-row-wide'),
    ), $checkout->get_value( 'custom_question_text_ragione_sociale' ) );

    echo "</div>";

}

add_action( 'woocommerce_before_checkout_billing_form', 'custom_checkout_question_field' );






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
                    codiceFiscaleField  = $('.custom-question-codice-fiscale-field'),
                    pIvaField           = $('.custom-question-p-iva-field'),
                    ragioneSocialeField = $('.custom-question-ragione-sociale-field ');

                // Check that all fields exist
                if(
                    !questionField.length ||
                    !codiceFiscaleField.length ||
                    !pIvaField.length ||
                    !ragioneSocialeField.length
                ) {
                    return;
                }

                function toggleVisibleFields() {
                    var selectedAnswer = questionField.find('input:checked').val();

                    if(selectedAnswer === 'privato_cittadino') {
                        codiceFiscaleField.show();
                        pIvaField.hide();
                        ragioneSocialeField.hide();
                    } else if(selectedAnswer === 'azienda_professionista') {
                        codiceFiscaleField.hide();
                        pIvaField.show();
                        ragioneSocialeField.show();
                    } else {
                        codiceFiscaleField.hide();
                        pIvaField.hide();
                        ragioneSocialeField.hide();
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
            'custom_question_text_codice_fiscale'     => '',
            'custom_question_text_p_iva'                => '',
            'custom_question_text_ragione_sociale'    => '',
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
        wc_add_notice( 'Please select an answer for the question.', 'error' );
    }

    if (
        $field_values['custom_question_field'] === 'privato_cittadino' &&
        empty( $field_values['custom_question_text_codice_fiscale'] )
    ) {
        wc_add_notice( 'Please enter codice fiscale.', 'error' );
    }

    if (
        $field_values['custom_question_field'] === 'azienda_professionista' &&
        empty( $field_values['custom_question_text_p_iva'] )
    ) {
        wc_add_notice( 'Please enter p iva.', 'error' );
    }

    if (
        $field_values['custom_question_field'] === 'azienda_professionista' &&
        empty( $field_values['custom_question_text_ragione_sociale'] )
    ) {
        wc_add_notice( 'Please enter ragione sociale.', 'error' );
    }

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

        foreach( $field_values as $field_name => $value ) {
            if( !empty( $field_values[ $field_name ] ) ) {
                update_post_meta( $order_id, $field_name, $value );
            }
        }
    }

    add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_question_field_save' );
}