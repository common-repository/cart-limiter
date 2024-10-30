<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings;

use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields\SettingsFields;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\GeneralUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\notice\NoticeUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\Base;
use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields\Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings Class
 */
abstract class Settings extends Base {

	use GeneralUtils;
	use NoticeUtils;

	/**
	 * Settings ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Settings Key.
	 *
	 * @var string
	 */
	protected $settings_key;

	/**
	 * Settings
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Settings Fields Object.
	 *
	 * @var SettingsFields
	 */
	protected $settings_fields;


	/**
	 * Default Settings
	 *
	 * @var array
	 */
	protected $default_settings = array();

	/**
	 * Default Settings Fields
	 *
	 * @var array
	 */
	protected $default_settings_fields = array();

	/**
	 * Allow Direct Submit.
	 *
	 * @var boolean
	 */
	protected $allow_direct_submit = true;

	/**
	 * Allow AJAX Save.
	 *
	 * @var boolean
	 */
	protected $allow_ajax_submit = false;

	/**
	 * User Cap to save.
	 *
	 * @var string
	 */
	protected $cap = 'administrator';

	/**
	 * Is the Settings autoloaded.
	 *
	 * @var boolean
	 */
	protected $autoload = false;

	/**
	 * Is WooCommerce Settings.
	 *
	 * @var boolean
	 */
	protected $is_woocommerce = false;

	/**
	 * Settings Nonce.
	 *
	 * @var string
	 */
	protected $nonce;


	/**
	 * Tab Key.
	 *
	 * @var string
	 */
	protected $tab_key = 'tab';

	/**
	 * Settings Field.
	 *
	 * @var Field
	 */
	protected $field;

	/**
	 * Settings Constructor.
	 */
	public function __construct() {
		$this->setup();
		$this->base_hooks();
	}

	/**
	 * Init Settings.
	 *
	 * @return object
	 */
	public static function init() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Hooks function.
	 *
	 * @return void
	 */
	public function base_hooks() {
		add_action( $this->id . '-form-submit', array( $this, 'submit_save_settings' ) );

		if ( $this->allow_ajax_submit ) {
			add_action( 'wp_ajax_' . $this->id, array( $this, 'ajax_save_settings' ) );
		}

		if ( $this->is_woocommerce ) {
			add_action( $this->id . '-form-close-submit-fields', array( $this, 'woo_submit_fields' ) );
		}

		if ( method_exists( $this, 'hooks' ) ) {
			$this->hooks();
		}

	}

	/**
	 * Setup Settings.
	 *
	 * @return void
	 */
	public function setup() {
		$this->prepare();
		$this->after_prepare();
	}

	/**
	 * After Preparing Settings.
	 *
	 * @return void
	 */
	private function after_prepare() {
		$this->prepare_default_settings();
		$this->settings_key    = $this->id . '-settings-key';
		$this->settings        = $this->get_settings();
		$this->settings_fields = new SettingsFields( $this->id, $this->fields, $this->settings );
		$this->nonce           = wp_create_nonce( $this->id . '-settings-nonce' );
		$this->field           = new Field();
	}

	/**
	 * Set ID and Settings Fields.
	 *
	 * @return void
	 */
	abstract protected function prepare();

	/**
	 * Get Default Settings.
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		return $this->default_settings;
	}

	/**
	 * Get Settings Values.
	 *
	 * @return array|string
	 */
	public function get_settings( $main_key = null ) {
		$settings = maybe_unserialize( get_option( $this->settings_key, $this->default_settings ) );
		if ( $settings ) {
			$settings = array_replace_recursive( $this->default_settings, $settings );
		} else {
			$settings = $this->default_settings;
		}

		// Handle sub-fields.
		foreach ( $this->default_settings_fields as $field_name => $field_arr ) {
			if ( ! empty( $field_arr['default_subitem'] ) ) {
				foreach ( $settings[ $field_name ] as $index => $subfield ) {
					$settings[ $field_name ][ $index ] = array_merge( $field_arr['default_subitem'], $subfield );
				}
			}
		}

		if ( ! is_null( $main_key ) ) {
			if ( ! isset( $settings[ $main_key ] ) ) {
				return false;
			}
			return $settings[ $main_key ];
		}
		return $settings;
	}

	/**
	 * Print Settinsg HTML.
	 *
	 * @return void
	 */
	public function print_settings( $tab, $full_form = true ) {

		do_action( $this->id . '-form-submit' );
		do_action( $this->id . '-form-submit-' . $tab );

		if ( $full_form ) {
			self::form_open();
		}

		$this->settings_listing_html( $tab );

		if ( $full_form ) {
			$this->form_close();
		}

		do_action( $this->id . '-settings-tabs-action', $this->settings );
	}

