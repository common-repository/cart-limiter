<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields;

use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields\FieldBase;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Textarea Field.
 */
class TextareaField extends FieldBase {


	/**
	 * Get Textarea Field HTML.
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
		<textarea <?php echo esc_attr( ! empty( $this->field['id'] ) ? 'id=' . $this->field['id'] : '' ); ?> class="large-text <?php echo esc_attr( ! empty( $this->field['classes'] ) ? $this->field['classes'] : '' ); ?>" <?php $this->field_name( $this->field ); ?> <?php $this->custom_attributes_html( $this->field ); ?> ><?php echo ( ( ! empty( $this->field['html_allowed'] ) && $this->field['html_allowed'] ) ? wp_kses_post( $this->field['value'] ) : esc_html( $this->field['value'] ) ); ?></textarea>
		<?php
		if ( $return ) {
			return ob_get_clean();
		}
	}

}
