<?php

/*Этот модуль решает проблему отображения товаров и вложенных категорий
 на странице (убирает отображение товаров из подкатегорий).
 Раскрывает содержимое uncategorized в корне и скрывает эту категорию */


/*
 * скрыть товары дочерних категорий на странице родительской категории. и развернуть содерджимое Uncategirized
 * https://inprocess.by/blog/kak-skryt-tovary-dochernih-kategorij-na-stranitse-kategorii-woocommerce/
 *
 * есть ещё один вариант:
 * https://wpspec.com/kak-skryit-tovaryi-dochernih-kategoriy-na-stranice-roditelskoy-kategorii-v-woocommerce/
 * */
function custom_pre_get_posts_query( $query ) {

    $children = get_term_children( $query->queried_object_id , 'product_cat' );

    $tax = $query->get( 'tax_query');

    if(is_shop()){
        //Если корневая директория, то делаем экстракт Uncategirized
        $tax[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => get_option( 'default_product_cat' ),
            'operator' => 'IN',
        );
    }else{
        //Если внутри какой-то категории
        $tax[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $children,
            'operator' => 'NOT IN',
        );
    }
    $query->set( 'tax_query', $tax );
}
add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );



/** Скрываем uncategorized
 * https://wordpress.stackexchange.com/questions/301729/hide-uncategorized-products-from-the-shop-page
 * in Woocommerce
 */
function wc_hide_selected_terms( $terms, $taxonomies, $args ) {
    $new_terms = array();
    $defaultcategory = get_option( 'default_product_cat' );
    if ( in_array( 'product_cat', $taxonomies ) ) {
        foreach ( $terms as $key => $term ) {
            if ( $term->term_taxonomy_id != $defaultcategory ) {
                $new_terms[] = $term;
            }
        }
        $terms = $new_terms;
    }
    return $terms;
}
add_filter( 'get_terms', 'wc_hide_selected_terms', 10, 3 );


//Если непосредственно в категории нет товаров, то она не отображается совсем.
//И пофиг если в ней есть вложенная категория с товарами. Исправлияем это
add_filter( 'woocommerce_product_subcategories_hide_empty', function() { return false; }, 10, 1 );


//При этом неправильно считается количество товаров в категории. Удаляем цифру к чертям
add_filter( 'woocommerce_subcategory_count_html', 'woo_remove_category_products_count' );

function woo_remove_category_products_count() {
    return;
}