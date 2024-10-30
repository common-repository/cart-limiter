<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\settingsFields;

use GPLSCore\GPLS_PLUGIN_WWCLR\Base;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\GeneralUtils;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\notice\NoticeUtils;

/**
 * Settings Field.
 */
abstract class FieldBase extends Base {
    use GeneralUtils;
    use NoticeUtils;

    /**
     * Settings ID.
     *
     * @var string
     */
    protected $id;

	/**
	 * Field Data.
	 *
	 * @var array
	 */
	protected $field;

    /**
     * Field Identifier.
     *
     * @var string
     */
    protected $identifier;

	/**
	 * Field Constructor.
	 *
	 * @param array $this->field
	 */
	public function __construct( $id, $field ) {
        $this->id         = $id;
		$this->field      = $field;
        $this->identifier = wp_generate_uuid4();

        if ( method_exists( $this, 'hooks' ) ) {
            $this->hooks();
        }
	}

    /**
     * Get Field HTML.
     *
     * @return string
     */
    abstract public function get_field_html();

    /**
	 * Get Custom Attributes HTML.
	 *
	 * @return void
	 */
	protected function custom_attributes_html() {
		$attributes = '';
		if ( ! empty( $this->field['attrs'] ) && is_array( $this->field['attrs'] ) ) {
			foreach ( $this->field['attrs'] as $key => $value ) {
				$attributes .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
			}
		}
		if ( ! empty( $attributes ) ) {
			echo $attributes;
		}
	}

	/**
	 * Field Name
	 *
	 * @return void
	 */
	protected function field_name() {
		echo 'name="' . esc_attr( ! empty( $this->field['name'] ) ? $this->field['name'] : $this->id . '[' . $this->field['key'] . ']' . ( ! empty( $this->field['subkey'] ) ? '[' . $this->field['subkey'] . ']' : '' ) . ( ! empty( $this->field['multiple'] ) ? '[]' : '' ) ) . '"';
	}