	/**
	 * Get Settings Field.
	 *
	 * @param string $tab
	 * @param string $section
	 * @param string $field
	 * @return array|false
	 */
	public function get_settings_field( $key ) {
		return $this->settings_fields->get_settings_field( $key, true );
	}

	/**
	 * Save Settings using Submit.
	 *
	 * @return void
	 */
	public function submit_save_settings() {
		// 2) Check tab nonce.
		if ( ! empty( $_POST[ $this->id . '-settings-nonce' ] ) && wp_verify_nonce( wp_unslash( $_POST[ $this->id . '-settings-nonce' ] ), $this->id . '-settings-nonce' ) ) {

			// Check user cap.
			if ( ! current_user_can( $this->cap ) ) {
				$this->add_error( sprintf( esc_html__( 'You need a higher level of permission.', '%s' ), self::$plugin_info['name'] ) );
				return;
			}

			$this->save_settings();
		}
	}

	public function ajax_save_settings() {
		if ( wp_doing_ajax() && is_admin() && ! empty( $_POST['context'] ) ) {
			// Nonce Check.
			check_ajax_referer( $this->page_nonce, 'nonce' );

			// Cap Check.
			if ( ! current_user_can( $this->cap ) ) {
				wp_die(
					'<h1>' . esc_html__( 'You need a higher level of permission.' ) . '</h1>',
					403
				);
			}

			$this->save_settings();
		}

		wp_die( -1, 403 );
	}

	/**
	 * Save Settings.
	 *
	 * @return void
	 */
	private function save_settings() {
		$tab = $this->get_current_tab();
		if ( ! $tab ) {
			return;
		}

		$settings     = $this->get_settings();
		$old_settings = $settings;
		$fields       = $this->get_fields_for_save( $tab );

		if ( ! empty( $_post[ $this->id ] ) ) {
			return;
		}

		// Before tab Save.
		do_action( $this->id . '-before-settings-save', $settings, $fields );

		foreach ( $fields as $field_key => $field_arr ) {
			if ( 'repeater' === $field_arr['type'] ) {
				$value = $this->settings_fields->sanitize_submitted_repeater_field( $field_key, $settings );
			} else {
				$value = $this->settings_fields->sanitize_submitted_field( $field_key, $field_key, $settings[ $field_key ] );
			}
			$settings[ $field_key ] = $value;
		}

		$settings = apply_filters( $this->id . '-filter-settings-before-saving', $settings, $old_settings, $this, $tab );

		$saving = apply_filters( $this->id . '-just-before-saving', true, $settings, $this, $tab );

		if ( $saving ) {
			update_option( $this->settings_key, $settings, $this->autoload );
			$this->refresh_settings();
		}

		// after tab save.
		do_action( $this->id . '-after-settings-save', $settings, $this, $tab, $saving );

		if ( $saving ) {
			$this->add_message( sprintf( esc_html__( 'Settings have been saved.', '%s' ), self::$plugin_info['name'] ) );
		}
	}

	private function refresh_settings() {
		$this->settings = $this->get_settings();
		$this->settings_fields->refresh_settings( $this->settings );
	}

	/**
	 * Form HTML Open
	 *
	 * @return void
	 */
	public function form_open() {
		do_action( $this->id . '-before-form-open', $this->id );
		?>
		<form method="post" id="mainform" action enctype="multipart/form-data">
		<?php
	}

	/**
	 * Form HTML Close.
	 *
	 * @return void
	 */
	public function form_close() {
		if ( empty( $GLOBALS[ $this->id . '-hide-save-btn' ] ) ) :
		?>
			<p class="submit">
				<button name="save" class="button-primary" type="submit" value="Save Changes"><?php esc_html_e( 'Save changes', '' ); ?></button>
				<input type="hidden" id="<?php echo esc_attr( $this->id . '-settings-nonce' ); ?>" name="<?php echo esc_attr( $this->id . '-settings-nonce' ); ?>" value="<?php echo esc_attr( $this->nonce ); ?>">
				<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( wp_get_referer() ); ?>" />
				<?php do_action( $this->id . '-form-close-submit-fields' ); ?>
			</p>
		</form>
		<?php
		endif;
		do_action( $this->id . '-after-form-close', $this->id );
	}

