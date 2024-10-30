<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\utils;

trait LimiterUtils {

	use CartUtils;
	use LimiterNoticesUtils;

	/**
	 * Limit Select Type Mapping.
	 *
	 * @var array
	 */
	private $limit_select_type_mapping = array(
		'products'    => 1,
		'taxs'        => 2,
	);

	/**
	 * Check if Limit is enabled.
	 *
	 * @return boolean
	 */
	private function is_limit_enabled() {
		return ( 'on' === $this->main_settings->get_settings( 'global_enable' ) );
	}

	/**
	 * Is limit Type Only One Product.
	 *
	 * @return boolean
	 */
	private function is_limit_type_only_one_product() {
		return ( 1 === $this->main_settings->get_settings( 'limit_type' ) );
	}

	/**
	 * Check if apply restrict on Quantity Field.
	 *
	 * @return boolean
	 */
	private function is_apply_restrict_on_qty_field() {
		return ( 'on' === $this->settings['qty_field_restrict'] );
	}

	/**
	 * Is limit Type limit conditions.
	 *
	 * @return boolean
	 */
	private function is_limit_type_conditions() {
		return ( 2 === $this->main_settings->get_settings( 'limit_type' ) );
	}

	/**
	 * Get Settings Field.
	 *
	 * @param string $field_key
	 * @return mixed
	 */
	private function get_settings_field( $field_key ) {
		return $this->settings[ $field_key ];
	}

	/**
	 * Compare Qty based on compare type.
	 *
	 * @param string  $operator
	 * @param mixed   $src
	 * @param mixed   $target
	 * @param boolean $include_equal
	 * @return boolean
	 */
	private function qty_compare( $operator, $src, $target, $include_equal = false ) {
		if ( $include_equal ) {
			return match ( $operator ) {
				'<' => ( $src <= $target ),
				'>' => ( $src >= $target ),
			};
		}
		return match ( $operator ) {
			'<' => ( $src < $target ),
			'>' => ( $src > $target ),
		};
	}

	/**
	 * Disable Cart Update Trigger.
	 *
	 * @return void
	 */
	private function disable_cart_update_triggered() {
		add_filter( 'woocommerce_update_cart_action_cart_updated', '__return_false' );
	}

	/**
	 * Trigger the Qty changed is checked.
	 *
	 * @return void
	 */
	private function trigger_qty_changed_is_checked( $result ) {
		$GLOBALS[ static::$plugin_info['name'] . '-qty-changed-check-limits-checked' ] = $result;
	}

	/**
	 * Check if the Qty changed is already checked.
	 *
	 * @return boolean
	 */
	private function is_qty_changed_checked() {
		return isset( $GLOBALS[ static::$plugin_info['name'] . '-qty-changed-check-limits-checked' ] );
	}

	/**
	 * Get Qty Changed Check Result.
	 *
	 * @return boolean
	 */
	private function get_qty_changed_check_result() {
		return $GLOBALS[ static::$plugin_info['name'] . '-qty-changed-check-limits-checked' ] ?? null;
	}

}
