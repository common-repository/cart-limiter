<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR;

use GPLSCore\GPLS_PLUGIN_WWCLR\Base;
use GPLSCore\GPLS_PLUGIN_WWCLR\includes\pages\SettingsPage;
use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\MainSettings;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\LimiterUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\CartUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\TotalsLimiter;
use GPLSCore\GPLS_PLUGIN_WWCLR\QtyLimiter;

defined( 'ABSPATH' ) || exit();


/**
 * CartLimiter Class.
 */
class CartLimiter extends Base {
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
	 * Limiters Repository.
	 *
	 * @var array
	 */
	private $limiters;

	/**
	 * Constructor.
	 *
	 * @param Core  $core
	 * @param array $plugin_info
	 */
	private function __construct() {
		$this->setup();
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
	 * Setup.
	 *
	 * @return void
	 */
	private function setup() {
		$this->settings_page = SettingsPage::init();
		$this->main_settings = MainSettings::init();
		$this->settings      = $this->main_settings->get_settings();

		$this->prepare_limiters();
	}

	/**
	 * Prepare Cart Limiters.
	 *
	 * @return void
	 */
	private function prepare_limiters() {
		$this->limiters[] = TotalsLimiter::init();
		$this->limiters[] = QtyLimiter::init();
		$this->limiters = apply_filters( self::$plugin_info['name'] . '-cart-limiters', $this->limiters );
	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	private function hooks() {

		add_action( 'wp_enqueue_scripts', array( $this, 'front_assets' ) );

		// Added To Cart.
		add_action( 'woocommerce_add_to_cart', array( $this, 'handle_cart_limits_on_added_to_cart' ), PHP_INT_MAX, 6 );

		// === Quantity is Updated. === //
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'handle_cart_limits_on_qty_change' ), PHP_INT_MAX, 4 );

		// Cart Restore Item.
		add_action( 'woocommerce_cart_item_restored', array( $this, 'handle_cart_limits_on_item_restored' ), PHP_INT_MAX, 2 );

		// Check limit at Check Cart Items.
		add_action( 'woocommerce_check_cart_items', array( $this, 'handle_limit_cart_on_checkout' ), PHP_INT_MAX );

		add_action( 'plugin_action_links_' . self::$plugin_info['basename'], array( $this, 'settings_link' ), 5, 1 );
	}

