<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_API
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_API {
	
	function __construct() {
		add_action( 'rest_api_init', [$this, 'add_custom_fields'] );
		add_filter( 'rest_post_dispatch', [$this, 'rest_post_dispatch'], 10, 3);
	}
	
	function rest_post_dispatch($result, $server, $request) {
		if ($result->get_matched_route() == '/wc/v3/shipping/zones/(?P<zone_id>[\d]+)/methods') {
			$data = $result->data;
			$newdata = [];
			foreach ($data as $item) {
				if ($item['method_id'] == 'floristpress') {
					$suburbs = explode("\r\n", $item['settings']['method_suburbs']['value']);
					$item['suburbs'] = $suburbs;
				}
				$newdata[] = $item;
			}
			$result->set_data($newdata);
		}
		return $result;
	}
	
	function add_custom_fields() {
		register_rest_field(
			'shop_order',
			'delivery_date',
			array(
			    'get_callback'    => [$this, 'get_delivery_date'],
			    'update_callback' => null,
			    'schema'          => null,
			     )
		);
		register_rest_field(
			'shop_order',
			'delivery_timeslot',
			array(
			    'get_callback'    => [$this, 'get_delivery_timeslot'],
			    'update_callback' => null,
			    'schema'          => null,
			     )
		);
		register_rest_field(
			'shop_order',
			'shipping_notes',
			array(
			    'get_callback'    => [$this, 'get_shipping_notes'],
			    'update_callback' => null,
			    'schema'          => null,
			     )
		);
		register_rest_field(
			'shop_order',
			'card_message',
			array(
			    'get_callback'    => [$this, 'get_card_message'],
			    'update_callback' => null,
			    'schema'          => null,
			     )
		);
	}
	
	function get_delivery_date( $object, $field_name, $request ) {
		$order = wc_get_order($object['id']);
		$customfieldvalue = $order->get_meta('_delivery_date', true);
		$customfieldvalue = $customfieldvalue ? $customfieldvalue : null;
		return $customfieldvalue;
	}
	
	function get_delivery_timeslot( $object, $field_name, $request ) {
		$order = wc_get_order($object['id']);
		$customfieldvalue = $order->get_meta('_delivery_timeslot', true);
		$customfieldvalue = $customfieldvalue ? $customfieldvalue : null;
		return $customfieldvalue;
	}
	
	function get_shipping_notes( $object, $field_name, $request ) {
		$order = wc_get_order($object['id']);
		$customfieldvalue = $order->get_meta('_shipping_notes', true);
		$customfieldvalue = $customfieldvalue ? $customfieldvalue : null;
		return $customfieldvalue;
	}
	
	function get_card_message( $object, $field_name, $request ) {
		$order = wc_get_order($object['id']);
		$customfieldvalue = $order->get_meta('_card_message', true);
		$customfieldvalue = $customfieldvalue ? $customfieldvalue : null;
		return $customfieldvalue;
	}
	
}