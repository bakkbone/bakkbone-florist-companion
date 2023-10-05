<?php

/**
 * @author BAKKBONE Australia
 * @package BkfFunctions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

function bkf_shipping_tax_rates(){
    $tax = new WC_Tax();
    $rates = $tax->get_rates();
    $result = [];
    foreach($rates as $rate){
        if($rate['shipping'] == "yes"){
            $result[] = $rate;
        }
    }
    return $result;
}

function bkf_get_shipping_methods(){
	$allzones = WC_Data_Store::load('shipping-zone');
	$rawzones = $allzones->get_zones();
	$zones = array();
	$zones[] = new WC_Shipping_Zone( 0 );
	foreach($rawzones as $rawzone){
		$zones[] = new WC_Shipping_Zone( $rawzone );
	}
	
	$sm = array();
	foreach($zones as $zone){
		$items = $zone->get_shipping_methods();
		foreach($items as $item){
			$sm[] = $item;
		}

	}
	
	return $sm;
}

function bkf_get_shipping_methods_array(){
	$allzones = WC_Data_Store::load('shipping-zone');
	$rawzones = $allzones->get_zones();
	$zones = array();
	$zones[] = new WC_Shipping_Zone( 0 );
	foreach($rawzones as $rawzone){
		$zones[] = new WC_Shipping_Zone( $rawzone );
	}
	
	$sm = array();
	foreach($zones as $zone){
		$items = $zone->get_shipping_methods();
		foreach($items as $item){
			$sm[] = array(
				'zone'		=>	array(
					'name'		=>	$zone->get_zone_name(),
					'id'		=>	$zone->get_id(),
					'locations'	=>	$zone->get_zone_locations(),
					'location'	=>	$zone->get_formatted_location()
				),
				'method'	=>	$item,
				'methodid'	=>	$item->get_instance_id()
			);
		}

	}
	
	return $sm;
}

function bkf_get_shipping_rates(){
	$methods = bkf_get_shipping_methods_array();
	$suburbs = bkf_get_all_suburbs();
	
	$sr = array();
	
	foreach($methods as $thismethod){
		$method = $thismethod['method'];
		$zone = $thismethod['zone'];
		$method_is_enabled = $method->is_enabled();
		$method_instance_id = $method->get_instance_id();
		$method_rate_id = $method->get_rate_id();
		$title = $method->get_method_title();
		$instance = $method->instance_settings;
		$usertitle = $instance['title'];
		$taxable = array_key_exists('tax_status',$instance) ? $instance['tax_status'] : 'none';
		$cost = array_key_exists('cost',$instance) ? $instance['cost'] : '0';
		
		$methodsuburbs = array();
		foreach($suburbs as $suburb){
			if($suburb['method'] == $method_rate_id){
				$methodsuburbs[] = $suburb['suburb'];
			}
		}
		
		if(0 === strpos($method_rate_id, 'local_pickup')){
			$pickup = true;
		} else {
			$pickup = false;
		}
		
		if(!empty($methodsuburbs)){
			$hassuburbs = true;
		} else {
			$hassuburbs = false;
		}
		
		$sr[] = array(
			'enabled'		=> $method_is_enabled,
			'instanceid'	=> $method_instance_id,
			'rateid'		=> $method_rate_id,
			'usertitle'		=> $usertitle,
			'tax_status'	=> $taxable,
			'cost'			=> $cost,
			'suburbs'		=> $methodsuburbs,
			'hassuburbs'	=> $hassuburbs,
			'title'			=> $title,
			'zone'			=> $zone['name'],
			'zoneid'        => $zone['id'],
			'zonelocations'	=> $zone['locations'],
			'zonelocation'	=> $zone['location'],
			'pickup'		=> $pickup
		);
	}
	return $sr;
}

function bkf_get_shipping_rates_associative(){
	$methods = bkf_get_shipping_methods_array();
	$suburbs = bkf_get_all_suburbs();
	
	$sr = array();
	
	foreach($methods as $thismethod){
		$method = $thismethod['method'];
		$zone = $thismethod['zone'];
		$method_is_enabled = $method->is_enabled();
		$method_instance_id = $method->get_instance_id();
		$method_rate_id = $method->get_rate_id();
		$title = $method->get_method_title();
		$instance = $method->instance_settings;
		$usertitle = $instance['title'];
		$taxable = array_key_exists('tax_status',$instance) ? $instance['tax_status'] : 'none';
		$cost = array_key_exists('cost',$instance) ? $instance['cost'] : '0';
		
		$methodsuburbs = array();
		foreach($suburbs as $suburb){
			if($suburb['method'] == $method_rate_id){
				$methodsuburbs[] = $suburb['suburb'];
			}
		}
		
		if(0 === strpos($method_rate_id, 'local_pickup')){
			$pickup = true;
		} else {
			$pickup = false;
		}
		
		if(!empty($methodsuburbs)){
			$hassuburbs = true;
		} else {
			$hassuburbs = false;
		}
		
		$sr[$method_rate_id] = array(
			'enabled'		=> $method_is_enabled,
			'instanceid'	=> $method_instance_id,
			'rateid'		=> $method_rate_id,
			'usertitle'		=> $usertitle,
			'tax_status'	=> $taxable,
			'cost'			=> $cost,
			'suburbs'		=> $methodsuburbs,
			'hassuburbs'	=> $hassuburbs,
			'title'			=> $title,
			'zone'			=> $zone['name'],
			'zonelocations'	=> $zone['locations'],
			'zonelocation'	=> $zone['location'],
			'pickup'		=> $pickup
		);
	}
	return $sr;
}

function bkf_get_shipping_rates_for_suburb($suburb){
	// only excludes rates with a suburbs list where the suburb is not present - does not check zone restrictions
	$rates = array();
	$allrates = bkf_get_shipping_rates();
	
	foreach($allrates as $rate){
		if($rate['hassuburbs']){
			$suburbs = array_map('strtoupper', $rate['suburbs']);
			if(in_array(strtoupper($suburb), $suburbs)){
				$rates[] = $rate;
			}
		} elseif(! $rate['hassuburbs']){
			$rates[] = $rate;
		}
	}
	
	return $rates;
}

function bkf_get_shipping_rates_for_location($suburb, $postcode, $state, $country){
	$allrates = bkf_get_shipping_rates();
	$rates = array();
	
	foreach($allrates as $rate){
		
		$pass = '';
		$countrypass = '';
		$countryarray = array();
		$passarray = array();
		$pcfail = array();
		
		if($rate['hassuburbs']){
			if(!in_array($suburb, $rate['suburbs'])){
				unset($rate);
			} elseif(!empty($rate['zonelocations'])){
				$postcodes = wp_list_filter($rate['zonelocations'],array('type'=>'postcode'));
				$haspostcodes = !empty($postcodes) ? true : false;
				
				if($haspostcodes){
					foreach($rate['zonelocations'] as $location){
						if($location->type == 'postcode'){
							if(preg_match('/[\d\w]{1,10}...[\d\w]{1,10}/',$location->code)){
								$rangelimits = explode('...',$location->code);
								if($postcode < $rangelimits[0] || $postcode > $rangelimits[1]){
									$pcfail[] = 'fail';
								} else {
									$pcfail[] = 'pass';
								}
							} else {
								if($postcode !== $location->code){
									$pcfail[] = 'fail';
								} else {
									$pcfail[] = 'pass';
								}
							}
						} elseif($location->type == 'state'){
							$locationarray = explode(':',$location->code);
							$locationcountry = $locationarray[0];
							$locationstate = $locationarray[1];
							
							if($locationstate === $state && $locationcountry === $country){
								$passarray[] = 'true';
							} else {
								$passarray[] = 'false';
							}
						} elseif($location->type == 'country') {
							if($location->code !== $country){
								$countryarray[] = 'false';
							} else {
								$countryarray[] = 'true';
							}
						}
					}			
				} else {
					foreach($rate['zonelocations'] as $location){
						if($location->type == 'state'){
							$locationarray = explode(':',$location->code);
							$locationcountry = $locationarray[0];
							$locationstate = $locationarray[1];
							
							if($locationstate === $state && $locationcountry === $country){
								$passarray[] = 'true';
							} else {
								$passarray[] = 'false';
							}
						} elseif($location->type == 'country') {
							if($location->code !== $country){
								$countryarray[] = 'false';
							} else {
								$countryarray[] = 'true';
							}
						}
					}
				}
				
				if(empty($pcfail)){
					$pcfailed = '';
				} elseif(in_array('pass',$pcfail)){
					$pcfailed = '';
				} else {
					$pcfailed = 'fail';
				}
				
				if(empty($passarray)){
					$pass = 'true';
				} elseif((in_array('true',$passarray))){
					$pass = 'true';
				} else {
					$pass = 'false';
				}
				
				if(empty($countryarray)){
					$countrypass = 'true';
				} elseif((in_array('true',$countryarray))){
					$countrypass = 'true';
				} else {
					$countrypass = 'false';
				}

				if($pcfailed == '' && $pass == 'true' && $countrypass == 'true'){
					$rates[] = $rate;
				}
			}
		} elseif(!empty($rate['zonelocations'])){
				$postcodes = wp_list_filter($rate['zonelocations'],array('type'=>'postcode'));
				$haspostcodes = !empty($postcodes) ? true : false;
				
				if($haspostcodes){
					foreach($rate['zonelocations'] as $location){
						if($location->type == 'postcode'){
							if(preg_match('/[\d\w]{1,10}...[\d\w]{1,10}/',$location->code)){
								$rangelimits = explode('...',$location->code);
								if($postcode < $rangelimits[0] || $postcode > $rangelimits[1]){
									$pcfail[] = 'fail';
								} else {
									$pcfail[] = 'pass';
								}
							} else {
								if($postcode !== $location->code){
									$pcfail[] = 'fail';
								} else {
									$pcfail[] = 'pass';
								}
							}
						} elseif($location->type == 'state'){
							$locationarray = explode(':',$location->code);
							$locationcountry = $locationarray[0];
							$locationstate = $locationarray[1];
							
							if($locationstate === $state && $locationcountry === $country){
								$passarray[] = 'true';
							} else {
								$passarray[] = 'false';
							}
						} elseif($location->type == 'country') {
							if($location->code !== $country){
								$countryarray[] = 'false';
							} else {
								$countryarray[] = 'true';
							}
						}
					}			
				} else {
					foreach($rate['zonelocations'] as $location){
						if($location->type == 'state'){
							$locationarray = explode(':',$location->code);
							$locationcountry = $locationarray[0];
							$locationstate = $locationarray[1];
							
							if($locationstate === $state && $locationcountry === $country){
								$passarray[] = 'true';
							} else {
								$passarray[] = 'false';
							}
						} elseif($location->type == 'country') {
							if($location->code !== $country){
								$countryarray[] = 'false';
							} else {
								$countryarray[] = 'true';
							}
						}
					}
				}
				if(empty($pcfail)){
					$pcfailed = '';
				} elseif(in_array('pass',$pcfail)){
					$pcfailed = '';
				} else {
					$pcfailed = 'fail';
				}
				
				if(empty($passarray)){
					$pass = 'true';
				} elseif((in_array('true',$passarray))){
					$pass = 'true';
				} else {
					$pass = 'false';
				}
				
				if(empty($countryarray)){
					$countrypass = 'true';
				} elseif((in_array('true',$countryarray))){
					$countrypass = 'true';
				} else {
					$countrypass = 'false';
				}

				if($pcfailed == '' && $pass == 'true' && $countrypass == 'true'){
					$rates[] = $rate;
				}
			}
	}
	
	return $rates;
}

function bkf_get_all_suburbs(){
	global $wpdb;
	$sub = array();
	$subs = $wpdb->get_results(
		"
			SELECT id, method, suburb
			FROM {$wpdb->prefix}bkf_suburbs
		"
	);
	foreach($subs as $suburb){
		$sub[] = array(
			'id'		=>	$suburb->id,
			'method'	=>	$suburb->method,
			'suburb'	=>	$suburb->suburb
		);
	}
	
	return $sub;
}

function bkf_get_suburbs_by_rate(){
	$subs = bkf_get_all_suburbs();
	$rates = bkf_get_shipping_rates();
	$suburbs = array();
	
	foreach($rates as $rate){
		$rateid = $rate['rateid'];
		$suburbs[$rateid] = array();
		foreach($subs as $sub){
			if($sub['method'] == $rate['rateid']){
				$suburbs[$rateid][] = $sub['suburb'];
			}
		}
	}
	
	return $suburbs;
}

function bkf_currency_symbol( $echo = false ){
	if($echo){
		echo html_entity_decode(get_woocommerce_currency_symbol(get_woocommerce_currency()));
	} else {
		return html_entity_decode(get_woocommerce_currency_symbol(get_woocommerce_currency()));
	}
}

function bkf_get_rss_feed($feed_url) {
	$content = file_get_contents($feed_url);
	$x = new SimpleXmlElement($content);
	
	$items = array();
	foreach($x->channel->item as $entry) {
		$items[] = $entry;
	}
	return $items;
}

function bkf_full_count(){
	$statuslist = wc_get_order_statuses();
	$total = 0;
	foreach($statuslist as $key => $value){
		$count = wc_orders_count($key);
		$total += $count;
	}
	return $total;
}

function bkf_all_count(){
	$allstatus = array("wc-processing","wc-made","wc-collect","wc-out","wc-scheduled","wc-new","wc-accept","wc-invoiced","wc-phone-draft");
	$total = 0;
	foreach($allstatus as $key){
		$count = wc_orders_count($key);
		$total += $count;
	}
	return $total;
}

function bkf_cart_has_physical(){
	$has_physical = false;

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( ! $cart_item['data']->is_virtual() ) {
			$has_physical = true;
			break;
		}
	}
	
	return $has_physical;
}

function bkf_order_has_physical($orderid){
	$order = new WC_Order($orderid);
	return $order->needs_shipping_address();
}

function bkf_shop_has_pickup(){
	$sm = bkf_get_shipping_rates();
	$pu = array();
	foreach($sm as $smethod){
		if($smethod['enabled'] == true && !empty(strpos($smethod['rateid'],'pickup'))){
			$pu[] = $smethod;
		}
	}
	if(!empty($pu)){
		return true;
	} else {
		return false;
	}
}

function bkf_get_timeslots(){
	$ts = array();
	global $wpdb;
	$timeslots = $wpdb->get_results(
		"
			SELECT id, method, day, start, end, fee
			FROM {$wpdb->prefix}bkf_dd_timeslots
		"
	);
	foreach($timeslots as $timeslot){
		$ts[] = array(
			'id'		=>	$timeslot->id,
			'method'	=>	$timeslot->method,
			'day'		=>	$timeslot->day,
			'start'		=>	$timeslot->start,
			'end'		=>	$timeslot->end,
			'fee'		=>	$timeslot->fee
		);
	}
	uasort($ts, function($a,$b){ return strcmp($a['start'],$b['start']);} );
	return $ts;
}

function bkf_get_timeslots_associative(){
	$ts = array();
	global $wpdb;
	$timeslots = $wpdb->get_results(
		"
			SELECT id, method, day, start, end, fee
			FROM {$wpdb->prefix}bkf_dd_timeslots
		"
	);
	foreach($timeslots as $timeslot){
		$ts['ts'.$timeslot->id] = array(
			'id'		=>	$timeslot->id,
			'method'	=>	$timeslot->method,
			'day'		=>	$timeslot->day,
			'start'		=>	$timeslot->start,
			'end'		=>	$timeslot->end,
			'fee'		=>	$timeslot->fee
		);
	}
	uasort($ts, function($a,$b){ return strcmp($a['start'],$b['start']);} );
	return $ts;
}

function bkf_get_timeslots_for_order($method,$day){
	$ts = bkf_get_timeslots();
	
	$timeslots = array();
	foreach($ts as $tslot){
		if($tslot['day'] == strtolower($day) && $tslot['method'] == $method){
			$timeslots[] = $tslot;
		}
	}
	
	return $timeslots;
}

function bkf_get_timeslot_string($id){
	$result = bkf_get_timeslots_associative()['ts'.$id];
	
	$string = date("g:i a", strtotime($result['start'])).' - '.date("g:i a", strtotime($result['end']));
	
	return $string;
}

function bkf_get_catblocks(){
	global $wpdb;
	$cb = array();
	$catblocks = $wpdb->get_results(
		"
			SELECT id, category, date
			FROM {$wpdb->prefix}bkf_dd_catblocks
		"
	);
	foreach($catblocks as $catblock){
		$cb[] = array(
			'id'		=>	$catblock->id,
			'category'	=>	$catblock->category,
			'date'		=>	$catblock->date
		);
	}
	
	return $cb;	
}

function bkf_get_cutoffs(){
	global $wpdb;
	$co = array();
	$cutoffs = $wpdb->get_results(
		"
			SELECT id, method, day, cutoff
			FROM {$wpdb->prefix}bkf_dd_sameday_methods
		"
	);
	foreach($cutoffs as $cutoff){
		$co[$cutoff->method.'-'.$cutoff->day] = array(
			'id'		=>	$cutoff->id,
			'method'	=>	$cutoff->method,
			'day'		=>	$cutoff->day,
			'cutoff'	=>	$cutoff->cutoff
		);
	}
	
	return $co;

}

function bkf_get_customers(){
	$userquery = get_users(array('fields'=>'all'));
	$users = array();
	foreach($userquery as $thisuser){
		$user = new WC_Customer($thisuser->ID);
		$company = '';
		$company = $user->get_billing_company();
		$business = $company !== '' ? true : false;
		$users[] = array(
			'id'		=> $thisuser->ID,
			'name'		=> $user->get_billing_first_name() . ' ' . $user->get_billing_last_name(),
			'company'	=> $company,
			'email'		=> $user->get_billing_email(),
			'phone'		=> $user->get_billing_phone(),
			'business'	=> $business
		);
	}
	
	return $users;
}

function bkf_get_all_products(){
	$pq = array(
		'numberposts'	=> '-1',
		'post_type'		=> 'product',
		'orderby'		=> 'title',
		'post_status'	=> 'any'
	);
	$productquery = get_posts($pq);
	$products = array();
	foreach($productquery as $item){
		$thisitem = wc_get_product($item->ID);
		$products[] = array(
			'id'	=> $item->ID,
			'name'	=> $thisitem->get_formatted_name(),
			'cat'	=> strip_tags(wc_get_product_category_list($item->ID)),
			'has_child'=>$thisitem->has_child(),
			'price'	=> $thisitem->get_price_html(),
			'value'	=> $thisitem->get_price(),
			'virtual'=> $thisitem->is_virtual()
		);
		if($thisitem->has_child()){
			$variations = $thisitem->get_available_variations();
			$variations_id = wp_list_pluck( $variations, 'variation_id' );
			foreach($variations_id as $child){
				$thischild = new WC_Product_Variation($child);
				$variation = wc_get_product($child);
				$products[] = array(
					'id'	=> $child,
					'name'	=> $variation->get_formatted_name(),
					'cat'	=> strip_tags(wc_get_product_category_list($thischild->get_parent_id())),
					'has_child'=>$variation->has_child(),
					'price'	=> $variation->get_price_html(),
					'value'	=> $variation->get_price(),
					'virtual'=> $thisitem->is_virtual()
				);
			}
		}
	}
	
	return $products;
}

function bkf_check_weekday_fee($day){
	$fees = get_option('bkf_wf_setting');
	$checkday = strtolower($day);
	if(array_key_exists($day,$fees)){
		if($fees[$day] !== ''){
			$fee = $fees[$day];
		} else {
			$fee = '0.00';
		}
	} else {
		$fee = '0.00';
	}
	
	return $fee;
}

function bkf_check_dd_fee($date){
	date_default_timezone_set(wp_timezone_string());
	$checkdate = strtotime($date);
	$fees = get_option('bkf_dd_ds_fees');
	if(array_key_exists($checkdate, $fees)){
		$fee = $fees[$checkdate];
	} else {
		$fee = array();
	}
	
	return $fee;
}

function bkf_get_monday(){
	if(wp_date('w') == 1){
		$date = wp_date('Y-m-d');
	} else {
		$date = date("Y-m-d", strtotime('previous monday'));
	}
	return $date;
}