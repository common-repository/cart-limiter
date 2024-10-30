<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields;

use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields\FieldBase;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Select Field.
 */
class SelectField extends FieldBase {


	/**
	 * Get Select Field HTML.
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
		<select <?php echo esc_attr( ! empty( $this->field['id'] ) ? 'id=' . $this->field['id'] : '' ); ?> <?php echo ( ! empty( $this->field['classes'] ) ? 'class="' . esc_attr( $this->field['classes'] ) . '"' : '' ); ?> <?php $this->field_name( $this->field ); ?> <?php $this->custom_attributes_html( $this->field ); ?> <?php echo esc_attr( ! empty( $this->field['multiple'] ) ? 'multiple' : '' ); ?> >
		<?php
		if ( ! empty( $this->field['options'] ) ) :
			foreach ( $this->field['options'] as $value => $label ) :
				?>
			<option <?php selected( $value, $this->field['value'] ); ?> value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
				<?php
			endforeach;
		endif;
		?>
		</select>
        <?php
		if ( $return ) {
			return ob_get_clean();
		}
	}

}
