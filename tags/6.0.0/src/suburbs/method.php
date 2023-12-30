<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Suburbs_Shipping_Method
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

function BKF_Suburbs_filter_shipping_methods($rates, $package){
	$sm = bkf_get_shipping_rates();	
	$customer_sub = strtoupper( isset( $_REQUEST['s_city'] ) ? $_REQUEST['s_city'] : ( isset ( $_REQUEST['calc_shipping_city'] ) ? $_REQUEST['calc_shipping_city'] : ( ! empty( $user_city = WC()->customer->get_shipping_city() ) ? $user_city : WC()->countries->get_base_city() ) ) );
	
	foreach($sm as $smethod){
		if($smethod['type'] == 'floristpress' && !empty($smethod['method_suburbs'])){
			$suburbs = array_map('strtoupper', $smethod['method_suburbs']);
			
			if(!in_array($customer_sub, $suburbs)){
				unset($rates[$smethod['rateid']]);
			}
		} elseif($smethod['type'] == 'floristpress' && empty($smethod['method_suburbs'])) {
			unset($rates[$smethod['rateid']]);
		}
	}
	return $rates;
}

add_filter('woocommerce_package_rates', 'BKF_Suburbs_filter_shipping_methods', PHP_INT_MAX - 1, 2);

function BKF_Suburbs_add_shipping_method( $methods ) {
	$methods['floristpress'] = 'BKF_Shipping_Method';
	return $methods;
}

add_filter( 'woocommerce_shipping_methods', 'BKF_Suburbs_add_shipping_method' );

