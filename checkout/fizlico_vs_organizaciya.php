<?php



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

    woocommerce_form_field( 'custom_question_field', array(
        'type'            => 'radio',
        'required'        => true,
        'class'           => array('custom-question-field', 'form-row-wide'),
        'options'         => array(
            'fiz_lico'         => 'Физ.лицо',
            'individ_predprin'    => 'ИП',
            'yur_lico'    => 'Юр.Лицо',
        ),
        'default'         => 'fiz_lico',
    ), $checkout->get_value( 'custom_question_field' ) );

    woocommerce_form_field( 'custom_question_text_p_inn', array(
        'type'            => 'text',
        'label'           => 'ИНН',
        'required'        => true,
        'class'           => array('custom-question-p-inn-field', 'form-row-wide'),
    ), $checkout->get_value( 'custom_question_text_p_inn' ) );

    woocommerce_form_field( 'custom_question_text_ogrn', array(
        'type'            => 'text',
        'label'           => 'ОГРН',
        'required'        => true,
        'class'           => array('custom-question-ogrn-field', 'form-row-wide'),
    ), $checkout->get_value( 'custom_question_text_ogrn' ) );

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
                    pINNField           = $('.custom-question-p-inn-field'),
                    ogrnField = $('.custom-question-ogrn-field ');

                // Check that all fields exist
                if(
                    !questionField.length ||
                    !pINNField.length ||
                    !ogrnField.length
                ) {
                    return;
                }

                function toggleVisibleFields() {
                    var selectedAnswer = questionField.find('input:checked').val();

                    if(selectedAnswer === 'fiz_lico') {
                        pINNField.hide();
                        ogrnField.hide();
                    } else if(selectedAnswer === 'individ_predprin' || selectedAnswer === 'yur_lico') {
                        pINNField.show();
                        ogrnField.show();
                    } else {
                        pINNField.hide();
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
            'custom_question_text_p_inn'                => '',
            'custom_question_text_ogrn'    => '',
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

    if (
        $field_values['custom_question_field'] === 'individ_predprin' &&
        empty( $field_values['custom_question_text_p_inn'] )
    ) {
        wc_add_notice( '<b>Поле ИНН</b> является обязательным полем.', 'error' );
    }

    if (
        $field_values['custom_question_field'] === 'individ_predprin' &&
        empty( $field_values['custom_question_text_ogrn'] )
    ) {
        wc_add_notice( '<b>Поле ОГРН</b> является обязательным полем.', 'error' );
    }

    ///////////////////////////////////////

    if (
        $field_values['custom_question_field'] === 'yur_lico' &&
        empty( $field_values['custom_question_text_p_inn'] )
    ) {
        wc_add_notice( '<b>Поле ИНН</b> является обязательным полем.', 'error' );
    }

    if (
        $field_values['custom_question_field'] === 'yur_lico' &&
        empty( $field_values['custom_question_text_ogrn'] )
    ) {
        wc_add_notice( '<b>Поле ОГРН</b> является обязательным полем.', 'error' );
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