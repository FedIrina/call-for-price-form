<?php
/**
 * Single variation cart button
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>
<!-- TEST: Variation add to cart button template loaded -->
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<?php
/* 	do_action( 'woocommerce_before_add_to_cart_quantity' );

	woocommerce_quantity_input(
		array(
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
			'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
		)
	);

	do_action( 'woocommerce_after_add_to_cart_quantity' ); */
	?>

	<!-- Кнопка добавления в корзину (показывается только для вариаций с ценой) -->
	<button type="submit" 
		name="add-to-cart" 
		value="<?php echo esc_attr( $product->get_id() ); ?>" 
		class="btn btn-primary single_add_to_cart_button button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>">
		<?php echo esc_html( $product->single_add_to_cart_text() ); ?> (<span class="variation-price"><?php echo wc_price($product->get_price()); ?></span>)
	</button>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>

<!-- Отдельный контейнер для кнопки "Запросить цену" -->
<div class="variation-request-price-container" style="display: none;">
	<button type="button" 
		class="btn btn-primary request-price-button variation-request-price-button" 
		data-bs-toggle="modal" 
		data-bs-target="#requestPriceModal"
		data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
		data-product-name="<?php echo esc_attr( Call_For_Price_Form_Functions::get_variation_name($product) ); ?>"
		data-original-product-name="<?php echo esc_attr( $product->get_name() ); ?>"
		data-product-url="<?php echo esc_attr( $product->get_permalink() ); ?>">
		<?php echo esc_html( Call_For_Price_Form_Settings::get_option('call_for_price_button_text', __('Запросить цену', 'call-for-price-form')) ); ?>
	</button>
</div> 