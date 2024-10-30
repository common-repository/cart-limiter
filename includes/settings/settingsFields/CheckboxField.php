<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields;

use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields\FieldBase;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checkbox Field.
 */
class CheckboxField extends FieldBase {


	/**
	 * Get Checkbox Field HTML.
	 *
	 * @param boolean $return;
	 *
	 * @return mixed
	 */
	public function get_field_html( $return = false ) {
		if ( $return ) {
			ob_start();
		}
		?>
		<input type="checkbox" <?php echo esc_attr( ! empty( $this->field['id'] ) ? 'id=' . $this->field['id'] : '' ); ?> <?php echo ( ! empty( $this->field['classes'] ) ? 'class="' . esc_attr( $this->field['classes'] ) . '"' : '' ); ?> <?php $this->field_name( $this->field ); ?> value="<?php echo esc_attr( ! empty( $this->field['value'] ) ? $this->field['value'] : '' ); ?>" <?php $this->custom_attributes_html( $this->field ); ?> <?php echo esc_attr( ! empty( $this->field['value'] ) && 'on' === $this->field['value'] ? 'checked=checked' : '' ); ?> >
        <?php
		if ( $return ) {
			return ob_get_clean();
		}
	}

}
