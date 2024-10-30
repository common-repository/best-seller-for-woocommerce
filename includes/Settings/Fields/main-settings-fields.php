<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\Fields;

defined( 'ABSPATH' ) || exit;
/**
 * Setup Settings Fields.
 *
 * @return array
 */
function setup_main_settings_fields( $core, $plugin_info ) {
	return array(
		'general'    => array(
			'general' => array(
				'settings_list' => array(
					'max_best_seller'  => array(
						'input_label'  => esc_html__( 'Max Best Seller Badges', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Maximum product rank to display best seller badge.', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 3,
						'attrs'        => array(
							'min' => 1,
						),
					),
					'max_best_sellers' => array(
						'input_label'  => esc_html__( 'Max Best Sellers Products', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Maximum limit to consider a product as a best seller [ for best seller products listings ].', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 50,
						'attrs'        => array(
							'min'      => 1,
						),
					),
					'max_cats_rank'    => array(
						'input_label'  => esc_html__( 'Max Best Sellers Categories', 'best-seller-for-woocommerce' ) . $core->pro_btn( '', 'Pro', '', '', true ),
						'input_footer' => esc_html__( 'Maximum limit to consider category as a best seller. [ for best seller categories listings ]', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 10,
						'attrs'        => array(
							'min'      => 1,
							'disabled' => 'disabled',
						),
					),
				),
			),
		),
		'badge'      => array(
			'general'        => array(
				'settings_list' => array(
					'badge_global_enable' => array(
						'input_label'  => esc_html__( 'Enable', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Enable Best Seller Badge', 'best-seller-for-woocommerce' ),
						'type'         => 'checkbox',
						'value'        => 'off',
					),
					'badge_side'          => array(
						'input_label'  => esc_html__( 'Position', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Best Seller Badge Side', 'best-seller-for-woocommerce' ),
						'type'         => 'select',
						'classes'      => 'edit-badge-icon-position',
						'options'      => array(
							'top_left'      => esc_html__( 'Top Left', 'best-seller-for-woocommerce' ),
							'top_center'    => esc_html__( 'Top Center', 'best-seller-for-woocommerce' ),
							'top_right'     => esc_html__( 'Top Right', 'best-seller-for-woocommerce' ),
							'bottom_left'   => esc_html__( 'Bottom Left', 'best-seller-for-woocommerce' ),
							'bottom_center' => esc_html__( 'Bottom Center', 'best-seller-for-woocommerce' ),
							'bottom_right'  => esc_html__( 'Bottom Right', 'best-seller-for-woocommerce' ),
						),
						'value'        => 'top_left',
					),
					'badge_horz_margin'   => array(
						'input_label'  => esc_html__( 'Horizontal Margin', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge horizontal margin based on side', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 0,
						'classes'      => 'edit-badge-horz-margin',
						'attrs'        => array(
							'min' => 0,
						),
					),
					'badge_vert_margin'   => array(
						'input_label'  => esc_html__( 'Vertical Margin', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge vertical margin based on side', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 0,
						'classes'      => 'edit-badge-vert-margin',
						'attrs'        => array(
							'min' => 0,
						),
					),
					'badge_angle'         => array(
						'input_label'  => esc_html__( 'Angle', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge rotation angle', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 0,
						'classes'      => 'edit-badge-icon-angle',
						'attrs'        => array(
							'min' => 0,
						),
					),
					'badge_type'          => array(
						'input_label'  => esc_html__( 'Badge Type', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Type: Text or Icon', 'best-seller-for-woocommerce' ),
						'type'         => 'radio',
						'value'        => 'text',
						'classes'      => 'edit-badge-icon-type-radio',
						'options'      => array(
							array(
								'input_footer' => esc_html__( 'Text' ),
								'value'        => 'text',
							),
							array(
								'input_footer' => esc_html__( 'Icon' ),
								'value'        => 'icon',
							),
						),
					),
					'show_cat_on_hover'   => array(
						'input_label'  => esc_html__( 'Best Seller Category', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Show a best sellers in category link when hover on the best seller badge ( same as amazon best seller badge )', 'best-seller-for-woocommerce' ) . $core->pro_btn( '', 'Pro', '', '', true ),
						'type'         => 'checkbox',
						'value'        => 'off',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'badge_text'          => array(
						'input_label'  => esc_html__( 'Badge Text', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text', 'best-seller-for-woocommerce' ),
						'type'         => 'text',
						'value'        => esc_html__( 'Best Seller', 'best-seller-for-woocommerce' ),
						'classes'      => 'edit-badge-icon-text-radio',
						'collapse'     => array(
							'collapse_source' => 'edit-badge-icon-type-radio',
							'collapse_value'  => 'text',
						),
					),
					'badge_fontsize'      => array(
						'input_label'  => esc_html__( 'Font Size', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text fontsize', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 14,
						'classes'      => 'edit-badge-icon-text-fontsize-radio',
						'collapse'     => array(
							'collapse_source' => 'edit-badge-icon-type-radio',
							'collapse_value'  => 'text',
						),
					),
					'badge_color'         => array(
						'input_label'  => esc_html__( 'Color', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text Color', 'best-seller-for-woocommerce' ),
						'type'         => 'color',
						'value'        => '#FFFFFF',
						'classes'      => 'edit-badge-icon-text-color-radio',
						'collapse'     => array(
							'collapse_source' => 'edit-badge-icon-type-radio',
							'collapse_value'  => 'text',
						),
					),
					'badge_bg'            => array(
						'input_label'  => esc_html__( 'Background', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Background Color', 'best-seller-for-woocommerce' ),
						'type'         => 'color',
						'value'        => '#C45500',
						'classes'      => 'edit-badge-icon-text-bg-radio',

						'collapse'     => array(
							'collapse_source' => 'edit-badge-icon-type-radio',
							'collapse_value'  => 'text',
						),
					),
					'badge_font_weight'   => array(
						'input_label'  => esc_html__( 'Text Weight', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text Font Weight', 'best-seller-for-woocommerce' ),
						'type'         => 'select',
						'value'        => 'normal',
						'classes'      => 'edit-badge-icon-text-font-weight-radio',
						'options'      => array(
							'normal' => esc_html__( 'normal', 'best-seller-for-woocommerce' ),
							'bold'   => esc_html__( 'bold', 'best-seller-for-woocommerce' ),
						),
						'collapse'     => array(
							'collapse_source' => 'edit-badge-icon-type-radio',
							'collapse_value'  => 'text',
						),
					),
					'badge_font_style'    => array(
						'input_label'  => esc_html__( 'Text Style', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text Style', 'best-seller-for-woocommerce' ),
						'type'         => 'select',
						'value'        => 'normal',
						'classes'      => 'edit-badge-icon-text-font-style-radio',
						'options'      => array(
							'normal' => esc_html__( 'normal', 'best-seller-for-woocommerce' ),
							'italic' => esc_html__( 'italic', 'best-seller-for-woocommerce' ),
						),
						'collapse'     => array(
							'collapse_source' => 'edit-badge-icon-type-radio',
							'collapse_value'  => 'text',
						),
					),
					'badge_icon'          => array(
						'input_label'  => esc_html__( 'Icon', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Best Seller Badge Icon', 'best-seller-for-woocommerce' ),
						'type'         => 'radio',
						'custom_input' => true,
						'value'        => 'best-seller-icon-1.png',
						'classes'      => 'edit-badge-icon-radio',
						'options'      => array(
							array(
								'best-seller-icon-1.png' => esc_html__( 'Icon 1', 'best-seller-for-woocommerce' ),
								'best-seller-icon-2.png' => esc_html__( 'Icon 2', 'best-seller-for-woocommerce' ),
								'best-seller-icon-3.png' => esc_html__( 'Icon 3', 'best-seller-for-woocommerce' ),
								'best-seller-icon-4.png' => esc_html__( 'Icon 4', 'best-seller-for-woocommerce' ),
								'best-seller-icon-5.png' => esc_html__( 'Icon 5', 'best-seller-for-woocommerce' ),
								'best-seller-icon-6.png' => esc_html__( 'Icon 6', 'best-seller-for-woocommerce' ),
								'best-seller-icon-7.png' => esc_html__( 'Icon 7', 'best-seller-for-woocommerce' ),
								'best-seller-icon-8.png' => esc_html__( 'Icon 8', 'best-seller-for-woocommerce' ),
								'best-seller-icon-9.png' => esc_html__( 'Icon 9', 'best-seller-for-woocommerce' ),
							),
						),
						'collapse'     => array(
							'collapse_source' => 'edit-badge-icon-type-radio',
							'collapse_value'  => 'icon',
						),
					),
					'badge_width'         => array(
						'input_label'  => esc_html__( 'Width', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Best Seller Badge Width', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 50,
						'attrs'        => array(
							'min' => 1,
						),
						'classes'      => 'edit-badge-icon-width',
						'collapse'     => array(
							'collapse_source' => 'edit-badge-icon-type-radio',
							'collapse_value'  => 'icon',
						),
					),
					'badge_height'        => array(
						'input_label'  => esc_html__( 'Height', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Best Seller Badge Height', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 50,
						'classes'      => 'edit-badge-icon-height',
						'attrs'        => array(
							'min' => 1,
						),
						'collapse'     => array(
							'collapse_source' => 'edit-badge-icon-type-radio',
							'collapse_value'  => 'icon',
						),
					),
				),
			),
			'sales_badge'    => array(
				'settings_list' => array(
					'sales_loop_enable'       => array(
						'input_label'  => esc_html__( 'Enable in Loop', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Enable sales badge in loop pages', 'best-seller-for-woocommerce' ),
						'classes'      => 'edit-sales-badge-loop-enable',
						'type'         => 'checkbox',
						'value'        => 'off',
						'attrs'        => array(
							'disabled' => 'disabled',
						),

					),
					'sales_badge_side'        => array(
						'input_label'  => esc_html__( 'Position', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Side', 'best-seller-for-woocommerce' ),
						'type'         => 'select',
						'classes'      => 'edit-sales-badge-icon-position',
						'options'      => array(
							'top_left'      => esc_html__( 'Top Left', 'best-seller-for-woocommerce' ),
							'top_center'    => esc_html__( 'Top Center', 'best-seller-for-woocommerce' ),
							'top_right'     => esc_html__( 'Top Right', 'best-seller-for-woocommerce' ),
							'bottom_left'   => esc_html__( 'Bottom Left', 'best-seller-for-woocommerce' ),
							'bottom_center' => esc_html__( 'Bottom Center', 'best-seller-for-woocommerce' ),
							'bottom_right'  => esc_html__( 'Bottom Right', 'best-seller-for-woocommerce' ),
						),
						'value'        => 'bottom_center',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'sales_badge_horz_margin' => array(
						'input_label'  => esc_html__( 'Horizontal Margin', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge horizontal margin based on side', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 0,
						'classes'      => 'edit-sales-badge-horz-margin',
						'attrs'        => array(
							'min'      => 0,
							'disabled' => 'disabled',
						),
					),
					'sales_badge_vert_margin' => array(
						'input_label'  => esc_html__( 'Vertical Margin', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge vertical margin based on side', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 0,
						'classes'      => 'edit-sales-badge-vert-margin',
						'attrs'        => array(
							'min'      => 0,
							'disabled' => 'disabled',
						),
					),
					'sales_badge_fontsize'    => array(
						'input_label'  => esc_html__( 'Font Size', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge sales count fontsize', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 14,
						'classes'      => 'edit-sales-badge-icon-text-fontsize-radio',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'sales_badge_font_weight' => array(
						'input_label'  => esc_html__( 'Font Weight', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text Font Weight', 'best-seller-for-woocommerce' ),
						'type'         => 'select',
						'value'        => 'bold',
						'classes'      => 'edit-sales-badge-icon-text-font-weight-radio',
						'options'      => array(
							'normal' => esc_html__( 'normal', 'best-seller-for-woocommerce' ),
							'bold'   => esc_html__( 'bold', 'best-seller-for-woocommerce' ),
						),
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'sales_badge_font_style'  => array(
						'input_label'  => esc_html__( 'Text Style', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text Style', 'best-seller-for-woocommerce' ),
						'type'         => 'select',
						'value'        => 'normal',
						'classes'      => 'edit-sales-badge-icon-text-font-style-radio',
						'options'      => array(
							'normal' => esc_html__( 'normal', 'best-seller-for-woocommerce' ),
							'italic' => esc_html__( 'italic', 'best-seller-for-woocommerce' ),
						),
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'sales_badge_style_loop'  => array(
						'input_label'  => esc_html__( 'Style in loop', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Sales Badge Style in loop pages', 'best-seller-for-woocommerce' ),
						'type'         => 'select',
						'options'      => array(
							'horz' => esc_html__( 'Horizontal', 'best-seller-for-woocommerce' ),
							'vert' => esc_html__( 'Vertical', 'best-seller-for-woocommerce' ),
						),
						'value'        => 'vert',
						'classes'      => 'edit-sales-badge-icon-style',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'sales_badge_color'       => array(
						'input_label'  => esc_html__( 'Color', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text Color', 'best-seller-for-woocommerce' ),
						'type'         => 'color',
						'value'        => '#FFFFFF',
						'classes'      => 'edit-sales-badge-icon-text-color-radio',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'sales_badge_bg'          => array(
						'input_label'  => esc_html__( 'Background', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Background Color', 'best-seller-for-woocommerce' ),
						'type'         => 'color',
						'value'        => '#63D981',
						'classes'      => 'edit-sales-badge-icon-text-bg-radio',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'sales_single_enable'     => array(
						'input_label'  => esc_html__( 'Enable in single', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Enable sales badge in single product page', 'best-seller-for-woocommerce' ),
						'classes'      => 'edit-sales-badge-single-enable',
						'type'         => 'checkbox',
						'value'        => 'off',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
				),
			),
			'category_badge' => array(
				'settings_list' => array(
					'category_badge_color'       => array(
						'input_label'  => esc_html__( 'Color', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text Color', 'best-seller-for-woocommerce' ),
						'type'         => 'color',
						'value'        => '#FFFFFF',
						'classes'      => 'edit-category-badge-icon-text-color-radio',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'category_badge_bg'          => array(
						'input_label'  => esc_html__( 'Background', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Background Color', 'best-seller-for-woocommerce' ),
						'type'         => 'color',
						'value'        => '#C45500',
						'classes'      => 'edit-category-badge-icon-text-bg-radio',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'category_badge_fontsize'    => array(
						'input_label'  => esc_html__( 'Font Size', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge sales count fontsize', 'best-seller-for-woocommerce' ),
						'type'         => 'number',
						'value'        => 14,
						'classes'      => 'edit-category-badge-icon-text-fontsize-radio',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'category_badge_font_weight' => array(
						'input_label'  => esc_html__( 'Font Weight', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Badge Text Font Weight', 'best-seller-for-woocommerce' ),
						'type'         => 'select',
						'value'        => 'normal',
						'classes'      => 'edit-category-badge-icon-text-font-weight-radio',
						'options'      => array(
							'normal' => esc_html__( 'normal', 'best-seller-for-woocommerce' ),
							'bold'   => esc_html__( 'bold', 'best-seller-for-woocommerce' ),
						),
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
					'category_badge_loop_enable' => array(
						'input_label'  => esc_html__( 'Enable in loop', 'best-seller-for-woocommerce' ),
						'input_footer' => esc_html__( 'Show product best rank in category badge in loop pages.', 'best-seller-for-woocommerce' ),
						'type'         => 'checkbox',
						'value'        => 'off',
						'attrs'        => array(
							'disabled' => 'disabled',
						),
					),
				),
			),
		),
		'shortcodes' => array(
			'general' => array(
				'settings_list' => array(),
			),
		),
	);
}
