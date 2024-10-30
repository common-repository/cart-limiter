<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings;

defined( 'ABSPATH' ) || exit();

use GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\Settings;
use GPLSCore\GPLS_PLUGIN_WWCLR\utils\notice\NoticeUtils;
use function GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\fields\setup_settings_fields;

/**
 * Main Settings.
 */
class MainSettings extends Settings {

	use NoticeUtils;

	/**
	 * Singular Instance.
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Settings Inline CSS.
	 *
	 * @return void
	 */
	protected function inline_css() {
		?>
		<style>
			.<?php echo esc_attr( $this->id . '-settings-wrapper' ); ?> .large-text::-webkit-input-placeholder {
				color: #cdcdcd;
			}
		</style>
		<?php
	}

	/**
	 * Prepare Fields
	 *
	 * @return void
	 */
	protected function prepare() {
		$this->is_woocommerce = true;
		$this->id             = self::$plugin_info['name'] . '-main-settings';
		$this->autoload       = true;
		$this->fields         = setup_settings_fields( self::$core, self::$plugin_info );
		$this->tab_key        = 'action';
	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	protected function hooks() {
		// Quantity Tab.
		add_action( $this->id . '-after-settings-field-qty_limit_conditions', array( $this, 'custom_qty_limit_default_field' ), 100, 1 );
		add_action( $this->id . '-after-settings-field-qty_conditional_limits', array( $this, 'custom_qty_limit_default_field' ), 100, 1 );
		add_action( $this->id . '-after-settings-field-product_limit_conditions', array( $this, 'custom_qty_limit_default_field' ), 100, 1 );

		add_action( $this->id . '-settings-tabs-action', array( $this, 'tabs_footer' ) );
	}

	public function custom_qty_limit_default_field( $repeater_field ) {
		$this->settings_fields->get_repeater_field_default_item( $repeater_field, null, true );
	}

	/**
	 * Custom Quantity Limits - Products Select Field.
	 *
	 * @param array $select_type_field
	 * @return void
	 */
	public function custom_qty_limits_select_field_html( $select_type_field ) {
		$this->products_select2_field( $select_type_field, 'qty_limit_conditions', 'qty_product_select_result' );
	}

	/**
	 * Product Min Qty Notice Field.
	 *
	 * @param array $min_qty_field
	 * @return void
	 */
	public function product_min_quantity_notice_field_html( $min_qty_field ) {
		$custom_attrs = array(
			'inline' => false,
		);
		$this->get_repeater_item_field_html( 'qty_limit_conditions', 'min_qty_msg', $min_qty_field['repeater_index'] ?? null, $custom_attrs );
	}

	/**
	 * Product Max Qty Notice Field.
	 *
	 * @param array $max_qty_field
	 * @return void
	 */
	public function product_max_quantity_notice_field_html( $max_qty_field ) {
		$custom_attrs = array(
			'inline' => false,
		);
		$this->get_repeater_item_field_html( 'qty_limit_conditions', 'max_qty_msg', $max_qty_field['repeater_index'] ?? null, $custom_attrs );
	}

	/**
	 * Custom Conditional Quantity Limits - Start Products Select Field.
	 *
	 * @param array $select_type_field
	 * @return void
	 */
	public function custom_conditional_qty_limits_start_fields_html( $select_type_field ) {
		$custom_attrs = array(
			'inline' => false,
		);
		$this->products_select2_field( $select_type_field, 'qty_conditional_limits', 'product_start_select_result' );
		$this->get_repeater_item_field_html( 'qty_conditional_limits', 'start_limit_type', $select_type_field['repeater_index'] ?? null, $custom_attrs );
		$this->get_repeater_item_field_html( 'qty_conditional_limits', 'start_limit_qty', $select_type_field['repeater_index'] ?? null, $custom_attrs );
	}

	/**
	 * Custom Conditional Quantity Limits - End Products Select Field.
	 *
	 * @param array $select_type_field
	 * @return void
	 */
	public function custom_conditional_qty_limits_end_fields_html( $select_type_field ) {
		$custom_attrs = array(
			'inline' => false,
		);
		$this->products_select2_field( $select_type_field, 'qty_conditional_limits', 'product_end_select_result', true );
		$this->get_repeater_item_field_html( 'qty_conditional_limits', 'end_limit_type', $select_type_field['repeater_index'] ?? null, $custom_attrs );
		$this->get_repeater_item_field_html( 'qty_conditional_limits', 'end_limit_qty', $select_type_field['repeater_index'] ?? null, $custom_attrs );
	}

	/**
	 * Custom Products Limits - Start Products Fields.
	 *
	 * @param array $select_type_field
	 * @return void
	 */
	public function custom_product_limits_start_fields_html( $select_type_field ) {
		$this->products_select2_field( $select_type_field, 'product_limit_conditions', 'product_start_select_result' );
	}

	/**
	 * Custom Products Limits - End Products Fields.
	 *
	 * @param array $select_type_field
	 * @return void
	 */
	public function custom_product_limits_end_fields_html( $select_type_field ) {
		$this->products_select2_field( $select_type_field, 'product_limit_conditions', 'product_end_select_result', true );
	}

	/**
	 * Products Select2 Field HTML.
	 *
	 * @param array $field
	 * @param array $attr
	 * @return void
	 */
	public function products_select2_field( $field, $main_key, $sub_key, $products_match_type = false ) {
		// Products Select Field.
		?>
		<div class="select-results-wrapper py-1 my-1 col-md-12 <?php echo esc_attr( 2 === $field['value'] ? 'hidden' : '' ); ?>" data-target="1">
			<?php $this->product_selects_field_html( $field, 'products', $main_key, $sub_key, '', '', $products_match_type ); ?>
		</div>

		<div class="select-results-wrapper py-1 my-1 col-md-12 <?php echo esc_attr( empty( $field['value'] ) || 1 === $field['value'] ? 'hidden' : '' ); ?>" data-target="2">
			<div class="main-taxs-list">
			<?php
				// Main Taxs [ Categories and Tags ] first.
				foreach ( $this->get_main_taxs_list() as $tax_name => $tax_label ) {
					$this->product_selects_field_html( $field, 'taxs', $main_key, $sub_key, $tax_name, $tax_label, true );
				}
			?>
			</div>
			<h6 class="border p-4 bg-white d-flex align-items-center justify-content-between btn attrs-taxs-list-toggler">
				<span><?php esc_html_e( 'Attributes', 'cart-limiter' ); ?></span>
				<span class="dashicons dashicons-arrow-down accordion-toggle accordion-toggler ms-3"></span>
			</h6>
			<div class="attrs-taxs-list collapse bg-white">
			<?php
			// Taxonomies Select Field.
			$woo_products_taxs = $this->get_full_taxs_list();
			foreach ( $woo_products_taxs as $tax_name => $tax_label ) {
				$this->product_selects_field_html( $field, 'taxs', $main_key, $sub_key, $tax_name, $tax_label, true );
			}

			$product_select_field = $this->get_repeater_item( $main_key, $sub_key, $field['repeater_index'] ?? null );
			?>
			</div>
			<div class="taxs-match-type w-100">
				<div class="match-type-container my-3 bg-white p-3">
					<span class="d-block"><?php esc_html_e( ' Taxonomies Match Type', 'cart-limiter' ); ?></span>
					<select name="<?php echo esc_attr( $this->id . '[' . $main_key . '][' . ( $field['repeater_index'] ?? 0 ) . '][' . $sub_key . ']' . '[match_type]' ); ?>" >
						<option <?php selected( 'any', $product_select_field['value']['match_type'] ?? 'any' ); ?> value="any"><?php echo esc_html_e( 'Any', 'cart-limiter' ); ?></option>
						<option <?php selected( 'all', $product_select_field['value']['match_type'] ?? 'any' ); ?> value="all"><?php echo esc_html_e( 'All', 'cart-limiter' ); ?></option>
					</select>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Products Start Condition Field HTML.
	 *
	 * @param array $select_type_field
	 * @param string $tax_name
	 * @param string $tax_label
	 * @return void
	 */
	public function product_selects_field_html( $select_type_field, $mark_key, $main_key, $sub_key, $tax_name = '', $tax_label = '', $include_match_type = false ) {
		$product_select_field = $this->get_repeater_item( $main_key, $sub_key, $select_type_field['repeater_index'] ?? null );
		?>
		<div class="settings-field-wrapper col-md-12 col-lg-12 p-2">
			<div class="wrapper p-3">
				<?php if ( ! empty( $tax_label ) ) : ?>
				<span><?php esc_html_e( 'Name: ', 'cart-limiter' ); ?></span><span class="ms-1 font-w-bold"><?php printf( esc_html__( '%s', 'woocommerce' ), $tax_label ); ?></span>
				<?php endif; ?>
				<!-- Limit Conditions Conf -->
				<div class="product-select-container container-fluid">
					<div class="row">
						<div class="col-md-8">
							<select data-tax="<?php echo esc_attr( $tax_name ); ?>" data-type="<?php echo esc_attr( $select_type_field['value'] ); ?>" class="select2-input-<?php echo esc_attr( 'products' === $mark_key ? 'wooproducts' : 'wootaxs' ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( $this->id . '-settings-nonce' ) ); ?>" name="<?php echo esc_attr( $this->id . '[' . $main_key . '][' . ( $select_type_field['repeater_index'] ?? 0 ) . '][' . $sub_key . '][' . $mark_key . ']' . ( ! empty( $tax_name ) ? '[' . $tax_name . ']' : '' ) . '[results][]' ); ?>" multiple>
								<?php
								if ( ! empty( $product_select_field['value'][ $mark_key ] ) ) {
									$this->prepare_product_select_options( $mark_key, $product_select_field, $tax_name );
								}
								?>
							</select>
						</div>
						<?php if ( $include_match_type ) : ?>
						<div class="col-md-4 d-flex align-items-center">
							<div class="match-type-container d-flex align-items-center">
								<span class="me-1 mb-1"><?php esc_html_e( 'Match Type', 'cart-limiter' ); ?></span>
								<select name="<?php echo esc_attr( $this->id . '[' . $main_key . '][' . ( $select_type_field['repeater_index'] ?? 0 ) . '][' . $sub_key . '][' . $mark_key . ']' . ( ! empty( $tax_name ) ? '[' . $tax_name . ']' : '' ) . '[match_type]' ); ?>" >
								<?php if ( 'products' ===  $mark_key ) : ?>
									<option <?php selected( 'any', $product_select_field['value']['products']['match_type'] ?? 'any' ); ?> value="any"><?php echo esc_html_e( 'Any', 'cart-limiter' ); ?></option>
									<option <?php selected( 'all', $product_select_field['value']['products']['match_type'] ?? 'any' ); ?> value="all"><?php echo esc_html_e( 'All', 'cart-limiter' ); ?></option>
								<?php elseif ( 'taxs' === $mark_key ) : ?>
									<option <?php selected( 'any', $product_select_field['value']['taxs'][ $tax_name ]['match_type'] ?? 'any' ); ?> value="any"><?php echo esc_html_e( 'Any', 'cart-limiter' ); ?></option>
									<option <?php selected( 'all', $product_select_field['value']['taxs'][ $tax_name ]['match_type'] ?? 'any' ); ?> value="all"><?php echo esc_html_e( 'All', 'cart-limiter' ); ?></option>
								<?php endif; ?>
								</select>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}


	/**
	 * Prepare Product Select Options.
	 *
	 * @param string $select_type
	 * @param array  $field
	 * @return array
	 */
	public function prepare_product_select_options( $select_type, $field, $tax_name = '' ) {
		$result = array();

		if ( 'products' === $select_type ) {
			$ids = array_map( 'absint', $field['value']['products']['results'] ?? array() );

		} elseif ( 'taxs' === $select_type ) {
			$ids = array_map( 'absint', $field['value']['taxs'][ $tax_name ]['results'] ?? array() );
		}

		if ( empty( $ids ) ) {
			return;
		}

		if ( 'products' === $select_type ) {
			$products         = array();
			$products_objects = array();
			// To Get variations!, improve later maybe.
			foreach ( $ids as $id ) {
				$products_objects[] = wc_get_product( $id );
			}
			foreach ( $products_objects as $product_object ) {
				$formatted_name = is_a( $product_object, '\WC_Product_variation' ) ? ( '#' . $product_object->get_id() . ' [' . $product_object->get_name() . '] ' . ( $product_object->get_sku() ? ' (' . $product_object->get_sku() . ')' : '' ) ) : $product_object->get_formatted_name();
				$products[]     = array(
					'id'    => $product_object->get_id(),
					'title' => rawurldecode( $formatted_name ),
					'url'   => get_permalink( $product_object->get_id() ),
				);
			}

			$result = $products;

		} elseif ( 'taxs' === $select_type ) {
			$terms       = get_terms(
				array(
					'taxonomy'   => $tax_name,
					'include'    => $ids,
					'fields'     => 'id=>name',
					'hide_empty' => false,
				)
			);
			foreach ( $terms as $term_id => $term_name ) {
				$result[] = array(
					'id'    => $term_id,
					'title' => $term_name,
				);
			}
		}

		foreach ( $result as $row ) {
			?>
			<option selected value="<?php echo esc_attr( $row['id'] ); ?>"><?php echo esc_html( $row['title'] ); ?></option>
			<?php
		}
	}

	/**
	 * Tabs Footer
	 *
	 * @return void
	 */
	public function tabs_footer() {
		self::$core->review_notice();
		self::$core->default_footer_section();
	}

}