	/**
	 * List settings HTML.
	 *
	 * @return void
	 */
	protected function settings_listing_html( $tab ) {
		if ( empty( $this->fields ) ) {
			return;
		}
		$this->show_messages();
		?>
		<div class="<?php echo esc_attr( $this->id . '-settings-wrapper' ); ?> bg-white shadow-lg px-4 py-3" >
			<?php
			$this->base_css();
			if ( method_exists( $this, 'inline_css' ) ) {
				$this->inline_css();
			}
			?>
			<div class="container-fluid">
				<div class="row">
					<div class="col">
						<div class="settings-list row">
							<?php
							$tab_settings = $this->get_fields_for_listing( $tab );
							foreach ( $tab_settings as $section_name => $section_arr ) :
								?>
							<!-- Section -->
							<div class="tab-section-wrapper <?php echo esc_attr( 'tab-section-' . $section_name ); ?> col-12 my-3 p-3 bg-white shadow-lg">
								<?php if ( ! empty( $section_arr['section_title'] ) ) : ?>
									<h4><?php printf( esc_html__( '%s', '%s' ), $section_arr['section_title'], self::$plugin_info ); /* translators: %s Settings Section Title */ ?></h4>
								<?php endif; ?>
								<?php if ( ! empty( $section_arr['section_heading'] ) ) : ?>
									<span><?php printf( esc_html__( '%s', '%s' ), $section_arr['section_heading'], self::$plugin_info ); /* translators: %s Settings Section Heading */ ?></span>
								<?php endif; ?>
								<?php do_action( $this->id . '-before-settings-fields', $this ); ?>
								<div class="container-fluid border mt-4">
									<?php
									foreach ( $section_arr['settings_list'] as $field_name => $field_arr ) :
										$field_arr      = array_merge( array( 'key' => $field_name ), $field_arr );
										$settings_field = $this->field->new_field( $this->id, $field_arr );
										if ( is_null( $settings_field ) ) {
											continue;
										}
										$settings_field->get_field();
									endforeach;
									?>
								</div>
								<?php do_action( $this->id . '-' . $tab . '-after-settings-fields', $this ); ?>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}


	/**
	 * Get Settings for Fields Listing.
	 *
	 * @return array
	 */
	public function get_fields_for_listing( $tab ) {
		$settings_fields = $this->fields[ $tab ];

		foreach ( $settings_fields as $section_name => $section_settings ) {
			foreach ( $section_settings['settings_list'] as $setting_name => $setting_arr ) {
				$settings_fields[ $section_name ]['settings_list'][ $setting_name ]['value'] = $this->settings[ $setting_name ];
			}
		}

		$settings_fields = apply_filters( $this->id . '-settings-fields', $settings_fields );

		return $settings_fields;
	}

	/**
	 * Get Settings Fields.
	 *
	 * @return array
	 */
	public function get_fields( $tab = null ) {
		return ( is_null( $tab ) ? $this->fields : ( ! empty( $this->fields[ $tab ] ) ? $this->fields[ $tab ] : array() ) );
	}

	/**
	 * Get Repeater Item Array.
	 *
	 * @param string $field_key
	 * @param string $repeater_item_key
	 * @return array
	 */
	public function get_repeater_item( $field_key, $repeater_item_key, $index = null ) {
		$repeater_item_field = $this->default_settings_fields[ $field_key ]['default_subitem'][ $repeater_item_key ];
		if ( is_null( $repeater_item_field ) ) {
			return $repeater_item_field;
		}
		$repeater_item_value          = $this->settings[ $field_key ][ $index ][ $repeater_item_key ] ?? $repeater_item_field['value'];
		$repeater_item_field['value'] = $repeater_item_value;
		return $repeater_item_field;
	}

		/**
		 * Get Repeater Field HTML.
		 *
		 * @param string  $field_key
		 * @param string  $repeater_item_key
		 * @param int     $index
		 * @param array   $field
		 * @param boolean $full_field
		 * @param boolean $echo
		 * @param boolean $ignore_hide
		 * @return mixed
		 */
	public function get_repeater_item_field_html( $field_key, $repeater_item_key, $index, $custom_attrs = array(), $full_field = true, $echo = true, $ignore_hide = true ) {
		$repeater_field = $this->get_repeater_item( $field_key, $repeater_item_key, $index );
		$repeater_field = array_merge( $repeater_field, $custom_attrs );
		return $this->settings_fields->get_repeater_field_html( $field_key, $repeater_item_key, $index, $repeater_field, $full_field, $echo, $ignore_hide );
	}

