<?php
defined( 'ABSPATH' ) || exit;
$plugin_info             = $args['plugin_info'];
$core                    = $args['core'];
$main_settings           = $args['main_settings'];
$select_type_field       = $args['select_type_field'];
$repeater_field_key      = $args['repeater_field_key'];
$repeater_field_item_key = $args['repeater_field_item_key'];
$term_key                = $args['term_key'] ?? null;
$product_select_field    = $main_settings->get_repeater_item( $repeater_field_key, $repeater_field_item_key, $select_type_field['repeater_index'] ?? null );
?>
<div class="settings-field-wrapper py-4 my-4 col-md-12 col-lg-12 p-2" <?php echo ( ! empty( $term_key ) ? ( 'data-target="' . esc_attr( $term_key ) .'" ' ) : '' ); ?> >
	<div class="wrapper p-3">
		<!-- Limit Conditions Conf -->
		<div class="product-select-container">
			<select data-type="<?php echo esc_attr( $select_type_field['value'] ); ?>" class="select2-input" name="<?php echo esc_attr( $main_settings->get_id() . '[' . $repeater_field_key . '][' . ( $select_type_field['repeater_index'] ?? 0 ) . '][' . $repeater_field_item_key . ']' . ( ! empty( $term_key ) ? '[' . $term_key . ']' : '' ) . '[]' ); ?>" multiple>
				<?php $main_settings->prepare_product_select_options( absint( $select_type_field['value'] ), $product_select_field['value'] ); ?>
			</select>
		</div>
	</div>
</div>
