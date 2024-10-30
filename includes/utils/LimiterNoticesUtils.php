<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\utils;

/**
 * Limiter Terms related Functions.
 */
trait LimiterNoticesUtils {

    /**
	 * Notice Placeholders.
	 *
	 * @var array
	 */
	private $placeholders = array(
		'{{product_name}}',
	);

    /**
	 * Mark the notice message is set.
	 *
	 * @return void
	 */
	private function trigger_notice_message() {
		$GLOBALS[ static::$plugin_info['name'] . '-notice-message-is-set' ] = true;
	}

    /**
	 * Already notice message is set.
	 *
	 * @return boolean
	 */
	private function is_notice_message_triggered() {
		return ( ! empty( $GLOBALS[ static::$plugin_info['name'] . '-notice-message-is-set' ] ) );
	}

	/**
	 * Add Woo Message Notice.
	 *
	 * @param string $notice_message
	 * @return void
	 */
	private function add_notice( $notice_message, $tab, $key, $product_id = null, $notice_type = 'error', $additional_data = array() ) {
		if ( ! empty( $notice_message ) && ! wc_has_notice( $notice_message, 'error' ) ) {

			if ( ! is_null( $product_id ) ) {
				$notice_message = $this->resolve_placeholders( $notice_message, $product_id );
			}

			$notice_data = array_merge(
				array(
					static::$plugin_info['classes_prefix'] . '-notice' => 'on',
					'notice_tab'                                     => $tab,
					'notice_key'                                     => $key,
				),
				$additional_data
			);

			/* translators: %s Cart Limit Message Notice */
			wc_add_notice( sprintf( esc_html__( '%s', 'cart-limiter' ), $notice_message ), $notice_type, $notice_data );
			$this->trigger_notice_message();
		}
	}

	/**
	 * Resovle Notice Placeholders for Qty Limits.
	 *
	 * @param string $message
	 * @param int    $product_id
	 * @return string
	 */
	private function resolve_placeholders( $message, $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! is_a( $product, '\WC_Product' ) ) {
			return $message;
		}

		foreach ( $this->placeholders as $placeholder ) {

			switch ( $placeholder ) {
				// product_name.
				case '{{product_name}}':
					$message = str_replace( $placeholder, $product->get_name(), $message );
					break;
			}
		}

		return $message;
	}

    /**
	 * Get Limit Notices from Registered WC Notices.
	 *
	 * @param string $notice_type
	 * @return void
	 */
	public function get_limit_nonces( $notice_type = '' ) {
		$notices     = wc_get_notices( $notice_type );
		return array_filter(
			$notices,
			function( $notice ) {
				return ( ! empty( $notice['data'] ) && ! empty( $notice['data'][ static::$plugin_info['classes_prefix'] . '-notice' ] ) );
			}
		);
	}

    /**
	 * Pass Limit Notices to Fragments through WOO fragments Hook.
	 *
	 * @return void
	 */
	private function pass_notices_to_fragments() {
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'limit_notices_to_fragments' ), PHP_INT_MAX, 1 );
	}

	/**
	 * Disable Passing limit noticesto Fragments.
	 */
	private function disable_notices_to_fragments() {
		remove_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'limit_notices_to_fragments' ), PHP_INT_MAX );
	}

	/**
	 * Add limit notices to Fragments.
	 *
	 * @return array
	 */
	public function limit_notices_to_fragments( $fragments ) {
		ob_start();
		woocommerce_output_all_notices();
		$notices = ob_get_clean();

		if ( empty( $notices ) ) {
			return $fragments;
		}

		$fragments[ static::$plugin_info['classes_prefix'] . '-notices' ] = $notices;
		return $fragments;
	}

    /**
	 * Disable Add To Cart Message.
	 *
	 * @return void
	 */
	private function trigger_disable_add_to_cart_notice() {
		add_filter( 'wc_add_to_cart_message_html', array( $this, 'disable_add_to_cart_notice' ), PHP_INT_MAX, 3 );
	}

	/**
	 * Disable Add to Cart Message once.
	 *
	 * @param string  $message
	 * @param array   $products
	 * @param boolean $show_qty
	 * @return string
	 */
	public function disable_add_to_cart_notice( $message, $products, $show_qty ) {
		remove_filter( 'wc_add_to_cart_message_html', array( $this, 'disable_add_to_cart_notice' ), PHP_INT_MAX );
		return false;
	}
}
