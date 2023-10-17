<?php

defined( 'ABSPATH' ) || exit;

$cost_desc = __( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'bakkbone-florist-companion' ) . '<br/><br/>' . __( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'bakkbone-florist-companion' );

$settings = array(
	'title'      => array(
		'title'       => __( 'Method title', 'bakkbone-florist-companion' ),
		'type'        => 'text',
		'description' => __( 'This controls the title which the user sees during checkout.', 'bakkbone-florist-companion' ),
		'default'     => __( 'Delivery', 'bakkbone-florist-companion' ),
		'desc_tip'    => true,
	),
	'tax_status' => array(
		'title'   => __( 'Tax status', 'bakkbone-florist-companion' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'default' => 'taxable',
		'options' => array(
			'taxable' => __( 'Taxable', 'bakkbone-florist-companion' ),
			'none'    => _x( 'None', 'Tax status', 'bakkbone-florist-companion' ),
		),
	),
	'cost'       => array(
		'title'             => __( 'Cost', 'bakkbone-florist-companion' ),
		'type'              => 'text',
		'placeholder'       => '',
		'description'       => $cost_desc,
		'default'           => '0',
		'desc_tip'          => true,
		'sanitize_callback' => array( $this, 'sanitize_cost' ),
	),
	'method_suburbs'    => array(
		'title'             => __( 'Suburbs', 'bakkbone-florist-companion' ),
		'type'              => 'textarea',
		'placeholder'       => __('One per line', 'bakkbone-florist-companion'),
		'description'       => __('Enter one suburb per line, in the exact format you would write on a postage envelope. Case does not matter, only spelling – Perth would be treated the same as PERTH or perth. Ensure there are no additional spaces at the beginning or end of each line.', 'bakkbone-florist-companion'),
		'default'           => '',
		'desc_tip'          => true,
	),
);

$shipping_classes = WC()->shipping()->get_shipping_classes();

if ( ! empty( $shipping_classes ) ) {
	$settings['class_costs'] = array(
		'title'       => __( 'Delivery class costs', 'bakkbone-florist-companion' ),
		'type'        => 'title',
		'default'     => '',
		/* translators: %s: URL for link. */
		'description' => sprintf( __( 'These costs can optionally be added based on the <a href="%s">product delivery class</a>.', 'bakkbone-florist-companion' ), admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ) ),
	);
	foreach ( $shipping_classes as $shipping_class ) {
		if ( ! isset( $shipping_class->term_id ) ) {
			continue;
		}
		$settings[ 'class_cost_' . $shipping_class->term_id ] = array(
			/* translators: %s: delivery class name */
			'title'             => sprintf( __( '"%s" delivery class cost', 'bakkbone-florist-companion' ), esc_html( $shipping_class->name ) ),
			'type'              => 'text',
			'placeholder'       => __( 'N/A', 'bakkbone-florist-companion' ),
			'description'       => $cost_desc,
			'default'           => $this->get_option( 'class_cost_' . $shipping_class->slug ), // Before 2.5.0, we used slug here which caused issues with long setting names.
			'desc_tip'          => true,
			'sanitize_callback' => array( $this, 'sanitize_cost' ),
		);
	}

	$settings['no_class_cost'] = array(
		'title'             => __( 'No delivery class cost', 'bakkbone-florist-companion' ),
		'type'              => 'text',
		'placeholder'       => __( 'N/A', 'bakkbone-florist-companion' ),
		'description'       => $cost_desc,
		'default'           => '',
		'desc_tip'          => true,
		'sanitize_callback' => array( $this, 'sanitize_cost' ),
	);

	$settings['type'] = array(
		'title'   => __( 'Calculation type', 'bakkbone-florist-companion' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'default' => 'class',
		'options' => array(
			'class' => __( 'Per class: Charge delivery for each delivery class individually', 'bakkbone-florist-companion' ),
			'order' => __( 'Per order: Charge delivery for the most expensive delivery class', 'bakkbone-florist-companion' ),
		),
	);
}

return $settings;