<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields;

use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields\FieldBase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Repeater Field.
 */
class RepeaterField extends FieldBase {

	/**
	 * Repeater Hooks.
	 *
	 * @return void
	 */
	protected function hooks() {
        add_action( $this->id . '-after-settings-field-' . ( ! empty( $this->field['filter'] ) ? $this->field['filter'] : $this->field['key'] ), array( $this, 'new_repeater_row_btn' ) );
	}

    /**
     * New Repeater Row Btn.
     *
     * @return void
     */
    public function new_repeater_row_btn() {
        $this->loader_icon( 'big', 'add-repeater-field-item-loader hidden mx-auto', 'width:60px;height:35px;' );
        ?>
        <!-- Repeater Add Group Rule Button -->
        <button data-key="<?php echo esc_attr( $this->field['key'] ); ?>" disabled="disabled" data-action="<?php echo esc_attr( $this->id . '-' . '-get-repeater-item' ); ?>" data-target="<?php echo esc_attr( $this->id . '-' . $this->field['key'] . '-repeater-container' ); ?>" data-count="<?php echo esc_attr( count( $this->field['value'] ) ); ?>" class="disabled my-4 btn btn-primary <?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-add-rule-group' ); ?>"><?php printf( esc_html__( '%s', '%s' ), ! empty( $this->field['repeat_add_label'] ) ? $this->field['repeat_add_label'] : 'Add rule group', self::$plugin_info['name'] ); ?></button>
        <?php
    }

	/**
	 * Get Repeater Field HTML.
	 *
	 * @param boolean $return;
	 *
	 * @return mixed
	 */
	public function get_field_html( $return = false ) {
        $settings_field = new Field();
		if ( $return ) {
			ob_start();
		}
		foreach ( $this->field['value'] as $index => $subitem_row ) {
			?>
			<input type="hidden" name="<?php echo esc_attr( $this->id . '[' . $this->field['key'] . ']' ); ?>" value="" >
			<div id="repeater-item-<?php echo esc_attr( $this->field['key'] . '-' . $index ); ?>" class="repeater-item position-relative <?php echo esc_attr( ! empty( $this->field['classes'] ) ? $this->field['classes'] : '' ); ?>">
				<div class="position-absolute top-0 end-0 bg-black" style="border-radius:50%;padding:4px 5px;margin:5px;">
					<button type="button" class="btn-close btn btn-close-white" aria-label="Close" style="opacity:1;"></button>
				</div>
				<div class="container-fluid">
					<div class="row mt-2">
					<?php
					foreach ( $subitem_row as $subitem_key => $subitem_value ) :
						$subitem_field                   = $this->field['default_subitem'][ $subitem_key ];
						$subitem_field['repeater_index'] = $index;
						$subitem_field['name']           = $this->id . '[' . $this->field['key'] . '][' . $index . '][' . $subitem_key . ']';
						$subitem_field['filter']         = $this->field['key'] . '-' . $subitem_key;
						$subitem_field['value']          = $subitem_value;
						$field                           = $settings_field->new_field( $this->id, $subitem_field );
						$field->get_field();
					endforeach
					?>
					</div>
				</div>
			</div>
			<?php
		}
		if ( $return ) {
			return ob_get_clean();
		}
	}

}
