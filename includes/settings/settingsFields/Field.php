<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields;

use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields\{TextareaField, TextField, RepeaterField, SelectField, RadioField, CheckboxField };

/**
 * Field Template Class.
 *
 */
class Field {

	/**
	 * new Field.
	 *
	 * @param string $id
	 * @param array  $field
	 * @return FieldBase|null
	 */
	public function new_field( $id, $field ) {
		switch ( $field['type'] ) {
			case 'repeater':
				return new RepeaterField( $id, $field );
				break;
			case 'text':
			case 'url':
			case 'email':
				return new TextField( $id, $field );
				break;
			case 'textarea':
				return new TextareaField( $id, $field );
				break;
			case 'select':
				return new SelectField( $id, $field );
				break;
			case 'checkbox':
				return new CheckboxField( $id, $field );
				break;
			case 'radio':
				return new RadioField( $id, $field );
				break;
		}

		return null;
	}
}
