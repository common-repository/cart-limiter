<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR;

use GPLSCore\GPLS_PLUGIN_WWCLR\Base;
use GPLSCore\GPLS_PLUGIN_WWCLR\includes\pages\SettingsPage;
use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\MainSettings;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\LimiterUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\CartUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\SingleLimiter;
use GPLSCore\GPLS_PLUGIN_WWCLR\LimiterInterface;

defined( 'ABSPATH' ) || exit();
/**
 * Quantity Related Limiter Class.
 */
class QtyLimiter extends Base implements LimiterInterface {
	use CartUtils;
	use LimiterUtils;

	/**
	 * Singular Instance.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Settings Page.
	 *
	 * @var AdminPage
	 */
	protected $settings_page;

	/**
	 * Main Settings.
	 *
	 * @var MainSettings
	 */
	protected $main_settings;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Limit Types.
	 *
	 * @var array
	 */
	private $limit_types = array( 'min', 'max' );

	/**
	 * Single Limiter Instance.
	 *
	 * @var SingleLimiter
	 */
	private $single_limiter;

	/**
	 * Constructor.
	 *
	 * @param Core  $core
	 * @param array $plugin_info
	 */
	private function __construct() {
		$this->single_limiter = SingleLimiter::init();
		$this->settings_page  = SettingsPage::init();
		$this->main_settings  = MainSettings::init();
		$this->settings       = $this->main_settings->get_settings();

		$this->hooks();
	}

	/**
	 * Singular Instantiation.
	 *
	 * @param core  $core
	 * @param array $plugin_info
	 * @return self
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * hooks.
	 *
	 * @return void
	 */
	private function hooks() {
		// Max - Min Quantity Input Field Limits.
		add_filter( 'woocommerce_quantity_input_args', array( $this, 'filter_qty_allowed' ), PHP_INT_MAX, 2 );
	}

	/**
	 * Filter Products Quantity.
	 *
	 * @param array       $args
	 * @param \WC_Product $_product
	 * @return array
	 */
	public function filter_qty_allowed( $args, $_product ) {
		if ( ! $this->is_limit_enabled() ) {
			return $args;
		}
		if ( ! $this->is_apply_restrict_on_qty_field() ) {
			return $args;
		}

		$product_id = $_product->get_id();
		$product_qty_limits = $this->get_product_qty_limits( $product_id );
		if ( ! $product_qty_limits || ! $product_qty_limits['min_limit']['qty'] && ! $product_qty_limits['max_limit']['qty'] ) {
			return $args;
		}

		// Min Qty.
		if ( $product_qty_limits['min_limit']['qty'] ) {
			if ( ! isset( $args['min_value'] ) || ( 0 === $args['min_value'] ) ) {
				$args['min_value'] = $product_qty_limits['min_limit']['qty'];
			}

			if ( ( $args['min_value'] < $product_qty_limits['min_limit']['qty'] ) && ! $_product->is_sold_individually() ) {
				$args['min_value'] = $product_qty_limits['min_limit']['qty'];
			}
		}

		// Max Qty.
		if ( $product_qty_limits['max_limit']['qty'] ) {
			if ( ! isset( $args['max_value'] ) || -1 === $args['max_value'] ) {
				$args['max_value'] = $product_qty_limits['max_limit']['qty'];
			}

			if ( $args['max_value'] > $product_qty_limits['max_limit']['qty'] ) {
				$args['max_value'] = $product_qty_limits['max_limit']['qty'];
			}
		}

		return $args;
	}

	/**
	 * Validate Qty Limits.
	 *
	 * @param string|null $cart_item_key
	 * @return boolean
	 */
	public function validate_limits( $cart_item_key = null, $context = '' ) {
		return $this->validate_qty_limits( $cart_item_key );
	}

	/**
	 * Validate Quantity Limits.
	 *
	 * @param string $cart_item_key
	 * @return boolean
	 */
	public function validate_qty_limits( $cart_item_key = null ) {
		$result = $this->single_limiter->validate_single_qty_limit( $cart_item_key );
		if ( ! $result ) {
			return $result;
		}

		return $this->validate_global_qty_limit( $cart_item_key );
	}