    /**
	 * Settings Field HTML.
	 *
	 * @param array $this->field
	 * @return void|string
	 */
	public function add_field( $echo = true ) {
		ob_start();
		$is_inline = ( ! isset( $this->field['inline'] ) || true === $this->field['inline'] );
		?>
		<?php $this->settings_field_wrapper_start(); ?>

		<!-- Field Label -->
		<?php if ( ! empty( $this->field['input_label'] ) ) : ?>
			<div class="<?php echo esc_attr( $is_inline ? 'col-lg-3' : 'col-12' ); ?> d-flex flex-column <?php echo esc_attr( ! empty( $this->field['center_label'] ) ? 'align-items-center' : 'align-items-start' ); ?> mb-4 mb-md-0">
				<h6 class="<?php echo esc_attr( ! empty( $this->field['input_label_classes'] ) ? $this->field['input_label_classes'] : '' ); ?>"><?php printf( esc_html__( '%s', '%s' ), $this->field['input_label'], self::$plugin_info['name'] ); /* translators: %s Settings Field Label */ ?></h6>
				<!-- Field Label subheading -->
				<?php if ( ! empty( $this->field['input_label_subheading'] ) ) : ?>
				<p class="text-small text-muted d-block mt-2 <?php echo esc_attr( $this->field['input_label_subheading_classes'] ?? '' ); ?>"><?php echo esc_html( $this->field['input_label_subheading'] ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Field input -->
		<div class="<?php echo esc_attr( ! empty( $this->field['input_label'] ) ? ( $is_inline ? 'col-lg-9' : 'col-12' ) : 'col-12' ); ?> d-flex flex-column align-items-start">
			<div class="input w-100 <?php echo esc_attr( 'repeater' === $this->field['type'] ? $this->id . '-' . $this->field['key'] . '-repeater-container' : '' ); ?>">
				<!-- Input Heading -->
				<?php if ( ! empty( $this->field['input_heading'] ) ) : ?>
					<h6 class="text-muted"><?php printf( esc_html__( '%s', '%s' ), $this->field['input_heading'], self::$plugin_info['name'] ); /* translators: %s Settings Field Input Heading */ ?></h6>
				<?php endif; ?>

				<!-- Input  -->
				<?php $this->get_field_html(); ?>

				<!-- Input Suffix -->
				<?php if ( ! empty( $this->field['input_suffix'] ) ) : ?>
					<small class="text-muted"><?php echo esc_html( $this->field['input_suffix'] ); ?></small>
				<?php endif; ?>

				<!-- Input Footer -->
				<?php if ( ! empty( $this->field['input_footer'] ) ) : ?>
					<small class="small text-muted mt-1"><?php printf( esc_html__( '%s', '%s' ), $this->field['input_footer'], self::$plugin_info['name'] ); /* translators: %s Settings Field Input Footer */ ?></small>
				<?php endif; ?>
			</div>
		</div>
		<?php
		do_action( $this->id . '-after-settings-field-' . ( ! empty( $this->field['filter'] ) ? $this->field['filter'] : $this->field['key'] ), $this->field );

		$this->settings_field_wrapper_end();

		$result = ob_get_clean();
		if ( $echo ) {
			echo $result;
		}
		return $result;
	}

    /**
	 * Settings Field Row Wrapper Start.
	 *
	 * @return void
	 */
	protected function settings_field_wrapper_start() {
        do_action( $this->id . '-before-settings-field', $this->field );
       ?>
       <div class="settings-field-wrapper <?php echo esc_attr( ! empty( $this->field['wrapper_padding'] ) ? $this->field['wrapper_padding'] : 'py-4' ); ?> <?php echo esc_attr( ! empty( $this->field['wrapper_margin'] ) ? $this->field['wrapper_margin'] : 'my-4' ); ?> col-md-12 <?php echo esc_attr( ! empty( $this->field['wrapper_classes'] ) ? $this->field['wrapper_classes'] : '' ); ?>" <?php $this->custom_attributes_html( ! empty( $this->field['wrapper_attrs'] ) ? $this->field['wrapper_attrs'] : '' ); ?> >
           <div class="container-fluid <?php echo esc_attr( $this->field['container_classes'] ?? '' ); ?>">
               <div class="row">
                   <?php if ( ! empty( $this->field['field_heading'] ) ) : ?>
                   <div class="col-12">
                       <h4 class="field-heading <?php echo esc_attr( $this->field['field_heading_classes'] ?? '' ); ?>"><?php printf( esc_html__( '%s', '%s' ), $this->field['field_heading'], self::$plugin_info['name'] ); ?></h4>
                   </div>
                   <?php endif; ?>
               <?php
   }

	/**
	 * Settings Field Row Wrapper End.
	 *
	 * @return void
	 */
	protected function settings_field_wrapper_end() {
		?>
				</div>
			</div>
			<?php if ( ! isset( $this->field['show_divider'] ) || true === $this->field['show_divider'] ) : ?>
				<div class="astrodivider"><div class="astrodividermask"></div><span><i>&#10038;</i></span></div>
			<?php endif; ?>
		</div>
		<?php
		do_action( $this->id . '-after-settings-field', $this->field );
	}

	/**
	 * Get Field HTML.
	 *
	 * @param array   $this->fieldget_field
	 * @param boolean $full_field
	 * @return boolean
	 */
	public function get_field( $full_field = true, $echo = true, $ignore_hide = false ) {
		if ( $ignore_hide ) {
			unset( $this->field['hide'] );
		}

		if ( ! empty( $this->field['hide'] ) ) {
			return;
		}

		if ( ! empty( $this->field['custom'] ) ) {
			ob_start();
			do_action( $this->id . '-' . $this->field['filter'] . '-settings-field-custom', $this->field );
			$result = ob_get_clean();
			if ( $echo ) {
				echo $result;
			}
			return $result;
		}

		ob_start();

		if ( $full_field ) {
			$this->add_field();
		} else {
			$this->get_field_html( true );
		}

		$result = ob_get_clean();

		if ( $echo ) {
			if ( $full_field ) {
				echo $result;
			} else {
				echo $this->kses_field( $result, $this->field );
			}
		}

		return $result;
	}

    /**
	 * Kses Settings Fields.
	 *
	 * @param string $html
	 * @param array  $this->field
	 * @return string
	 */
	protected function kses_field( $html ) {
		$attrs = array();
		if ( ! empty( $this->field['attrs'] ) ) {
			$attrs = array_keys( $this->field['attrs'] );
		}

		if ( ! empty( $this->field['options'] ) ) {
			$attrs = array_unique(
				array_merge(
					...array_map(
						function( $field_attr ) {
							return array_keys( $field_attr );
						},
						array_values( array_column( $this->field['options'], 'attrs' ) )
					)
				)
			);
		}

		$default_attrs       = $this->default_attrs;
		$this->default_attrs = array_merge( $this->default_attrs, $attrs );
		add_filter( 'wp_kses_allowed_html', array( $this, 'allow_inputs_in_kses' ), 100, 2 );
		$html = wp_kses_post( $html );
		remove_filter( 'wp_kses_allowed_html', array( $this, 'allow_inputs_in_kses' ), 100 );
		$this->default_attrs = $default_attrs;
		return $html;
	}

	/**
	 * Allow input fields for Kses.
	 *
	 * @param array  $allowed_tags
	 * @param string $context
	 * @return array
	 */
	public function allow_inputs_in_kses( $allowed_tags, $context ) {
		foreach ( $this->default_fields as $this->field ) {
			$allowed_tags[ $this->field ] = $this->default_attrs;
			foreach ( $allowed_tags[ $this->field ] as $attr ) {
				$allowed_tags[ $this->field ][ $attr ] = array();
			}
		}
		return $allowed_tags;
	}
}