	/**
	 * Settings Link.
	 *
	 * @param array $links Plugin Row Links.
	 * @return array
	 */
	public function settings_link( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . self::$plugin_info['name'] . '-settings' ) ) . '">' . esc_html__( 'Settings' ) . '</a>';
		$links[] = '<a href="' . esc_url( self::$plugin_info['pro_link'] ) . '">' . esc_html__( 'Pro' ) . '</a>';
		return $links;
	}

	/**
	 * Front Assets.
	 *
	 * @return void
	 */
	public function front_assets() {
		wp_enqueue_script( self::$plugin_info['name'] . '-front-actions', self::$plugin_info['url'] . 'assets/dist/js/front/actions.min.js', array( 'jquery' ), self::$plugin_info['version'], true );
		wp_localize_script(
			self::$plugin_info['name'] . '-front-actions',
			str_replace( '-', '_', self::$plugin_info['name'] . '-localize-data' ),
			array(
				'prefix'  => self::$plugin_info['classes_prefix'],
				'ajaxURL' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Handle Limit The Cart on Cart Check.
	 *
	 * @return boolean
	 */
	public function handle_limit_cart_on_checkout() {
		if ( ! $this->is_limit_enabled() ) {
			return;
		}

		foreach ( $this->limiters as $cart_limiter ) {
			if ( ! is_a( $cart_limiter, 'GPLSCore\GPLS_PLUGIN_WWCLR\LimiterInterface' ) ) {
				continue;
			}
			$cart_limiter->validate_limits( null, 'checkout' );
		}
	}

	/**
	 * Handle Cart Limits After add to cart.
	 *
	 * @param string $cart_item_key
	 * @param int    $product_id
	 * @param int    $qty
	 * @param int    $variation_id
	 * @param array  $variation
	 * @param array  $cart_item_data
	 * @return void
	 */
	public function handle_cart_limits_on_added_to_cart( $cart_item_key, $product_id, $qty, $variation_id, $variation, $cart_item_data ) {
		if ( ! $this->is_limit_enabled() ) {
			return true;
		}

		if ( $this->is_qty_changed_checked() && ! $this->get_qty_changed_check_result() ) {
			$this->trigger_disable_add_to_cart_notice();
			return true;
		}

		foreach ( $this->limiters as $cart_limiter ) {
			if ( ! is_a( $cart_limiter, 'GPLSCore\GPLS_PLUGIN_WWCLR\LimiterInterface' ) ) {
				continue;
			}
			$result = $cart_limiter->validate_limits( $cart_item_key );
			if ( ! $result ) {
				$this->resolve_cart_after_add_to_cart( $cart_item_key );
				return;
			}
		}
	}

	/**
	 * Handle Cart Limits after cart item is restored.
	 *
	 * @param string   $cart_item_key
	 * @param \WC_Cart $cart
	 * @return void
	 */
	public function handle_cart_limits_on_item_restored( $cart_item_key, $cart ) {
		if ( ! $this->is_limit_enabled() ) {
			return;
		}

		foreach ( $this->limiters as $cart_limiter ) {
			if ( ! is_a( $cart_limiter, 'GPLSCore\GPLS_PLUGIN_WWCLR\LimiterInterface' ) ) {
				continue;
			}
			$result = $cart_limiter->validate_limits( $cart_item_key );
			if ( ! $result ) {
				$this->resolve_cart_after_item_restored( $cart_item_key );
				return;
			}
		}
	}

	/**
	 * Handle Cart Limits after Changing Quantity.
	 *
	 * @param string   $cart_item_key
	 * @param int      $qty
	 * @param int      $old_qty
	 * @param \WC_Cart $cart
	 * @return void
	 */
	public function handle_cart_limits_on_qty_change( $cart_item_key, $qty, $old_qty, $cart ) {
		if ( ! $this->is_limit_enabled() ) {
			return;
		}

		// 1) Prepare Cart after Quantity change in order to apply cart limits checks.
		$cart->calculate_totals();

		foreach ( $this->limiters as $cart_limiter ) {
			if ( ! is_a( $cart_limiter, 'GPLSCore\GPLS_PLUGIN_WWCLR\LimiterInterface' ) ) {
				continue;
			}
			$result = $cart_limiter->validate_limits( $cart_item_key );
			if ( ! $result ) {
				$this->resolve_cart_after_update_qty( $cart_item_key, $old_qty, $cart );
				$this->trigger_qty_changed_is_checked( $result );
				return;
			}
		}
	}

	/**
	 * Resolve Cart limit fail after adding to cart.
	 *
	 * @param string $cart_item_key
	 * @return void
	 */
	private function resolve_cart_after_add_to_cart( $cart_item_key ) {
		$this->trigger_disable_add_to_cart_notice();
		$this->get_cart()->remove_cart_item( $cart_item_key );

		if ( wp_doing_ajax() ) {
			$this->pass_notices_to_fragments();
		}
	}

	/**
	 * Resolve Cart limit fail after cart item restore.
	 *
	 * @param string $cart_item_key
	 * @return void
	 */
	private function resolve_cart_after_item_restored( $cart_item_key ) {
		$this->get_cart()->remove_cart_item( $cart_item_key );
	}

	/**
	 * Resolve Cart Limit fail after change quantity.
	 *
	 * @param string   $cart_item_key
	 * @param int      $old_qty
	 * @param \WC_Cart $cart
	 * @return void
	 */
	private function resolve_cart_after_update_qty( $cart_item_key, $old_qty, $cart ) {
		$cart->cart_contents[ $cart_item_key ]['quantity'] = $old_qty;
		$cart->calculate_totals();
		$this->disable_cart_update_triggered();

		if ( wp_doing_ajax() ) {
			$this->pass_notices_to_fragments();
		}
	}

}