	/**
	 * Get Settings Field.
	 *
	 * @param string|array $field
	 * @return string|false
	 */
	public function get_field_html( $field, $full_field = true, $echo = true, $ignore_hide = true ) {
		if ( is_string( $field ) ) {
			$field_key = $field;
			$field     = $this->get_settings_field( $field_key, true );

			if ( ! $field ) {
				return;
			}

			$field['key'] = $field_key;
		}

		$field['filter'] = $field['filter'] ?? $field['key'];
		$settings_field  = $this->field->new_field( $this->id, $field );

		return $settings_field->get_field( $full_field, $echo, $ignore_hide );
	}

	/**
	 * Get Field.
	 *
	 * @param string $field_key
	 * @return array
	 */
	public function get_field( $field_key ) {
		return $this->default_settings_fields[ $field_key ];
	}

	/**
	 * Get Fields for Save.
	 *
	 * @param string $tab
	 * @return array
	 */
	public function get_fields_for_save( $tab ) {
		$fields                   = $this->get_fields();
		$prepared_settings_fields = array();

		if ( empty( $fields[ $tab ] ) ) {
			return array();
		}

		foreach ( $fields[ $tab ] as $section_name => $section_settings ) {
			if ( ! empty( $section_settings['settings_list'] ) ) {
				foreach ( $section_settings['settings_list'] as $setting_name => $setting_arr ) {
					$prepared_settings_fields[ $setting_name ]           = $setting_arr;
					$prepared_settings_fields[ $setting_name ]['key']    = $setting_name;
					$prepared_settings_fields[ $setting_name ]['filter'] = $setting_name;
				}
			}
		}

		return $prepared_settings_fields;
	}

	/**
	 * Prepare Default settings.
	 *
	 * @return void
	 */
	protected function prepare_default_settings() {
		$prepared_settings        = array();
		$prepared_settings_fields = array();

		foreach ( $this->fields as $tab_name => &$sections ) {
			foreach ( $sections as $section_name => &$section_settings ) {
				if ( ! empty( $section_settings['settings_list'] ) ) {
					foreach ( $section_settings['settings_list'] as $setting_name => &$setting_arr ) {
						$prepared_settings[ $setting_name ]                  = $setting_arr['value'];
						$prepared_settings_fields[ $setting_name ]           = $setting_arr;
						$prepared_settings_fields[ $setting_name ]['key']    = $setting_name;
						$prepared_settings_fields[ $setting_name ]['filter'] = $setting_name;

						// Repeater Field.
						if ( 'repeater' === $setting_arr['type'] ) {
							foreach ( $setting_arr['default_subitem'] as $repeater_field_name => &$repeater_field_arr ) {
								$repeater_field_arr['filter'] = $setting_name . '-' . $repeater_field_name;
							}
						}
					}
				}
			}
		}

		$this->default_settings        = $prepared_settings;
		$this->default_settings_fields = $prepared_settings_fields;
	}

	/**
	 * Get Settings ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get Settings Key.
	 *
	 * @return string
	 */
	public function get_settings_key() {
		return $this->settings_key;
	}

	/**
	 * Woo Submit Fields.
	 *
	 * @return void
	 */
	public function woo_submit_fields() {
		wp_nonce_field( 'woocommerce-settings' );
	}

	/**
	 * Settings Base CSS.
	 *
	 * @return void
	 */
	private function base_css() {
		?>
		<style>
		.astrodivider i,.astrodivider span{position:absolute;border-radius:100%}.astrodivider{margin:64px auto;width:100%;max-width:100%;position:relative}.astrodividermask{overflow:hidden;height:20px}.astrodividermask:after{content:'';display:block;margin:-25px auto 0;width:100%;height:25px;border-radius:125px/12px;box-shadow:0 0 8px #8cade7}.astrodivider span{width:50px;height:50px;bottom:100%;margin-bottom:-25px;left:50%;margin-left:-25px;box-shadow:0 2px 4px #3f4acb;background:#fff}.astrodivider i{top:4px;bottom:4px;left:4px;right:4px;border:1px dashed #68beaa;text-align:center;line-height:40px;font-style:normal;color:#c1006b}
		</style>
		<?php
	}

	/**
	 * Get Current Tab.
	 *
	 * @return string|false
	 */
	private function get_current_tab() {
		return ! empty( $_GET[ $this->tab_key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $this->tab_key ] ) ) : $this->get_first_tab();
	}

	/**
	 * Get First Tab.
	 *
	 * @return void
	 */
	private function get_first_tab() {
		return array_key_first( $this->fields );
	}
}
