<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!function_exists('wc_print_notices')){
    return;
}

?>
<div id="la_wishlist_table_wrapper">
    <div id="la_wishlist_table_wrapper2">
<?php
wc_print_notices();
$lists = Bakerfresh_WooCommerce_Wishlist::get_data();
$total = count($lists);
$per_page = 10;
$current_page = max( 1, get_query_var( 'paged' ) );
$page_links = '';
$newlist = $lists;
if($total > 1){
    $pages = absint(ceil( $total / $per_page ));

    if( $current_page > $pages ){
        $current_page = $pages;
    }
    $offset = ( $current_page - 1 ) * $per_page;

    if( $pages > 1 ){
        $page_links = paginate_links( array(
            'base' => esc_url( add_query_arg( array( 'paged' => '%#%' ), bakerfresh_get_wishlist_url() )),
            'format' => '?paged=%#%',
            'current' => $current_page,
            'total' => $pages,
            'show_all' => true,
            'prev_text'    => esc_html_x('Prev', 'front-view', 'bakerfresh'),
            'next_text'    => esc_html_x('Next', 'front-view', 'bakerfresh'),
            'type'         => 'list'
        ) );
        $page_links = sprintf('<div class="la-pagination">%s</div>', $page_links);
    }
    $newlist = array_slice($lists, $offset, $per_page);
}

?>
<table class="la_wishlist_table shop_table shop_table_responsive woocommerce-cart-form__contents" cellspacing="0">
    <thead>
    <tr>
        <th class="product-remove">&nbsp;</th>
        <th class="product-thumbnail">&nbsp;</th>
        <th class="product-name"><?php esc_html_e( 'Product', 'bakerfresh' ); ?></th>
        <th class="product-stock-status"><?php esc_html_e( 'Stock Status', 'bakerfresh' ); ?></th>
        <th class="product-price"><?php esc_html_e( 'Price', 'bakerfresh' ); ?></th>
        <th class="product-action"></th>
    </tr>
    </thead>
    <tbody>
    <?php

    if($total > 0){

        foreach($newlist as $product_id){

            global $product;

            $product_id = bakerfresh_wpml_object_id ( $product_id, 'product', true );
            $_product = wc_get_product($product_id);
            $availability = $_product->get_availability();
            $stock_status = $availability['class'];
            if( $_product && $_product->exists() ){
                $product = $_product;
                ?>
                <tr class="woocommerce-cart-form__cart-item cart_item">
                    <td class="product-remove">
                        <?php
                        // @codingStandardsIgnoreLine
                        echo sprintf(
                            '<a href="%s" class="remove la_remove_from_wishlist" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                            esc_url( add_query_arg( array(
                                'la_helpers_wishlist_remove' => $product_id
                            ) ) ),
                            esc_attr__( 'Remove this item', 'bakerfresh' ),
                            esc_attr( $product_id ),
                            esc_attr( $_product->get_sku() )
                        );
                        ?>
                    </td>
                    <td class="product-thumbnail"><?php
                        printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink() ), $_product->get_image() );
                    ?></td>
                    <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'bakerfresh' ); ?>"><?php
                        printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink() ), $_product->get_name() );
                    ?></td>
                    <td class="product-stock-status" data-title="<?php esc_attr_e( 'Stock Status', 'bakerfresh' ); ?>"><?php
                        if($stock_status == 'out-of-stock'){
                            echo '<span class="stock out-of-stock">' . esc_html__( 'Out of Stock', 'bakerfresh' ) . '</span>';
                        }
                        else{
                            echo '<span class="stock in-stock">' . esc_html__( 'In Stock', 'bakerfresh' ) . '</span>';
                        }

                    ?></td>
                    <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'bakerfresh' ); ?>">
                        <?php
                        echo bakerfresh_render_variable($_product->get_price_html());
                        ?>
                    </td>
                    <td class="product-action">
                        <!-- Add to cart button -->
                        <?php
                        if( isset( $stock_status ) && $stock_status != 'out-of-stock' ){
                            woocommerce_template_loop_add_to_cart();
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        }

        if(!empty($page_links)){
            echo sprintf(
                '<tr class="pagination-row"><td colspan="6">%s</td></tr>',
                $page_links
            );
        }
        ?>
        <?php

    }
    else{
        ?>
        <tr class="not-found-product text-center"><td colspan="6"><?php esc_html_e('No products were added to the wishlist', 'bakerfresh') ?></td></tr>
        <?php
    }

    ?>
    </tbody>
</table>
</div>
</div>