function BKF_Suburbs_init_shipping_method(){
	if ( ! class_exists( 'BKF_Shipping_Method' ) ) {
		class BKF_Shipping_Method extends WC_Shipping_Method {
			protected $fee_cost = '';

			public $cost;

			public $type;

			public function __construct( $instance_id = 0 ) {
				
				$this->id                 = 'floristpress';
				$this->instance_id        = absint( $instance_id );
				$this->method_order       = 1;
				$this->method_title       = __( 'FloristPress Suburbs List', 'bakkbone-florist-companion' );
				$this->method_description = __( 'Uses the FloristPress Delivery Suburbs feature to restrict the method per suburb within the Zone.', 'bakkbone-florist-companion' );
				$this->supports           = array(
					'shipping-zones',
					'instance-settings',
					'instance-settings-modal',
				);
				$this->init();

				add_action( 'woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options'] );
			}

			public function init() {
				$this->instance_form_fields = include __DIR__ . '/method-settings.php';
				$this->title                = $this->get_option( 'title' );
				$this->tax_status           = $this->get_option( 'tax_status' );
				$this->cost                 = $this->get_option( 'cost' );
				$this->type                 = $this->get_option( 'type', 'class' );
			}

			protected function evaluate_cost( $sum, $args = [] ) {
				// Add warning for subclasses.
				if ( ! is_array( $args ) || ! array_key_exists( 'qty', $args ) || ! array_key_exists( 'cost', $args ) ) {
					wc_doing_it_wrong( __FUNCTION__, '$args must contain `cost` and `qty` keys.', '4.0.1' );
				}

				include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';

				// Allow 3rd parties to process shipping cost arguments.
				$args           = apply_filters( 'woocommerce_evaluate_shipping_cost_args', $args, $sum, $this );
				$locale         = localeconv();
				$decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );
				$this->fee_cost = $args['cost'];

				// Expand shortcodes.
				add_shortcode( 'fee', [$this, 'fee'] );

				$sum = do_shortcode(
					str_replace(
						array(
							'[qty]',
							'[cost]',
						),
						array(
							$args['qty'],
							$args['cost'],
						),
						$sum
					)
				);

				remove_shortcode( 'fee', [$this, 'fee'] );

				// Remove whitespace from string.
				$sum = preg_replace( '/\s+/', '', $sum );

				// Remove locale from string.
				$sum = str_replace( $decimals, '.', $sum );

				// Trim invalid start/end characters.
				$sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

				// Do the math.
				return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
			}

			public function fee( $atts ) {
				$atts = shortcode_atts(
					array(
						'percent' => '',
						'min_fee' => '',
						'max_fee' => '',
					),
					$atts,
					'fee'
				);

				$calculated_fee = 0;

				if ( $atts['percent'] ) {
					$calculated_fee = $this->fee_cost * ( floatval( $atts['percent'] ) / 100 );
				}

				if ( $atts['min_fee'] && $calculated_fee < $atts['min_fee'] ) {
					$calculated_fee = $atts['min_fee'];
				}

				if ( $atts['max_fee'] && $calculated_fee > $atts['max_fee'] ) {
					$calculated_fee = $atts['max_fee'];
				}

				return $calculated_fee;
			}

			public function calculate_shipping( $package = [] ) {
				$rate = array(
					'id'      => $this->get_rate_id(),
					'label'   => $this->title,
					'cost'    => 0,
					'package' => $package,
				);

				// Calculate the costs.
				$has_costs = false; // True when a cost is set. False if all costs are blank strings.
				$cost      = $this->get_option( 'cost' );

				if ( '' !== $cost ) {
					$has_costs    = true;
					$rate['cost'] = $this->evaluate_cost(
						$cost,
						array(
							'qty'  => $this->get_package_item_qty( $package ),
							'cost' => $package['contents_cost'],
						)
					);
				}

				// Add shipping class costs.
				$shipping_classes = WC()->shipping()->get_shipping_classes();

				if ( ! empty( $shipping_classes ) ) {
					$found_shipping_classes = $this->find_shipping_classes( $package );
					$highest_class_cost     = 0;

					foreach ( $found_shipping_classes as $shipping_class => $products ) {
						// Also handles BW compatibility when slugs were used instead of ids.
						$shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
						$class_cost_string   = $shipping_class_term && $shipping_class_term->term_id ? $this->get_option( 'class_cost_' . $shipping_class_term->term_id, $this->get_option( 'class_cost_' . $shipping_class, '' ) ) : $this->get_option( 'no_class_cost', '' );

						if ( '' === $class_cost_string ) {
							continue;
						}

						$has_costs  = true;
						$class_cost = $this->evaluate_cost(
							$class_cost_string,
							array(
								'qty'  => array_sum( wp_list_pluck( $products, 'quantity' ) ),
								'cost' => array_sum( wp_list_pluck( $products, 'line_total' ) ),
							)
						);

						if ( 'class' === $this->type ) {
							$rate['cost'] += $class_cost;
						} else {
							$highest_class_cost = $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost;
						}
					}

					if ( 'order' === $this->type && $highest_class_cost ) {
						$rate['cost'] += $highest_class_cost;
					}
				}

				if ( $has_costs ) {
					$this->add_rate( $rate );
				}

				do_action( 'woocommerce_' . $this->id . '_shipping_add_rate', $this, $rate );
			}

			public function get_package_item_qty( $package ) {
				$total_quantity = 0;
				foreach ( $package['contents'] as $item_id => $values ) {
					if ( $values['quantity'] > 0 && $values['data']->needs_shipping() ) {
						$total_quantity += $values['quantity'];
					}
				}
				return $total_quantity;
			}

			public function find_shipping_classes( $package ) {
				$found_shipping_classes = [];

				foreach ( $package['contents'] as $item_id => $values ) {
					if ( $values['data']->needs_shipping() ) {
						$found_class = $values['data']->get_shipping_class();

						if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
							$found_shipping_classes[ $found_class ] = [];
						}

						$found_shipping_classes[ $found_class ][ $item_id ] = $values;
					}
				}

				return $found_shipping_classes;
			}

			public function sanitize_cost( $value ) {
				$value = is_null( $value ) ? '' : $value;
				$value = wp_kses_post( trim( wp_unslash( $value ) ) );
				$value = str_replace( array( get_woocommerce_currency_symbol(), html_entity_decode( get_woocommerce_currency_symbol() ) ), '', $value );
				// Thrown an error on the front end if the evaluate_cost will fail.
				$dummy_cost = $this->evaluate_cost(
					$value,
					array(
						'cost' => 1,
						'qty'  => 1,
					)
				);
				if ( false === $dummy_cost ) {
					throw new Exception( WC_Eval_Math::$last_error );
				}
				return $value;
			}
			
			public function sanitize_suburbs( $value ) {
				$array = explode("\r\n",$value);
				sort($array);
				$newarray = [];
				foreach($array as $item){
					$newarray[] = sanitize_text_field($item);
				}
				$newvalue = implode("\r\n",$newarray);
				return $newvalue;
			}
		}
	}
}

add_action('woocommerce_shipping_init', 'BKF_Suburbs_init_shipping_method');