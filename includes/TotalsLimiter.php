<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR;

use GPLSCore\GPLS_PLUGIN_WWCLR\Base;
use GPLSCore\GPLS_PLUGIN_WWCLR\includes\pages\SettingsPage;
use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\MainSettings;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\LimiterUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\CartUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\LimiterInterface;

defined( 'ABSPATH' ) || exit();
/**
 * Totals Related Limiter Class.
 */
class TotalsLimiter extends Base implements LimiterInterface {
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
	 * Constructor.
	 *
	 * @param Core  $core
	 * @param array $plugin_info
	 */
	private function __construct() {

		$this->settings_page = SettingsPage::init();
		$this->main_settings = MainSettings::init();
		$this->settings      = $this->main_settings->get_settings();

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
	 * Hooks.
	 *
	 * @return void
	 */
	private function hooks() {

	}

	/**
	 * Validate Totals Limits.
	 *
	 * @param string|null $cart_item_key
	 * @return boolean
	 */
	public function validate_limits( $cart_item_key = null, $context = '' ) {
		return $this->validate_totals_limit( $cart_item_key, $context );
	}

	/**
	 * Validate Totals Limits.
	 *
	 * @return boolean
	 */
	public function validate_totals_limit( $cart_item_key, $context ) {
		$result = $this->validate_totals_cart_total_limit();
		if ( ! $result ) {
			$this->add_notice( $this->get_totals_limit_notice( 'totals_cart_total' ), 'totals', 'totals_cart_total' );
			return $result;
		}

		if ( 'checkout' === $context ) {
			$result = $this->validate_totals_min_cart_total_limit();
			if ( ! $result ) {
				$this->add_notice( $this->get_totals_limit_notice( 'totals_min_cart_total' ), 'totals', 'totals_min_cart_total' );
			}
		}

		$result = $this->validate_totals_products_count_limit();
		if ( ! $result ) {
			$this->add_notice( $this->get_totals_limit_notice( 'totals_products_num' ), 'totals', 'totals_products_num' );
			return $result;
		}

		$result = $this->validate_totals_cart_qty_limit();
		if ( ! $result ) {
			$this->add_notice( $this->get_totals_limit_notice( 'totals_qty_num' ), 'totals', 'totals_qty_num' );
			return $result;
		}

		return true;
	}


	/**
	 * Validate Cart Minimum total Limit.
	 *
	 * @param string $cart_item_key
	 *
	 * @return boolean
	 */
	public function validate_totals_min_cart_total_limit( $cart_item_key = null ) {
		$cart_total       = number_format( $this->get_cart_total(), 2 );
		$cart_total_limit = $this->get_cart_min_total_limit();
		return ( ! $cart_total_limit || ( $cart_total_limit <= $cart_total ) );
	}

	/**
	 * Validate Cart total Limit.
	 *
	 * @param string $cart_item_key
	 *
	 * @return boolean
	 */
	public function validate_totals_cart_total_limit( $cart_item_key = null ) {
		$cart_total       = number_format( $this->get_cart_total(), 2 );
		$cart_total_limit = $this->get_cart_max_total_limit();
		return ( ! $cart_total_limit || ( $cart_total_limit >= $cart_total ) );
	}

	/**
	 * Validate Products Quantities in Cart limit.
	 *
	 * @param string $cart_item_key
	 *
	 * @return boolean
	 */
	public function validate_totals_products_count_limit( $cart_item_key = null ) {
		$cart_items_count       = count( $this->get_cart_contents() );
		$cart_items_count_limit = $this->get_cart_products_count_limit();
		return ( ! $cart_items_count_limit || ( $cart_items_count <= $cart_items_count_limit ) );
	}

	/**
	 * Validate Cart Products Total Quantites Limit.
	 *
	 * @param string $cart_item_key
	 *
	 * @return boolean
	 */
	public function validate_totals_cart_qty_limit( $cart_item_key = null ) {
		$cart_total_qty       = $this->get_cart_total_qty();
		$cart_total_qty_limit = $this->get_cart_qty_limit();
		return ( ! $cart_total_qty_limit || ( $cart_total_qty <= $cart_total_qty_limit ) );
	}

	/**
	 * Get Cart Max Total Limit.
	 *
	 * @return int
	 */
	private function get_cart_max_total_limit() {
		return absint( $this->settings['totals_cart_total'] );
	}

	/**
	 * Get Cart Min Total Limit.
	 *
	 * @return int
	 */
	private function get_cart_min_total_limit() {
		return absint( $this->settings['totals_min_cart_total'] );
	}

	/**
	 * Get Cart Products Count Limit.
	 *
	 * @return int
	 */
	private function get_cart_products_count_limit() {
		return absint( $this->settings['totals_products_num'] );
	}

	/**
	 * Get Cart total quantity Limit.
	 *
	 * @return int
	 */
	private function get_cart_qty_limit() {
		return absint( $this->settings['totals_qty_num'] );
	}

	/**
	 * Get Totals Limits Notice.
	 *
	 * @param string $limit_type
	 * @return string
	 */
	private function get_totals_limit_notice( $limit_type ) {
		return $this->settings[ $limit_type . '_msg' ];
	}


	/**
	 * Get Totals Notice Message.
	 *
	 * @param string $notice_type
	 * @return string
	 */
	private function get_totals_notice( $notice_type ) {
		return $this->settings[ $notice_type ];
	}
}