	/**
	 * Validate Global Quantity Limit.
	 *
	 *  @param string $cart_item_key validate against specific cart item.
	 *
	 * @return boolean
	 */
	public function validate_global_qty_limit( $cart_item_key = null ) {
		$result = $this->_validate_global_qty_limit( 'min', $cart_item_key );
		if ( ! $result ) {
			return $result;
		}

		$result = $this->_validate_global_qty_limit( 'max', $cart_item_key );
		if ( ! $result ) {
			return $result;
		}

		return $result;
	}

	/**
	 * Validate Global Quantity Limit by type.
	 *
	 * @param string $limit_type
	 * @param string $cart_item_key
	 * @return void
	 */
	public function _validate_global_qty_limit( $limit_type = 'min', $cart_item_key = null ) {
		$global_limit = $this->get_global_qty_limit( $limit_type );
		if ( ! $global_limit ) {
			return true;
		}

		$items_failed_validation = array();
		$cart_items              = ! is_null( $cart_item_key ) ? array( $this->get_cart_item( $cart_item_key ) ) : array_values( $this->get_cart_contents() );

		foreach ( $cart_items as $cart_item_arr ) {
			// 1) Get The cart item ( variation + parent ) IDs.
			$cart_item_products_ids = array_filter( array( $cart_item_arr['variation_id'], $cart_item_arr['product_id'] ) );

			// 2) Check if the product is excluded.
			if ( $this->single_limiter->is_item_excluded( $cart_item_products_ids, 'exclude_global' ) ) {
				continue;
			}

			// 4) check global qty limit.
			if ( $this->qty_compare( 'min' === $limit_type ? '<' : '>', $cart_item_arr['quantity'], $global_limit ) ) {
				$items_failed_validation[] = $cart_item_arr['variation_id'] ? $cart_item_arr['variation_id'] : $cart_item_arr['product_id'];
			}
		}
		$failed_items_count = count( $items_failed_validation );
		if ( $failed_items_count ) {
			$this->add_notice( $this->get_global_qty_notice( $limit_type ), 'qty', 'qty_' . $limit_type . '_qty' );
		}

		return $failed_items_count ? false : true;
	}

	/**
	 * Get Product Quantity Limits.
	 *
	 * @param int $product_id
	 * @return array|false
	 */
	public function get_product_qty_limits( $product_id ) {
		$qty_limits = $this->single_limiter->get_single_qty_limits( $product_id );

		// Already have single Limits.
		if ( $qty_limits['min_limit']['qty'] && $qty_limits['max_limit']['qty'] ) {
			return $qty_limits;
		}

		$excluded_from_global = $this->single_limiter->is_item_excluded( $product_id, 'exclude_global' );

		// Excluded from global and custom Qty limits.
		if ( $excluded_from_global ) {
			return false;
		}

		// Last Check, Global Qty Limits.
		if ( $excluded_from_global ) {
			return false;
		}

		if ( ! $qty_limits['min_limit']['qty'] ) {
			$qty_limits['min_limit']['qty'] = $qty_limits['min_limit']['qty'] ? $qty_limits['min_limit']['qty'] : $this->get_global_qty_limit( 'min' );
			$qty_limits['min_limit']['msg'] = $qty_limits['min_limit']['qty'] ? $qty_limits['min_limit']['msg'] : $this->get_global_qty_notice( 'min' );
		}

		if ( ! $qty_limits['max_limit']['qty'] ) {
			$qty_limits['max_limit']['qty'] = $qty_limits['max_limit']['qty'] ? $qty_limits['max_limit']['qty'] : $this->get_global_qty_limit( 'max' );
			$qty_limits['max_limit']['msg'] = $qty_limits['max_limit']['qty'] ? $qty_limits['max_limit']['msg'] : $this->get_global_qty_notice( 'max' );
		}

		return $qty_limits;
	}

	/**
	 * Get Global Quantity ( min - max ) limit.
	 *
	 * @return int
	 */
	private function get_global_qty_limit( $limit_type = 'min' ) {
		return absint( $this->settings[ 'qty_' . $limit_type . '_qty' ] );
	}

	/**
	 * Get Global Min - Max Quantity Limitation Notice.
	 *
	 * @param string $limit_type
	 * @return string
	 */
	private function get_global_qty_notice( $limit_type = 'min' ) {
		return $this->settings[ 'qty_' . $limit_type . '_qty_msg' ];
	}
}
