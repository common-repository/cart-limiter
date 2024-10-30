<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR;

defined( 'ABSPATH' ) || exit();

use GPLSCore\GPLS_PLUGIN_WWCLR\Base;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\CartUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\LimiterUtils;

/**
 * Single Limiter Class.
 */
class SingleLimiter  extends Base {

	use LimiterUtils;
	use CartUtils;

	/**
	 * ID.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Singular Instance.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Settings Key.
	 *
	 * @var string
	 */
	private $settings_key;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	private $default_settings = array();

	/**
	 * Singular Instance init.
	 *
	 * @return self
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->setup();
		$this->hooks();
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	private function setup() {
		$this->id                 = self::$plugin_info['name'] . '-single-qty-limiter';
		$this->settings_key       = $this->id . '-settings-key';
		$this->variation_main_key = self::$plugin_info['name'] . '-variation-field';
		$this->default_settings   = array(
			'exclude_global'              => 'no',
			'min_limit'                   => 0,
			'min_msg'                     => '',
			'max_limit'                   => 0,
			'max_msg'                     => '',
		);
	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	private function hooks() {
		// Coming Soon Settings in Products edit page.
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'cart_limiter_tab_in_single_product' ), 100, 1 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'cart_limiter_tab_in_single_product_settings' ) );
		add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_cart_limiter_settings' ), 100, 1 );

		// Variation.
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'cart_limiter_for_variation' ), 1000, 3 );
		add_action( 'woocommerce_ajax_save_product_variations', array( $this, 'save_variations_cart_limiter_settings' ), 10, 1 );
	}

	/**
	 * Cart Limiter Tab Registeration.
	 *
	 * @param array $tabs
	 * @return array
	 */
	public function cart_limiter_tab_in_single_product( $tabs ) {
		$tabs[ $this->id ] = array(
			'label'    => esc_html__( 'Cart Limiter [GrandPlugins]', '' ),
			'target'   => $this->id . '-settings-tab',
			'class'    => array(),
			'priority' => 60,
			'icon'     => 'dashicons-dashboard',
		);
		return $tabs;
	}

