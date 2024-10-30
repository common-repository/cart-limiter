<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\utils;

/**
 * General Functions Utils Trait.
 */
trait GeneralUtils {

	/**
	 * Loader HTML Code.
	 *
	 * @return void
	 */
	public static function loader_html( $prefix = null ) {
		?>
		<div class="loader w-100 h-100 position-absolute start-0 top-0 d-none <?php echo esc_attr( ! empty( $prefix ) ? $prefix . '-loader' : '' ); ?>">
			<div class="text-white wrapper text-center position-absolute d-block w-100 ">
				<img src="<?php echo esc_url_raw( admin_url( 'images/spinner-2x.gif' ) ); ?>"  />
			</div>
			<div class="overlay position-absolute d-block w-100 h-100"></div>
		</div>
		<?php
	}

	/**
	 * Loader HTML Code.
	 *
	 * @return void
	 */
	public static function loader_icon( $icon_size = 'small', $additional_classes = '', $custom_style = '' ) {
		?>
		<img <?php echo ( ! empty( $custom_style ) ? 'style="' . esc_attr( $custom_style ) . '"' : '' ); ?> <?php echo ( ! empty( $additional_classes ) ? 'class="' . esc_attr( $additional_classes ) . '"' : '' ); ?>  src="<?php echo esc_url_raw( admin_url( 'images/spinner' . ( ( 'big' === $icon_size ) ? '-2x' : '' ) . '.gif' ) ); ?>"  />
		<?php
	}

	/**
	 * Check if current Page.
	 *
	 * @param string $page_slug Page Slug.
	 *
	 * @return boolean
	 */
	public function is_current_page( $page_slug = null ) {

		if ( wp_doing_ajax() && ! is_null( $this->action ) && ! empty( $_POST['pageAction'] ) && ( sanitize_text_field( wp_unslash( $_POST['pageAction'] ) ) === $this->action ) ) {
			return true;
		}

		if ( ! is_null( $page_slug ) ) {
			return is_page( $page_slug );
		}
		if ( property_exists( $this, 'page_slug' ) && ! is_null( $this->page_slug ) ) {
			return is_page( $this->page_slug );
		}
		return false;
	}

	/**
	 * Parse Arguments recursevly.
	 *
	 * @param array $args
	 * @param array $defaults
	 * @return array
	 */
	private static function rec_parse_args( $args, $defaults ) {
		$new_args = (array) $defaults;
		foreach ( $args as $key => $value ) {
			if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
				$new_args[ $key ] = self::rec_parse_args( $value, $new_args[ $key ] );
			} else {
				$new_args[ $key ] = $value;
			}
		}
		return $new_args;
	}

	/**
	 * Map sanitize function name to field type.
	 *
	 * @param string $field_type
	 * @return string
	 */
	protected static function sanitize_functions_mapping( $field_type ) {
		$fields_sanitize_mapping = array(
			'text'     => 'sanitize_text_field',
			'textarea' => 'sanitize_textarea',
			'url'      => 'esc_url_raw',
			'email'    => 'sanitize_email',
		);
		return ( ! empty( $fields_sanitize_mapping[ $field_type ] ) ? $fields_sanitize_mapping[ $field_type ] : 'sanitize_text_field' );
	}

	/**
	 * Apply function on array.
	 *
	 * @param array        $arr
	 * @param string|array $func_name
	 * @param string       $casting
	 * @return array
	 */
	protected static function mapping_func( $arr, $func_name, $casting = null ) {
		foreach ( $arr as $key => $value ) {
			$func_name = (array) $func_name;
			foreach ( $func_name as $func ) {
				$value = call_user_func( $func, $value );
				if ( ! is_null( $casting ) ) {
					settype( $value, $casting );
				}
			}
			$arr[ $key ] = $value;
		}
		return $arr;
	}

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return boolean
	 */
	public static function is_woocommerce_active() {
		require_once \ABSPATH . 'wp-admin/includes/plugin.php';
		return is_plugin_active( 'woocommerce/woocommerce.php' ) && class_exists( '\WooCommerce' );
	}

	/**
	 * Check if current user is admin.
	 *
	 * @return boolean
	 */
	protected static function is_admin_user() {
		return current_user_can( 'administrator' );
	}

	/**
	 * Deep Sanitize Field.
	 *
	 * @param array $field
	 * @return array
	 */
	protected static function deep_sanitize_field( $field ) {
		foreach ( $field as $key => $val ) {
			$key = sanitize_text_field( $key );
			if ( is_array( $val ) ) {
				$field[ $key ] = self::deep_sanitize_field( $field[ $key ] );
			} else {
				$field[ $key ] = sanitize_text_field( $val );
			}
		}
		return $field;
	}
}
