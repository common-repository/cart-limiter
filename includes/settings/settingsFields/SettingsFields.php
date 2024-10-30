<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields;

use GPLSCore\GPLS_PLUGIN_WWCLR\Base;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\GeneralUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields\Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings Fields Class
 */
class SettingsFields extends Base {

	use GeneralUtils;

	/**
	 * Settings ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Settings Fields.
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Settings Field.
	 *
	 * @var Field
	 */
	protected $field;

	/**
	 * Default Attributes for Kses.
	 *
	 * @var array
	 */
	protected $default_attrs = array(
		'class',
		'id',
		'name',
		'value',
		'type',
		'selected',
		'checked',
		'placeholder',
	);

	/**
	 * Default Input Fields for Kses.
	 *
	 * @var array
	 */
	protected $default_fields = array(
		'input',
		'select',
		'textarea',
		'option',
	);

	/**
	 * Constructor.
	 *
	 * @param string $id
	 */
	public function __construct( $id, $fields, $settings = array() ) {
		$this->id       = $id;
		$this->fields   = $fields;
		$this->settings = $settings;
		$this->field    = new Field();
	}

	/**
	 * Get Field HTML.
	 *
	 * @param array $field
	 * @return void
	 */
	protected function get_field_html( $field ) {
		$settings_field = $this->field->new_field( $this->id, $field );
		$settings_field->get_field_html();
	}

	/**
	 * Get Repeater Field Default Item HTML
	 *
	 * @param array $field
	 * @param index $index
	 * @return string
	 */
	public function get_repeater_field_default_item( $field, $index, $echo = false ) {
		ob_start();
		?>
		<div id="repeater-item-<?php echo esc_attr( $field['key'] . '-' . $index ); ?>" class="repeater-item position-relative <?php echo esc_attr( ! empty( $field['classes'] ) ? $field['classes'] : '' ); ?>" >
			<div class="position-absolute top-0 end-0 bg-black" style="border-radius:50%;padding:4px 5px;margin:5px;">
				<button type="button" class="btn-close btn btn-close-white" aria-label="Close" style="opacity:1;"></button>
			</div>
			<div class="container-fluid">
				<div class="row mt-2">
				<?php
				foreach ( $field['default_subitem'] as $subitem_key => $subitem_field ) {
					$subitem_field['key']            = $subitem_key;
					$subitem_field['repeater_index'] = $index;
					$subitem_field['name']           = $this->id . '[' . $field['key'] . '][' . $index . '][' . $subitem_key . ']';
					$subitem_field['filter']         = $field['key'] . '-' . $subitem_key;
					$subitem_settings_field          = $this->field->new_field( $this->id, $subitem_field );
					$subitem_settings_field->get_field();
				}
				?>
				</div>
			</div>
		</div>
		<?php
		$result = ob_get_clean();
		if ( $echo ) {
			echo $result;
		}
		return $result;
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
	public function get_repeater_field_html( $field_key, $repeater_item_key, $index, $field, $full_field = true, $echo = true, $ignore_hide = true ) {
		$field['repeater_index'] = $index;
		$field['name']           = $this->id . '[' . $field_key . '][' . $index . '][' . $repeater_item_key . ']';
		$field['filter']         = $field_key . '-' . $repeater_item_key;
		$settings_field          = $this->field->new_field( $this->id, $field );
		$settings_field->get_field( $full_field, $echo, $ignore_hide );
	}

	/**
	 * Get Settings Field.
	 *
	 * @param string $field_key
	 *
	 * @return array
	 */
	public function get_settings_field( $field_key, $get_settings_value = false ) {
		// Loop over settings sections.
		foreach ( $this->fields as $tab_key => $sections ) {
			foreach ( $sections as $section_name => $section_settings ) {
				if ( isset( $section_settings['settings_list'][ $field_key ] ) ) {
					if ( $get_settings_value ) {
						$section_settings['settings_list'][ $field_key ]['value'] = $this->settings[ $field_key ];
					}
					$section_settings['settings_list'][ $field_key ]['key']    = $field_key;
					$section_settings['settings_list'][ $field_key ]['filter'] = $field_key;
					return $section_settings['settings_list'][ $field_key ];
				}
			}
		}

		return false;
	}

	/**
	 * Sanitize Submitted Repeater Field.
	 *
	 * @param string $key
	 * @param array  $settings
	 * @return mixed
	 */
	public function sanitize_submitted_repeater_field( $key, $settings ) {
		if ( ! isset( $_POST[ $this->id ][ $key ] ) ) {
			return $settings[ $key ];
		}

		if ( empty( $_POST[ $this->id ][ $key ] ) ) {
			return array();
		}

		if ( ! is_array( $_POST[ $this->id ][ $key ] ) ) {
			return $settings[ $key ];
		}

		$settings[ $key ]      = array();
		$repeater_field        = $this->get_settings_field( $key );
		$default_field_subitem = $repeater_field['default_subitem'];

		foreach ( $_POST[ $this->id ][ $key ] as $item_index => $item_arr ) {
			$subitem = array();
			foreach ( $default_field_subitem as $subitem_key => $subitem_arr ) {
				$posted_key              = array( $key, $item_index, $subitem_key );
				$subitem[ $subitem_key ] = $this->sanitize_submitted_field( $posted_key, $subitem_key, $default_field_subitem[ $subitem_key ]['value'], $default_field_subitem[ $subitem_key ], true );
			}
			$settings[ $key ][] = $subitem;
		}

		return $settings[ $key ];
	}

	/**
	 * Refresh Settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function refresh_settings( $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Sanitize Submitted Settings Field.
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @return mixed
	 */
	public function sanitize_submitted_field( $posted_key, $settings_key, $old_value, $field = null ) {
		$value      = $this->resolve_posted_key( $posted_key ) ?? $old_value;
		$field      = is_null( $field ) ? $this->get_settings_field( $settings_key ) : $field;
		$field_type = ! empty( $field['type'] ) ? $field['type'] : 'text';
		switch ( $field_type ) {
			case 'text':
			case 'radio':
				$value = sanitize_text_field( $value );
				break;
			case 'select':
				$value = is_array( $value ) ? self::deep_sanitize_field( $value ) : sanitize_text_field( $value );
				break;
			case 'email':
				$value = sanitize_email( $value );
				break;
			case 'url':
				$value = esc_url_raw( $value );
				break;
			case 'textarea':
				$value = ( isset( $field['html_allowed'] ) ? wp_kses_post( $value ) : sanitize_textarea_field( $value ) );
				break;
			case 'checkbox':
				if ( ! is_null( $this->resolve_posted_key( $posted_key ) ) ) {
					$value = 'on';
				} else {
					$value = 'off';
				}
				break;
			default:
				$value = sanitize_text_field( $value );
				break;
		}

		return is_numeric( $value ) ? $value + 0 : $value;
	}

	/**
	 * Resolve Posted Key for submitted Fields.
	 *
	 * @param string|array $posted_key
	 * @return mixed
	 */
	private function resolve_posted_key( $posted_key ) {
		if ( is_string( $posted_key ) ) {
			return wp_unslash( $_POST[ $this->id ][ $posted_key ] ?? null );
		}

		$value = wp_unslash( $_POST[ $this->id ] );
		foreach ( $posted_key as $key ) {
			$value = $value[ $key ] ?? null;
			if ( is_null( $value ) ) {
				break;
			}
		}

		return $value;
	}

	/**
	 * Get Field Type using Field Key.
	 *
	 * @param string $key
	 * @return string
	 */
	protected function get_field_type( $key ) {
		return $this->fields[ $key ]['type'];
	}

}
