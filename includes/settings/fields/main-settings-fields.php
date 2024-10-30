<?php
namespace GPLSCore\GPLS_PLUGIN_WWCLR\includes\settings\fields;

defined( 'ABSPATH' ) || exit;
/**
 * Setup Settings Fields.
 *
 * @return array
 */
function setup_settings_fields( $core, $plugin_info ) {
	return array(
		'totals'  => array(
			'general' => array(
				'settings_list' => array(
					'global_enable'           => array(
						'input_label'  => esc_html__( 'Enable', 'cart-limiter' ),
						'input_suffix' => esc_html__( 'Enable Cart Limiter', 'cart-limiter' ),
						'type'         => 'checkbox',
						'value'        => 'off',
					),
					'totals_min_cart_total'   => array(
						'input_label'  => esc_html__( 'Min Cart Total', 'cart-limiter' ) . $core->pro_btn( '', 'Premium', '', '', true ),
						'input_suffix' => esc_html__( 'Min allowed cart total', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Disable proceeding to checkout if the cart totals is less than this number. set it 0 to disable.', 'cart-limiter' ),
						'type'         => 'text',
						'subtype'      => 'number',
						'value'        => 0,
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one d-block mw-100',
						'attrs'        => array(
							'min'       => 0,
							'data-type' => 'totals_min_cart_total',
							'disabled'  => 'disabled',
						),
						'show_divider' => false,
					),
					'totals_min_cart_total_msg'   => array(
						'input_label'  => esc_html__( 'Min Cart Total Notice', 'cart-limiter' ) . $core->pro_btn( '', 'Premium', '', '', true ),
						'input_footer' => esc_html__( 'Min Cart notice message for the Cart total limitation', 'cart-limiter' ),
						'value'        => '',
						'type'         => 'text',
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one large-text',
						'attrs'        => array(
							'placeholder' => esc_html__( 'Example: Minimum allowed cart total is $50', 'cart-limiter' ),
							'data-type'   => 'totals_cart_total_msg',
							'disabled'    => 'disabled',
						),
					),
					'totals_cart_total'       => array(
						'input_label'  => esc_html__( 'Max Cart Total', 'cart-limiter' ),
						'input_suffix' => esc_html__( 'Max allowed cart total', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Disable adding to cart after reaching an amount of cart total. set it 0 to disable.', 'cart-limiter' ),
						'type'         => 'text',
						'subtype'      => 'number',
						'value'        => 0,
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one d-block mw-100',
						'attrs'        => array(
							'min'       => 0,
							'data-type' => 'totals_cart_total',
						),
						'show_divider' => false,
					),
					'totals_cart_total_msg'   => array(
						'input_label'  => esc_html__( 'Max Cart Total Notice', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Cart notice message for the Cart total limitation', 'cart-limiter' ),
						'value'        => '',
						'type'         => 'text',
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one large-text',
						'attrs'        => array(
							'placeholder' => esc_html__( 'Example: Maximum allowed cart total is $500', 'cart-limiter' ),
							'data-type'   => 'totals_cart_total_msg',
						),
					),
					'totals_products_num'     => array(
						'input_label'  => esc_html__( 'Max Products Number', 'cart-limiter' ),
						'input_suffix' => esc_html__( 'Max allowed number of products', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Disable adding to cart after reaching a number of products in cart. set it 0 to disable.', 'cart-limiter' ),
						'type'         => 'text',
						'subtype'      => 'number',
						'value'        => 0,
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one d-block mw-100',
						'attrs'        => array(
							'min'       => 0,
							'data-type' => 'totals_products_num',
						),
						'show_divider' => false,
					),
					'totals_products_num_msg' => array(
						'input_label'  => esc_html__( 'Products Number Notice', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Cart notice message for the maximum products number limitation', 'cart-limiter' ),
						'value'        => '',
						'type'         => 'text',
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one large-text',
						'attrs'        => array(
							'placeholder' => esc_html__( 'Example: Maximum allowed products number in cart is 7', 'cart-limiter' ),
							'data-type'   => 'totals_products_num_msg',
						),
					),
					'totals_qty_num'          => array(
						'input_label'  => esc_html__( 'Max Total Quantity', 'cart-limiter' ),
						'input_suffix' => esc_html__( 'Max allowed number of cart total quantity', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Disable adding to cart after reaching a number of quantity for the sum of all cart items. set it 0 to disable.', 'cart-limiter' ),
						'type'         => 'text',
						'subtype'      => 'number',
						'value'        => 0,
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one d-block mw-100',
						'attrs'        => array(
							'min'       => 0,
							'data-type' => 'totals_qty_num',
						),
						'show_divider' => false,

					),
					'totals_qty_num_msg'      => array(
						'input_label'  => esc_html__( 'Total Quantity Notice', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Cart notice message for the maximum total quantity in cart', 'cart-limiter' ),
						'value'        => '',
						'type'         => 'text',
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one large-text',
						'attrs'        => array(
							'placeholder' => esc_html__( 'Example: Maximum allowed total quantity in cart is 20', 'cart-limiter' ),
							'data-type'   => 'totals_qty_num_msg',
						),
					),
				),
			),
		),
		'qty'     => array(
			'global' => array(
				'settings_list' => array(
					'qty_min_qty'            => array(
						'input_label'  => esc_html__( 'Global Min Quantity', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Force Min quantity allowed on all products. set it 0 to disable.', 'cart-limiter' ),
						'type'         => 'text',
						'subtype'      => 'number',
						'value'        => 0,
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one d-block mw-100',
						'attrs'        => array(
							'min'       => 0,
							'data-type' => 'qty_min_qty',
						),
						'show_divider' => false,
					),
					'qty_min_qty_msg'        => array(
						'input_label'  => esc_html__( 'Min Quantity Notice', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Cart notice message for the min quantity limitation', 'cart-limiter' ),
						'value'        => '',
						'type'         => 'text',
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one large-text',
						'attrs'        => array(
							'placeholder' => esc_html__( 'Example: Minimum allowed items to purchase from any product is 5', 'cart-limiter' ),
							'data-type'   => 'qty_min_qty_msg',
						),
					),
					'qty_max_qty'            => array(
						'input_label'  => esc_html__( 'Global Max Quantity', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Force Max quantity allowed on all products. set it 0 to disable.', 'cart-limiter' ),
						'type'         => 'text',
						'subtype'      => 'number',
						'value'        => 0,
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one d-block mw-100',
						'attrs'        => array(
							'min'       => 0,
							'data-type' => 'qty_max_qty',
						),
						'show_divider' => false,
					),
					'qty_max_qty_msg'        => array(
						'input_label'  => esc_html__( 'Max Quantity Notice', 'cart-limiter' ),
						'input_footer' => esc_html__( 'Cart notice message for the max quantity limitation', 'cart-limiter' ),
						'value'        => '',
						'type'         => 'text',
						'classes'      => $plugin_info['classes_prefix'] . '-limit-type-one large-text',
						'attrs'        => array(
							'placeholder' => esc_html__( 'Example: You can buy up to 5 items from any product', 'cart-limiter' ),
							'data-type'   => 'qty_max_qty_msg',
						),
					),
					'qty_field_restrict'     => array(
						'input_label'  => esc_html__( 'Quantity field', 'cart-limiter' ),
						'input_suffix' => esc_html__( 'Add min and max quantity limits to products quantity input field', 'cart-limiter' ),
						'value'        => 'off',
						'type'         => 'checkbox',
						'attrs'        => array(
							'data-type' => 'qty_field_restrict',
						),
					),
					'qty_limit_conditions'   => array(
						'inline'                         => false,
						'type'                           => 'repeater',
						'classes'                        => 'border p-3 my-3 shadow-sm bg-light',
						'input_label'                    => esc_html__( 'Custom Quantity Limits', 'cart-limiter' ) . $core->pro_btn( '', 'Premium', '', '', true ),
						'input_label_classes'            => 'bg-success p-3 w-100 fs-3 text-white text-center',
						'input_label_subheading_classes' => 'w-100 text-center fs-6',
						'input_label_subheading'         => esc_html__( 'Add custom quantity limits to products in bulk.', 'cart-limiter' ),
						'repeat_add_label'               => esc_html__( 'Add Limit Rule', 'cart-limiter' ),
						'value'                          => array(),
						'default_subitem'                => array(
							'qty_product_select_type'   => array(
								'key'             => 'qty_product_select_type',
								'wrapper_classes' => 'col-lg-4 border shadow-sm p-2',
								'input_label'     => esc_html__( 'Select Type', 'cart-limiter' ),
								'type'            => 'radio',
								'value'           => 1,
								'options'         => array(
									array(
										'input_suffix' => esc_html__( 'Select Products Directly', 'cart-limiter' ),
										'value'        => 1,
										'default'      => true,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-products-content',
											'disabled'    => 'disabled',
										),
									),
									array(
										'input_suffix' => esc_html__( 'Select By Taxonomies', 'cart-limiter' ),
										'value'        => 2,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-tags-content',
											'disabled'    => 'disabled',
										),
									),
								),
								'show_divider'    => false,
								'attrs'           => array(
									'disabled'    => 'disabled',
								),
							),
							'qty_product_select_result' => array(
								'key'             => 'qty_product_select_result',
								'inline'          => false,
								'wrapper_classes' => 'col-lg-4 border shadow-sm p-2',
								'type'            => 'select',
								'classes'         => 'select2-input',
								'value'           => array(
									'match_type' => 'any',
									'taxs'       => array(),
									'products'   => array(),
								),
								'hide_label'      => true,
								'hide'            => true,
								'multiple'        => true,
								'show_divider'    => false,
								'attrs'           => array(
									'disabled'    => 'disabled',
								),
							),
							'min_qty'                   => array(
								'inline'          => false,
								'input_label'     => esc_html__( 'Min Quantity', 'cart-limiter' ),
								'input_footer'    => esc_html__( 'Minimum Quantity allowed to add the product to the cart', 'cart-limiter' ),
								'type'            => 'text',
								'wrapper_classes' => 'col-lg-4 border shadow-sm p-2',
								'subtype'         => 'number',
								'classes'         => 'limit-condition-field min_qty-condition-field d-block',
								'value'           => 0,
								'attrs'           => array(
									'min' => 0,
									'disabled'    => 'disabled',
								),
								'show_divider'    => false,
							),
							'max_qty'                   => array(
								'inline'          => false,
								'input_label'     => esc_html__( 'Max Quantity', 'cart-limiter' ),
								'input_footer'    => esc_html__( 'Maximum Quantity allowed to be added to the cart', 'cart-limiter' ),
								'classes'         => 'limit-condition-field max_qty-condition-field d-block',
								'type'            => 'text',
								'subtype'         => 'number',
								'wrapper_classes' => 'col-lg-4 border shadow-sm p-2',
								'value'           => 0,
								'attrs'           => array(
									'min' => 0,
									'disabled'    => 'disabled',
								),
								'show_divider'    => false,
							),
							'min_qty_msg'               => array(
								'wrapper_margin'    => 'my-1',
								'input_label'       => esc_html__( 'Min quantity notice', 'cart-limiter' ),
								'input_footer'      => esc_html__( 'Notice message for minimum quantity limitation.', 'cart-limiter' ),
								'attrs'             => array(
									'placeholder' => esc_html__( 'Example: Minimum items to purchase from Accessories Category is 5', 'cart-limiter' ),
									'disabled'    => 'disabled',
								),
								'classes'           => 'limit-condition-field min_qty_msg-condition-field d-block w-100',
								'type'              => 'text',
								'value'             => '',
								'hide'              => true,
								'show_divider'      => false,
								'wrapper_classes'   => 'col-lg-12 mt-5',
								'container_classes' => 'g-0',
							),
							'max_qty_msg'               => array(
								'wrapper_margin'    => 'my-1',
								'wrapper_classes'   => 'col-lg-12 mt-5',
								'input_label'       => esc_html__( 'Max quantity notice', 'cart-limiter' ),
								'input_footer'      => esc_html__( 'Notice message for maximum quantity limitation.', 'cart-limiter' ),
								'type'              => 'text',
								'classes'           => 'limit-condition-field max_qty_msg-condition-field d-block w-100',
								'value'             => '',
								'attrs'             => array(
									'placeholder' => esc_html__( 'Example: You can add up to 5 items from T-shirts products', 'cart-limiter' ),
									'disabled'    => 'disabled',
								),
								'hide'              => true,
								'show_divider'      => false,
								'container_classes' => 'g-0',
							),
						),
					),
					'qty_conditional_limits' => array(
						'inline'                         => false,
						'type'                           => 'repeater',
						'classes'                        => 'border p-3 my-3 shadow-sm bg-light',
						'input_label'                    => esc_html__( 'Products Conditional Quantity Limits', 'cart-limiter' ) . $core->pro_btn( '', 'Premium', '', '', true ),
						'input_label_classes'            => 'bg-success p-3 w-100 fs-3 text-white text-center',
						'input_label_subheading'         => esc_html__( 'add quantity limits to products based on the quantity of other products', 'cart-limiter' ),
						'input_label_subheading_classes' => 'w-100 text-center fs-6',
						'repeat_add_label'               => esc_html__( 'Add Limit Rule', 'cart-limiter' ),
						'value'                          => array(),
						'default_subitem'                => array(
							'product_start_select_type'   => array(
								'key'                   => 'product_start_select_type',
								'field_heading'         => esc_html__( 'Set Min or Max Quantity limit for selected products 🡺', 'cart-limiter' ),
								'field_heading_classes' => 'p-3 bg-white mb-5',
								'wrapper_classes'       => 'col-lg-6 border shadow-sm p-2',
								'input_label_classes'   => 'w-100 text-center',
								'input_label'           => esc_html__( 'Select Type', 'cart-limiter' ),
								'type'                  => 'radio',
								'value'                 => 1,
								'options'               => array(
									array(
										'input_suffix' => esc_html__( 'Select Products Directly', 'cart-limiter' ),
										'value'        => 1,
										'default'      => true,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-products-content',
											'disabled'    => 'disabled',
										),
									),
									array(
										'input_suffix' => esc_html__( 'Select By Taxonomies', 'cart-limiter' ),
										'value'        => 2,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-tags-content',
											'disabled'    => 'disabled',
										),
									),
								),
								'attrs' => array(
									'disabled'    => 'disabled',
								),
								'show_divider'          => false,
							),
							'product_start_select_result' => array(
								'key'             => 'product_start_select_result',
								'inline'          => false,
								'wrapper_classes' => 'col-lg-12 border shadow-sm p-2',
								'type'            => 'select',
								'classes'         => 'select2-input',
								'value'           => array(
									'match_type' => 'any',
									'taxs'       => array(),
									'products'   => array(),
								),
								'hide_label'      => true,
								'hide'            => true,
								'multiple'        => true,
								'attrs'           => array(
									'disabled'    => 'disabled',
								),
							),
							'start_limit_type'            => array(
								'key'             => 'start_limit_type',
								'inline'          => false,
								'hide'            => true,
								'input_label'     => esc_html__( 'Limit Type', 'cart-limiter' ),
								'input_footer'    => esc_html__( 'Set limit Type [ Min - Max ]', 'cart-limiter' ),
								'type'            => 'select',
								'options'         => array(
									'min' => esc_html__( 'Minimum', 'cart-limiter' ),
									'max' => esc_html__( 'Maximum', 'cart-limiter' ),
								),
								'value'           => 'min',
								'show_divider'    => false,
								'wrapper_classes' => 'col-lg-6 border shadow-sm p-2',
								'attrs'           => array(
									'disabled'    => 'disabled',
								),
							),
							'start_limit_qty'             => array(
								'key'             => 'start_limit_qty',
								'inline'          => false,
								'hide'            => true,
								'input_label'     => esc_html__( 'Limit Quantity', 'cart-limiter' ),
								'input_footer'    => esc_html__( 'Limit Quantity allowed to be added to the cart', 'cart-limiter' ),
								'classes'         => 'limit-condition-field max_qty-condition-field d-block',
								'type'            => 'text',
								'subtype'         => 'number',
								'wrapper_classes' => 'col-lg-6 border shadow-sm p-2',
								'value'           => 0,
								'attrs'           => array(
									'min' => 0,
									'disabled'    => 'disabled',
								),
								'show_divider'    => false,
							),
							'product_end_select_type'     => array(
								'key'                   => 'product_end_select_type',
								'field_heading'         => esc_html__( 'based on Min or Max of these products in cart', 'cart-limiter' ),
								'field_heading_classes' => 'p-3 bg-white mb-5',
								'wrapper_classes'       => 'col-lg-6 border shadow-sm p-2',
								'input_label'           => esc_html__( 'Select Type', 'cart-limiter' ),
								'input_label_classes'   => 'w-100 text-center',
								'type'                  => 'radio',
								'value'                 => 1,
								'options'               => array(
									array(
										'input_suffix' => esc_html__( 'Select Products Directly', 'cart-limiter' ),
										'value'        => 1,
										'default'      => true,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-products-content',
											'disabled'    => 'disabled',
										),
									),
									array(
										'input_suffix' => esc_html__( 'Select By Taxonomies', 'cart-limiter' ),
										'value'        => 2,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-tags-content',
											'disabled'    => 'disabled',
										),
									),
								),
								'show_divider'          => false,
								'attrs'                 => array(
									'disabled'    => 'disabled',
								)
							),
							'product_end_select_result'   => array(
								'key'             => 'product_select_result',
								'inline'          => false,
								'wrapper_classes' => 'col-lg-12 border shadow-sm p-2',
								'type'            => 'select',
								'classes'         => 'select2-input',
								'value'           => array(
									'match_type' => 'any',
									'taxs'       => array(),
									'products'   => array(
										array(
											'match_type' => 'any',
											'results'    => array(),
										),
									),
								),
								'hide_label'      => true,
								'hide'            => true,
								'multiple'        => true,
								'show_divider'    => false,
								'attrs'           => array(
									'disabled'    => 'disabled',
								),
							),
							'end_limit_type'              => array(
								'key'             => 'end_limit_type',
								'inline'          => false,
								'hide'            => true,
								'input_label'     => esc_html__( 'Limit Type', 'cart-limiter' ),
								'input_footer'    => esc_html__( 'Set limit Type [ at least - at most ]', 'cart-limiter' ),
								'type'            => 'select',
								'options'         => array(
									'min' => esc_html__( 'At least', 'cart-limiter' ),
									'max' => esc_html__( 'At most', 'cart-limiter' ),
								),
								'value'           => 'min',
								'show_divider'    => false,
								'wrapper_classes' => 'col-lg-6 border shadow-sm p-2',
								'attrs'           => array(
									'disabled'    => 'disabled',
								),
							),
							'end_limit_qty'               => array(
								'key'             => 'end_limit_qty',
								'inline'          => false,
								'hide'            => true,
								'wrapper_classes' => 'col-lg-6',
								'input_label'     => esc_html__( 'Limit Quantity', 'cart-limiter' ),
								'input_footer'    => esc_html__( 'Limit Quantity exist in the cart', 'cart-limiter' ),
								'classes'         => 'limit-condition-field max_qty-condition-field d-block',
								'type'            => 'text',
								'subtype'         => 'number',
								'wrapper_classes' => 'col-lg-6 border shadow-sm p-2',
								'value'           => 0,
								'attrs'           => array(
									'min' => 0,
									'disabled'    => 'disabled',
								),
								'show_divider'    => false,
							),
							'msg'                         => array(
								'key'             => 'min_msg',
								'wrapper_classes' => 'col-lg-12 border shadow-sm p-2',
								'input_label'     => esc_html__( 'Cart limitation notice', 'cart-limiter' ),
								'input_suffix'    => esc_html__( 'Cart notice message for the limitation', 'cart-limiter' ),
								'classes'         => 'large-text',
								'type'            => 'text',
								'value'           => '',
								'show_divider'    => false,
								'attrs'           => array(
									'placeholder' => esc_html__( 'Example: You can\'t add more than 5 items from accessories products if you have more than 3 items from hoodies products in cart', 'cart-limiter' ),
									'disabled'    => 'disabled',
								),
							),
						),
					),
				),
			),
		),
		'product' => array(
			'limit_type' => array(
				'settings_list' => array(
					'product_limit_conditions' => array(
						'inline'                         => false,
						'input_label'                    => esc_html__( 'Products Add To Cart Limitations', 'cart-limiter' ) . $core->pro_btn( '', 'Premium', '', '', true ),
						'input_label_classes'            => 'bg-success p-3 w-100 fs-3 text-white text-center',
						'input_label_subheading'         => esc_html__( 'Disable adding to cart selected products from one side if selected products from the other side in the cart.', 'cart-limiter' ),
						'input_label_subheading_classes' => 'w-100 text-center fs-6',
						'repeat_add_label'               => esc_html__( 'Add Limit Rule', 'cart-limiter' ),
						'type'                           => 'repeater',
						'value'                          => array(),
						'classes'                        => 'border p-3 my-3 shadow-sm bg-light',
						'default_subitem'                => array(
							'product_start_select_type'   => array(
								'key'                   => 'product_select_type',
								'field_heading_classes' => 'p-3 bg-white mb-5',
								'wrapper_classes'       => 'col-lg-6 border shadow-sm p-2',
								'input_label_classes'   => 'w-100 text-center',
								'input_label'           => esc_html__( 'Select Type', 'cart-limiter' ),
								'type'                  => 'radio',
								'value'                 => 1,
								'options'               => array(
									array(
										'input_suffix' => esc_html__( 'Select Products Directly', 'cart-limiter' ),
										'value'        => 1,
										'default'      => true,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-products-content',
											'disabled'    => 'disabled',
										),
									),
									array(
										'input_suffix' => esc_html__( 'Select By Taxonomies', 'cart-limiter' ),
										'value'        => 2,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-tags-content',
											'disabled'    => 'disabled',
										),
									),
								),
								'show_divider'          => false,
								'attrs'           => array(
									'disabled'    => 'disabled',
								),
							),
							'product_start_select_result' => array(
								'key'             => 'product_start_select_result',
								'inline'          => false,
								'wrapper_classes' => 'col-lg-12 border shadow-sm p-2',
								'type'            => 'select',
								'classes'         => 'select2-input',
								'value'           => array(
									'match_type' => 'any',
									'taxs'       => array(),
									'products'   => array(),
								),
								'hide_label'      => true,
								'hide'            => true,
								'multiple'        => true,
								'show_divider'    => false,
								'attrs'           => array(
									'disabled'    => 'disabled',
								),
							),
							'product_end_select_type'     => array(
								'key'                   => 'product_end_select_type',
								'field_heading_classes' => 'p-3 bg-white mb-5',
								'wrapper_classes'       => 'col-lg-6 border shadow-sm p-2',
								'input_label'           => esc_html__( 'Select Type', 'cart-limiter' ),
								'input_label_classes'   => 'w-100 text-center',
								'type'                  => 'radio',
								'value'                 => 1,
								'options'               => array(
									array(
										'input_suffix' => esc_html__( 'Select Products Directly', 'cart-limiter' ),
										'value'        => 1,
										'default'      => true,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-products-content',
										),
									),
									array(
										'input_suffix' => esc_html__( 'Select By Taxonomies', 'cart-limiter' ),
										'value'        => 2,
										'classes'      => $plugin_info['classes_prefix'] . '-limit-type select-product-type-radio',
										'attrs'        => array(
											'data-target' => 'select-by-tags-content',
										),
									),
								),
								'show_divider'          => false,
								'attrs'        => array(
									'disabled'    => 'disabled',
								),
							),
							'product_end_select_result'   => array(
								'key'             => 'product_select_result',
								'inline'          => false,
								'wrapper_classes' => 'col-lg-12 border shadow-sm p-2',
								'type'            => 'select',
								'classes'         => 'select2-input',
								'value'           => array(
									'match_type' => 'any',
									'taxs'       => array(),
									'products'   => array(
										array(
											'match_type' => 'any',
											'results'    => array(),
										),
									),
								),
								'hide_label'      => true,
								'hide'            => true,
								'multiple'        => true,
								'show_divider'    => false,
								'attrs' => array(
									'disabled'    => 'disabled',
								),
							),
							'msg'                         => array(
								'key'             => 'msg',
								'wrapper_classes' => 'col-lg-12 border shadow-sm p-2',
								'input_label'     => esc_html__( 'Cart limitation notice', 'cart-limiter' ),
								'input_suffix'    => esc_html__( 'Cart notice message for the limitation', 'cart-limiter' ),
								'classes'         => 'large-text',
								'type'            => 'text',
								'value'           => '',
								'show_divider'    => false,
								'attrs'           => array(
									'placeholder' => esc_html__( 'Example: You can\'t buy music products with t-shirts products.', 'cart-limiter' ),
									'disabled'    => 'disabled',
								),
							),
						),
					),
				),
			),
		),
	);

}
