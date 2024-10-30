<?php
defined( 'ABSPATH' ) || exit;
$plugin_info      = $args['plugin_info'];
$core             = $args['core'];
$main_settings    = $args['main_settings'];
$settings         = $main_settings->get_settings();
$limit_conditions = $main_settings->get_settings( 'product_limit_conditions' );
?>
<div class="limit_type-settings-field-container">
	<div class="wrapper p-3">
		<!-- Limit Conditions Conf -->
		<div class="products-limit-conditions-content">
			<div class="border <?php echo esc_attr( $main_settings->get_id() . '-repeater-container' ); ?> p-3">
				<?php $main_settings->get_field_html( 'product' ); ?>
			</div>
		</div>
	</div>
</div>