	/**
	 * Single Product Cart Limiter Settings.
	 *
	 * @return void
	 */
	public function cart_limiter_tab_in_single_product_settings() {
		global $post, $thepostid, $product_object;
		if ( ! $thepostid || ! $product_object || is_wp_error( $product_object ) ) {
			return;
		}
		$cart_limiter_settings = $this->get_settings( $thepostid );
		?>
		<div id="<?php echo esc_attr( $this->id . '-settings-tab' ); ?>" class="panel woocommerce_options_panel" >
			<div class="options-group">
			<?php
			woocommerce_wp_checkbox(
				array(
					'id'          => self::$plugin_info['name'] . '-exclude_global',
					'value'       => $cart_limiter_settings['exclude_global'],
					'label'       => esc_html__( 'Exclude Global', 'cart-limiter' ),
					'description' => esc_html__( 'Exclude the product from global quantity limits', 'cart-limiter' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => self::$plugin_info['name'] . '-min_limit',
					'type'              => 'number',
					'value'             => $cart_limiter_settings['min_limit'],
					'label'             => esc_html__( 'Min Limit', 'cart-limiter' ),
					'description'       => esc_html__( 'Min limit allowed to buy the product. set 0 to disable', 'cart-limiter' ),
					'custom_attributes' => array(
						'min' => 0,
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => self::$plugin_info['name'] . '-min_msg',
					'value'       => $cart_limiter_settings['min_msg'],
					'label'       => esc_html__( 'Min Limit Message', 'cart-limiter' ),
					'description' => esc_html__( 'Cart notice message for the min limit.', 'cart-limiter' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => self::$plugin_info['name'] . '-max_limit',
					'type'              => 'number',
					'value'             => $cart_limiter_settings['max_limit'],
					'label'             => esc_html__( 'Max Limit', 'cart-limiter' ),
					'description'       => esc_html__( 'Max limit allowed to buy the product. set 0 to disable', 'cart-limiter' ),
					'custom_attributes' => array(
						'min' => 0,
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => self::$plugin_info['name'] . '-max_msg',
					'value'       => $cart_limiter_settings['max_msg'],
					'label'       => esc_html__( 'Max Limit Message', 'cart-limiter' ),
					'description' => esc_html__( 'Cart notice message for the max limit.', 'cart-limiter' ),
				)
			);
			?>
			</div>
		</div>
			<?php
	}

	/**
	 * Cart Limiter Settings for Variations.
	 *
	 * @param int                   $loop
	 * @param array                 $variation_data
	 * @param \WC_Product_Variation $variation
	 * @return void
	 */
	public function cart_limiter_for_variation( $loop, $variation_data, $variation ) {
		$cart_limiter_settings = self::get_settings( $variation->ID );
		?>
		<div class="<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-variation-cart-limiter-box wc-metabox woocommerce_attribute postbox closed' ); ?>" style="padding-top:5px;background:#d5d2ff;">
			<h3>
				<div class="handlediv" aria-expanded="true" title="Click to toggle"></div>
				<div class="attribute_name"><?php esc_html_e( 'Cart Limiter [GrandPlugins]', 'cart-limiter' ); ?></div>
			</h3>
			<div class="woocommerce_attribute_data wc-metabox-content hidden">
				<div class="woocommerce_options_panel">
					<div class="options_group">
					<?php
					woocommerce_wp_checkbox(
						array(
							'id'          => $this->variation_main_key . '-' . $variation->ID . '-exclude_global',
							'name'        => $this->variation_main_key . '[' . $variation->ID . '][exclude_global]',
							'value'       => $cart_limiter_settings['exclude_global'],
							'label'       => esc_html__( 'Exclude Global', 'cart-limiter' ),
							'description' => esc_html__( 'Exclude the product from global quantity limits', 'cart-limiter' ),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'                => $this->variation_main_key . '-' . $variation->ID . '-min_limit',
							'name'              => $this->variation_main_key . '[' . $variation->ID . '][min_limit]',
							'type'              => 'number',
							'value'             => $cart_limiter_settings['min_limit'],
							'label'             => esc_html__( 'Min Limit', 'cart-limiter' ),
							'description'       => esc_html__( 'Min limit allowed to buy the product. set 0 to disable', 'cart-limiter' ),
							'custom_attributes' => array(
								'min' => 0,
							),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'          => $this->variation_main_key . '-' . $variation->ID . '-min_msg]',
							'name'        => $this->variation_main_key . '[' . $variation->ID . '][min_msg]',
							'value'       => $cart_limiter_settings['min_msg'],
							'label'       => esc_html__( 'Min Limit Message', 'cart-limiter' ),
							'description' => esc_html__( 'Cart notice message for the min limit.', 'cart-limiter' ),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'                => $this->variation_main_key . '-' . $variation->ID . '-max_limit]',
							'name'              => $this->variation_main_key . '[' . $variation->ID . '][max_limit]',
							'type'              => 'number',
							'value'             => $cart_limiter_settings['max_limit'],
							'label'             => esc_html__( 'Max Limit', 'cart-limiter' ),
							'description'       => esc_html__( 'Max limit allowed to buy the product. set 0 to disable', 'cart-limiter' ),
							'custom_attributes' => array(
								'min' => 0,
							),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'          => $this->variation_main_key . '-' . $variation->ID . '-max_msg]',
							'name'        => $this->variation_main_key . '[' . $variation->ID . '][max_msg]',
							'value'       => $cart_limiter_settings['max_msg'],
							'label'       => esc_html__( 'Max Limit Message', 'cart-limiter' ),
							'description' => esc_html__( 'Cart notice message for the max limit.', 'cart-limiter' ),
						)
					);
					?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save Cart Limiter Settings.
	 *
	 * @param WC_Product $product
	 * @return void
	 */
	public function save_cart_limiter_settings( $product ) {
		$settings = $this->default_settings;

		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-exclude_global' ] ) ) {
			$settings['exclude_global'] = 'yes';
		}

		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-min_limit' ] ) ) {
			$settings['min_limit'] = absint( sanitize_text_field( $_POST[ self::$plugin_info['name'] . '-min_limit' ] ) );
		}

		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-max_limit' ] ) ) {
			$settings['max_limit'] = absint( sanitize_text_field( $_POST[ self::$plugin_info['name'] . '-max_limit' ] ) );
		}

		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-min_msg' ] ) ) {
			$settings['min_msg'] = sanitize_text_field( $_POST[ self::$plugin_info['name'] . '-min_msg' ] );
		}

		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-max_msg' ] ) ) {
			$settings['max_msg'] = sanitize_text_field( $_POST[ self::$plugin_info['name'] . '-max_msg' ] );
		}

		update_post_meta( $product->get_id(), $this->settings_key, $settings );
	}

	/**
	 * Save Variations Cart Limiter Settings.
	 *
	 * @param int $product_id
	 * @return void
	 */
	public function save_variations_cart_limiter_settings( $product_id ) {
		if ( ! empty( $_POST[ $this->variation_main_key ] ) ) {
			$cart_limiter_settings = wp_unslash( $_POST[ $this->variation_main_key ] );
			foreach ( $cart_limiter_settings as $variation_id => $variation_cart_limiter_settings ) {
				$variation_id      = absint( sanitize_text_field( $variation_id ) );
				$variation_product = wc_get_product( $variation_id );
				if ( ! $variation_product || ( $variation_product && 'variation' !== $variation_product->get_type() ) ) {
					continue;
				}

				$settings = $this->default_settings;

				if ( ! empty( $variation_cart_limiter_settings['exclude_global'] ) ) {
					$settings['exclude_global'] = 'yes';
				}

				if ( ! empty( $variation_cart_limiter_settings['min_limit'] ) ) {
					$settings['min_limit'] = absint( sanitize_text_field( $variation_cart_limiter_settings['min_limit'] ) );
				}

				if ( ! empty( $variation_cart_limiter_settings['max_limit'] ) ) {
					$settings['max_limit'] = absint( sanitize_text_field( $variation_cart_limiter_settings['max_limit'] ) );
				}

				if ( ! empty( $variation_cart_limiter_settings['min_msg'] ) ) {
					$settings['min_msg'] = sanitize_text_field( $variation_cart_limiter_settings['min_msg'] );
				}

				if ( ! empty( $variation_cart_limiter_settings['max_msg'] ) ) {
					$settings['max_msg'] = sanitize_text_field( $variation_cart_limiter_settings['max_msg'] );
				}

				update_post_meta( $variation_id, $this->settings_key, $settings );
			}
		}
	}

	/**
	 * Check if product limits disabled.
	 *
	 * @param int    $product_id
	 * @param string $limit_type
	 * @return boolean
	 */
	private function is_limits_disabled( $product_id, $limit_type ) {
		return ( 'yes' === $this->get_settings( $product_id, $limit_type ) );
	}

	/**
	 * Check if is item is excluded from Limits.
	 *
	 * @param array|int $products_ids
	 * @param string    $exclude_type
	 * @return boolean
	 */
	public function is_item_excluded( $products_ids, $exclude_type ) {
		if ( empty( $products_ids ) || is_null( $products_ids ) ) {
			return false;
		}

		if ( is_int( $products_ids ) ) {
			$products_ids = array( $products_ids );
		}

		foreach ( $products_ids as $product_id ) {
			if ( $this->is_limits_disabled( $product_id, $exclude_type ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get Single Product Limit.
	 *
	 * @param int    $product_id
	 * @param string $limit_type
	 * @return int
	 */
	public function get_single_limit( $product_id, $limit_type = 'min' ) {
		return $this->get_settings( $product_id, $limit_type . '_limit' );
	}

	/**
	 * Get Single Product Limit Message.
	 *
	 * @param int    $product_id
	 * @param string $limit_type
	 * @return int
	 */
	public function get_single_limit_msg( $product_id, $limit_type = 'min' ) {
		return $this->get_settings( $product_id, $limit_type . '_msg' );
	}

	/**
	 * Get Single Qty Limits.
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_single_qty_limits( $product_id ) {
		return array(
			'min_limit' => array(
				'qty' => $this->get_single_limit( $product_id, 'min' ),
				'msg' => $this->get_single_limit_msg( $product_id, 'min' ),
			),
			'max_limit' => array(
				'qty' => $this->get_single_limit( $product_id, 'max' ),
				'msg' => $this->get_single_limit_msg( $product_id, 'max' ),
			),
		);
	}

	/**
	 * Get Settings.
	 *
	 * @param int          $product_id
	 * @param settings_key $string
	 * @return mixed
	 */
	public function get_settings( $product_id, $settings_key = null ) {
		$settings = $this->default_settings;
		if ( ! is_null( $settings_key ) && ! in_array( $settings_key, array_keys( $settings ) ) ) {
			return false;
		}
		$saved_settings = maybe_unserialize( get_post_meta( $product_id, $this->settings_key, true ) );
		$saved_settings = empty( $saved_settings ) ? $settings : array_merge( $settings, $saved_settings );
		return is_null( $settings_key ) ? $saved_settings : $saved_settings[ $settings_key ];
	}

	/**
	 * Validate Single Quantity Limit.
	 *
	 * @param string $cart_item_key
	 * @return boolean
	 */
	public function validate_single_qty_limit( $cart_item_key = null ) {
		$cart_item_keys = is_null( $cart_item_key ) ? $this->get_cart_keys() : array( $cart_item_key );
		foreach ( $cart_item_keys as $cart_single_item_key ) {
			$item_ids      = $this->resolve_cart_items_ids( $cart_single_item_key );
			$cart_item_arr = $this->get_cart_item( $cart_single_item_key );
			foreach ( $item_ids as $item_id ) {
				foreach ( array( 'min', 'max' ) as $limit_type ) {
					$single_product_limit = $this->get_single_limit( $item_id, $limit_type );
					if ( $single_product_limit && $this->qty_compare( 'min' === $limit_type ? '<' : '>', $cart_item_arr['quantity'], $single_product_limit ) ) {
						$this->add_notice( $this->get_single_limit_msg( $item_id, $limit_type ), 'qty', 'single_qty_limit', $item_id );
						return false;
					}
				}
			}
		}
		return true;
	}

}
