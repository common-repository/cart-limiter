<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\pages;

use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\MainSettings;
use GPLSCore\GPLS_PLUGIN_WWCLR\pages\AdminPage;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\notice\NoticeUtils;

defined( 'ABSPATH' ) || exit();

/**
 * Settings Page.
 */
class SettingsPage extends AdminPage {

	use NoticeUtils;

	/**
	 * Singular Instance.
	 *
	 * @var self
	 */
	protected static $instance = null;


	/**
	 * MainSettings.
	 *
	 * @var MainSettings
	 */
	protected $main_settings;

	/**
	 * Prepare Page.
	 *
	 * @return void
	 */
	protected function prepare() {
		$this->page_props['page_title']       = esc_html__( 'Cart Limiter', 'cart-limiter' );
		$this->page_props['menu_title']       = esc_html__( 'Cart Limiter [GrandPlugins]', 'cart-limiter' );
		$this->page_props['menu_slug']        = self::$plugin_info['name'] . '-settings';
		$this->page_props['is_woocommerce']   = true;
		$this->page_props['hide_save_button'] = true;
		$this->page_props['tab_key']          = 'action';
		$this->tabs                           = array(
			'totals'  => array(
				'default'    => true,
				'title'      => esc_html__( 'Totals Limits', 'cart-limiter' ),
				'hide_title' => true,
			),
			'qty'     => array(
				'title'      => esc_html__( 'Quantity Limits', 'cart-limiter' ),
				'hide_title' => true,
			),
			'product' => array(
				'title'      => esc_html__( 'Products Limits', 'cart-limiter' ),
				'hide_title' => true,
			),
		);
		$this->assets                         = array(
			array(
				'type'       => 'js',
				'handle'     => self::$plugin_info['name'] . '-admin-actions-js',
				'url'        => self::$plugin_info['url'] . 'assets/dist/js/admin/admin-actions.min.js',
				'dependency' => array( 'jquery' ),
				'localized'  => array(
					'data' => array(
						'prefix'               => self::$plugin_info['classes_prefix'],
						'ajaxURL'              => admin_url( 'admin-ajax.php' ),
						'nonce'                => wp_create_nonce( self::$plugin_info['name'] . '-admin-nonce' ),
						'getRuleGroupAction'   => self::$plugin_info['name'] . '-get-rule-group',
						'search_product_nonce' => wp_create_nonce( 'search-products' ),
						'labels'               => array(
							'search_products' => esc_html__( 'Search products...', 'cart-limiter' ),
						),
					),
				),
			),
		);
	}

	/**
	 * Totals Limit Content.
	 *
	 * @return void
	 */
	protected function totals_tab() {
		$this->main_settings = MainSettings::init();
		$this->main_settings->print_settings( 'totals' );
	}

	/**
	 * Quantity Limits Tab Content.
	 *
	 * @return void
	 */
	protected function qty_tab() {
		$this->main_settings = MainSettings::init();
		$this->main_settings->print_settings( 'qty' );
	}

	/**
	 * Product Limits Tab Content.
	 *
	 * @return void
	 */
	protected function product_tab() {
		$main_settings                                          = MainSettings::init();
		$GLOBALS[ $main_settings->get_id() . '-hide-save-btn' ] = true;
		$this->main_settings = $main_settings;
		$this->main_settings->print_settings( 'product' );
	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	protected function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	/**
	 * Admin Assets.
	 *
	 * @return void
	 */
	public function admin_assets() {
	}
}
