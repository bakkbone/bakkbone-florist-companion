<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Order_Status
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BKF_Order_Status{
  
	public function __construct() {
		add_action( 'init', [$this, 'bkf_register_order_status'], 10 );
		add_filter( 'wc_order_statuses', array( $this, 'bkf_add_status') );
		add_filter( 'woocommerce_reports_order_statuses', [$this, 'bkf_include_status_to_reports'], 10 );
		add_filter( 'woocommerce_order_is_paid_statuses', [$this, 'bkf_status_paid'], 10 );
		add_filter( 'bulk_actions-edit-shop_order', [$this, 'bkf_status_bulk_actions'], PHP_INT_MAX );
		add_filter( 'bulk_actions-woocommerce_page_wc-orders', [$this, 'bkf_status_bulk_actions'], PHP_INT_MAX );
		add_action( 'admin_head', array( $this, 'bkf_status_css' ), 10 );
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'bkf_order_actions' ), 10, 2 );
		add_filter( 'wc_order_statuses', array( $this, 'bkf_rename_order_status_msg'), 20 );
		add_filter( "views_edit-shop_order", array( $this, 'bkf_order_status_top_changed'), PHP_INT_MAX);
		add_filter( "views_woocommerce_page_wc-orders", array( $this, 'bkf_order_status_top_changed'), PHP_INT_MAX);
		add_filter( 'woocommerce_menu_order_count', [$this, 'bkf_woocommerce_menu_order_count'], 10, 1 );
	}

	function bkf_woocommerce_menu_order_count( $ordercount ) {
		return bkf_all_count();
	}

	public function bkf_register_order_status(){
		register_post_status('wc-phone-draft', array(
			'label' => __('Draft (Phone)', 'bakkbone-florist-companion'),
			'public' => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list' => true,
			'exclude_from_search' => false,
			'label_count' => _n_noop('Draft (Phone) <span class="count">(%s)</span>', 'Draft (Phone) <span class="count">(%s)</span>', 'bakkbone-florist-companion')
		));
		register_post_status('wc-made', array(
			'label' => __('Prepared', 'bakkbone-florist-companion'),
			'public' => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list' => true,
			'exclude_from_search' => false,
			'label_count' => _n_noop('Prepared <span class="count">(%s)</span>', 'Prepared <span class="count">(%s)</span>', 'bakkbone-florist-companion')
		));
		register_post_status('wc-collect', array(
			'label' => __('Ready for Collection', 'bakkbone-florist-companion'),
			'public' => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list' => true,
			'exclude_from_search' => false,
			'label_count' => _n_noop('Ready for Collection <span class="count">(%s)</span>', 'Ready for Collection <span class="count">(%s)</span>', 'bakkbone-florist-companion')
		));
		register_post_status('wc-out', array(
			'label' => __('Out for Delivery', 'bakkbone-florist-companion'),
			'public' => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list' => true,
			'exclude_from_search' => false,
			'label_count' => _n_noop('Out for Delivery <span class="count">(%s)</span>', 'Out for Delivery <span class="count">(%s)</span>', 'bakkbone-florist-companion')
		));
		register_post_status('wc-relay', array(
			'label' => __('Relayed', 'bakkbone-florist-companion'),
			'public' => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list' => true,
			'exclude_from_search' => false,
			'label_count' => _n_noop('Relayed <span class="count">(%s)</span>', 'Relayed <span class="count">(%s)</span>', 'bakkbone-florist-companion')
		));
		register_post_status('wc-scheduled', array(
			'label' => __('Scheduled', 'bakkbone-florist-companion'),
			'public' => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list' => true,
			'exclude_from_search' => false,
			'label_count' => _n_noop('Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>', 'bakkbone-florist-companion')
		));
		register_post_status('wc-invoiced', array(
			'label' => __('Invoiced, Awaiting Payment', 'bakkbone-florist-companion'),
			'public' => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list' => true,
			'exclude_from_search' => false,
			'label_count' => _n_noop('Invoiced <span class="count">(%s)</span>', 'Invoiced <span class="count">(%s)</span>', 'bakkbone-florist-companion')
		));
		$bkfoptions = get_option("bkf_features_setting");
		if($bkfoptions["petals_on"]) {
			register_post_status('wc-new', array(
				'label' => __('New (Petals)', 'bakkbone-florist-companion'),
				'public' => true,
				'show_in_admin_status_list' => true,
				'show_in_admin_all_list' => true,
				'exclude_from_search' => false,
				'label_count' => _n_noop('New (Petals) <span class="count">(%s)</span>', 'New (Petals) <span class="count">(%s)</span>', 'bakkbone-florist-companion')
			));
			register_post_status('wc-accept', array(
				'label' => __('Accepted (Petals)', 'bakkbone-florist-companion'),
				'public' => true,
				'show_in_admin_status_list' => true,
				'show_in_admin_all_list' => true,
				'exclude_from_search' => false,
				'label_count' => _n_noop('Accepted (Petals) <span class="count">(%s)</span>', 'Accepted (Petals) <span class="count">(%s)</span>', 'bakkbone-florist-companion')
			));
			register_post_status('wc-reject', array(
				'label' => __('Rejected (Petals)', 'bakkbone-florist-companion'),
				'public' => true,
				'show_in_admin_status_list' => true,
				'show_in_admin_all_list' => true,
				'exclude_from_search' => false,
				'label_count' => _n_noop('Rejected (Petals) <span class="count">(%s)</span>', 'Rejected (Petals) <span class="count">(%s)</span>', 'bakkbone-florist-companion')
			));
		}
	}

	public function bkf_add_status( $order_statuses ){
		$new_order_statuses = [];
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;
			if ( 'wc-processing' === $key ) {
				$new_order_statuses['wc-made'] = _x('Prepared', 'Order status', 'bakkbone-florist-companion');
				$new_order_statuses['wc-collect'] = _x('Ready for Collection', 'Order status', 'bakkbone-florist-companion');
				$new_order_statuses['wc-out'] = _x('Out for Delivery', 'Order status', 'bakkbone-florist-companion');
				$new_order_statuses['wc-relay'] = _x('Relayed', 'Order status', 'bakkbone-florist-companion');
				$new_order_statuses['wc-scheduled'] = _x('Scheduled', 'Order status', 'bakkbone-florist-companion');
				$bkfoptions = get_option("bkf_features_setting");
				if($bkfoptions["petals_on"]) {
					$new_order_statuses['wc-new'] = _x('New (Petals)', 'Order status', 'bakkbone-florist-companion');
					$new_order_statuses['wc-accept'] = _x('Accepted (Petals)', 'Order status', 'bakkbone-florist-companion');
					$new_order_statuses['wc-reject'] = _x('Rejected (Petals)', 'Order status', 'bakkbone-florist-companion');
				}
				$new_order_statuses['wc-invoiced'] = _x( 'Invoiced, Awaiting Payment', 'Order status', 'bakkbone-florist-companion' );
				$new_order_statuses['wc-phone-draft'] = _x( 'Draft (Phone)', 'Order status', 'bakkbone-florist-companion' );
			}
		}
		return $new_order_statuses;
	}
  
	function bkf_include_status_to_reports($order_statuses){
		if ( is_array( $order_statuses ) && in_array( 'completed', $order_statuses, true ) ) {
			$bkfoptions = get_option("bkf_features_setting");
			if($bkfoptions["petals_on"]) {
				return array_merge( $order_statuses, array( 'made', 'collect', 'out', 'relay', 'scheduled', 'new', 'accept', 'reject', 'invoiced' ) );
			} else {
				return array_merge( $order_statuses, array( 'made', 'collect', 'out', 'relay', 'scheduled', 'invoiced' ) );
			}
		}
		return $order_statuses;
	}

	public function bkf_status_paid($statuses){
		$bkfoptions = get_option("bkf_features_setting");
		if($bkfoptions["petals_on"] == true) {
				return array_merge( $statuses, array_keys( array('made', 'collect', 'out', 'relay', 'scheduled', 'new', 'accept', 'reject') ) );
		} else {
				return array_merge( $statuses, array_keys( array('made', 'collect', 'out', 'relay', 'scheduled') ) );
		}
	}
   
	function bkf_status_bulk_actions($actions){
		unset($actions['mark_completed']);
		unset($actions['mark_processing']);
		unset($actions['mark_on-hold']);
		unset($actions['mark_cancelled']);
		unset($actions['trash']);
		$bulk_actions = [];
		$bulk_actions['mark_processing'] = __( 'Mark as received', 'bakkbone-florist-companion' );
		$bulk_actions['mark_scheduled'] = __( 'Mark as scheduled', 'bakkbone-florist-companion' );
		$bulk_actions['mark_made'] = __( 'Mark as prepared', 'bakkbone-florist-companion' );
		$bulk_actions['mark_collect'] = __( 'Mark as ready for collection', 'bakkbone-florist-companion' );
		$bulk_actions['mark_out'] = __( 'Mark as out for delivery', 'bakkbone-florist-companion' );
		$bulk_actions['mark_completed'] = __( 'Mark as delivered', 'bakkbone-florist-companion' );
		$bulk_actions['mark_relay'] = __( 'Mark as relayed', 'bakkbone-florist-companion' );
		$bulk_actions['mark_on-hold'] = __( 'Mark as on hold', 'bakkbone-florist-companion' );
		$bulk_actions['mark_cancelled'] = __( 'Mark as cancelled', 'bakkbone-florist-companion' );
		$bulk_actions['trash'] = __( 'Move to Trash', 'bakkbone-florist-companion' );
		return array_merge($bulk_actions, $actions);		
	}
	
	function bkf_order_actions( $actions, $order ) {
		unset($actions['complete']);
		$default_actions = [];
		$order_id = $order->get_id();
		if ( $order->has_status( array( 'processing', 'accept' ) ) ) {
			$actions['scheduled'] = array(
				'url'	   => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=scheduled&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'	  => __( 'Scheduled', 'bakkbone-florist-companion' ),
				'action'	=> 'view scheduled',
				);
		}
		if ( $order->has_status( array( 'processing', 'accept', 'scheduled' ) ) ) {
			$actions['made'] = array(
				'url'	   => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=made&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'	  => __( 'Prepared', 'bakkbone-florist-companion' ),
				'action'	=> 'view made',
				);
		}
		if ( $order->has_status( array( 'processing', 'accept', 'scheduled', 'made' ) ) ) {
			$actions['collect'] = array(
				'url'	   => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=collect&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'	  => __( 'Ready for Collection', 'bakkbone-florist-companion' ),
				'action'	=> 'view collect',
				);
		}
		if ( $order->has_status( array( 'processing', 'accept', 'scheduled' ) ) ) {
			$actions['relay'] = array(
				'url'	   => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=relay&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'	  => __( 'Relayed', 'bakkbone-florist-companion' ),
				'action'	=> 'view relay',
				);
		}
		if ( $order->has_status( array( 'processing', 'accept', 'scheduled', 'made' ) ) ) {
			$actions['out'] = array(
				'url'	   => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=out&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'	  => __( 'Out for Delivery', 'bakkbone-florist-companion' ),
				'action'	=> 'view out',
				);
		}
		if ( $order->has_status( array( 'processing', 'accept', 'made', 'scheduled', 'collect', 'out' ) ) ) {
			$default_actions['complete'] = array(
				'url'		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'   	=> __( 'Delivered', 'bakkbone-florist-companion' ),
				'action' 	=> 'view complete'
				);
		}
		if ( $order->has_status( array( 'invoiced', 'phone-draft', 'pending', 'on-hold' ) ) ) {
			$default_actions['processing'] = array(
				'url'		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processing&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'   	=> __( 'Mark as Paid', 'bakkbone-florist-companion' ),
				'action' 	=> 'view processing'
				);
		}
		
		$actions = array_merge( $actions, $default_actions );
		return $actions;
	}
	
	public function bkf_status_css() {
		$bkfoptions = get_option("bkf_features_setting");
		if($bkfoptions["petals_on"]) {
		echo '<style id="bkf_order_status">
			mark.processing::after {
				content: "\\f18e";
				color: #000000;
			}
			.view.processing::after {
				font-family: dashicons !important;
				color: #000000 !important;
				content: "\\f18e" !important;
			}
			mark.order-status.status-scheduled {
				color: #ffffff;
				background-color: #000000;
			}
			mark.scheduled::after {
				content: "\\e00e";
				color: #000000;
			}
			.view.scheduled::after {
				font-family: WooCommerce !important;
				color: #000000 !important;
				content: "\\e00e" !important;
			}
			mark.order-status.status-made {
				color: #ffffff;
				background-color: #003300;
			}
			mark.made::after {
				content: "\\e034";
				color: #003300;
			}
			.view.made::after {
				font-family: WooCommerce !important;
				color: #003300 !important;
				content: "\\e034" !important;
			}
			mark.order-status.status-collect {
				color: #ffffff;
				background-color: #000099;
			}
			mark.collect::after {
				content: "\\e034";
				color: #000099;
			}
			.view.collect::after {
				font-family: WooCommerce !important;
				color: #000099 !important;
				content: "\\e03a" !important;
			}
			mark.order-status.status-out {
				color: #ffffff;
				background-color: #006600;
			}
			mark.out::after {
				content: "\\e019";
				color: #006600;
			}
			.view.out::after {
				font-family: WooCommerce !important;
				color: #006600 !important;
				content: "\\e019" !important;
			}
			mark.order-status.status-relay {
				color: #000000;
				background-color: #a38d00;
			}
			mark.relay::after {
				content: "\\e030";
				color: #a38d00;
			}
			.view.relay::after {
				font-family: WooCommerce !important;
				color: #a38d00 !important;
				content: "\\e030" !important;
			}
			mark.complete::after {
				content: "\\e015";
				color: #000000;
			}
			.view.complete::after,a.complete::after {
				font-family: WooCommerce !important;
				color: #000000 !important;
				content: "\\e015" !important;
			}
			mark.order-status.status-new {
				color: #ffffff;
				background-color: #ec008c;
			}
			mark.new::after {
				content: "\\e00e";
				color: #ec008c;
			}
			.view.new::after {
				font-family: WooCommerce !important;
				color: #ec008c !important;
				content: "\\e016" !important;
			}
			mark.order-status.status-accept {
				color: #5b841b;
				background-color: #c6e1c6;
			}
			mark.accept::after {
				content: "\\e011";
				color: #c6e1c6;
			}
			.view.accept::after {
				font-family: WooCommerce !important;
				color: #c6e1c6 !important;
				content: "\\e011" !important;
			}
			mark.order-status.status-reject {
				color: #ffffff;
				background-color: #990000;
			}
			mark.reject::after {
				content: "\\e013";
				color: #990000;
			}
			.view.reject::after {
				font-family: WooCommerce !important;
				color: #990000 !important;
				content: "\\e013" !important;
			}
			mark.scheduled:after,mark.made:after,mark.out:after,mark.relay:after,mark.new:after,mark.accept:after,mark.reject:after {
				font-family:WooCommerce;
				speak:none;
				font-weight:400;
				font-variant:normal;
				text-transform:none;
				line-height:1;
				-webkit-font-smoothing:antialiased;
				margin:0;
				text-indent:0;
				position:absolute;
				top:0;
				left:0;
				width:100%;
				height:100%;
				text-align:center;
			}
			</style>
		';
		} else {
		echo '<style id="bkf_order_status">
			mark.processing::after {
				content: "\\f18e";
				color: #000000;
			}
			.view.processing::after {
				font-family: dashicons !important;
				color: #000000 !important;
				content: "\\f18e" !important;
			}
			mark.order-status.status-scheduled {
				color: #ffffff;
				background-color: #000000;
			}
			mark.scheduled::after {
				content: "\\e00e";
				color: #000000;
			}
			.view.scheduled::after {
				font-family: WooCommerce !important;
				color: #000000 !important;
				content: "\\e00e" !important;
			}
			mark.order-status.status-made {
				color: #ffffff;
				background-color: #003300;
			}
			mark.made::after {
				content: "\\e034";
				color: #003300;
			}
			.view.made::after {
				font-family: WooCommerce !important;
				color: #003300 !important;
				content: "\\e034" !important;
			}
			mark.order-status.status-collect {
				color: #ffffff;
				background-color: #000099;
			}
			mark.collect::after {
				content: "\\e034";
				color: #000099;
			}
			.view.collect::after {
				font-family: WooCommerce !important;
				color: #000099 !important;
				content: "\\e03a" !important;
			}
			mark.order-status.status-out {
				color: #ffffff;
				background-color: #006600;
			}
			mark.out::after {
				content: "\\e019";
				color: #006600;
			}
			.view.out::after {
				font-family: WooCommerce !important;
				color: #006600 !important;
				content: "\\e019" !important;
			}
			mark.order-status.status-relay {
				color: #000000;
				background-color: #a38d00;
			}
			mark.relay::after {
				content: "\\e030";
				color: #a38d00;
			}
			.view.relay::after {
				font-family: WooCommerce !important;
				color: #a38d00 !important;
				content: "\\e030" !important;
			}
			mark.complete::after {
				content: "\\e015";
				color: #000000;
			}
			.view.complete::after,a.complete::after {
				font-family: WooCommerce !important;
				color: #000000 !important;
				content: "\\e015" !important;
			}
			mark.scheduled:after,mark.made:after,mark.out:after,mark.relay:after {
				font-family:WooCommerce;
				speak:none;
				font-weight:400;
				font-variant:normal;
				text-transform:none;
				line-height:1;
				-webkit-font-smoothing:antialiased;
				margin:0;
				text-indent:0;
				position:absolute;
				top:0;
				left:0;
				width:100%;
				height:100%;
				text-align:center;
			}
			</style>
		';
		}
	}

	public function bkf_rename_order_status_msg( $order_statuses ) {
		$order_statuses['wc-completed']  = _x( 'Delivered', 'Order status', 'bakkbone-florist-companion' );
		$order_statuses['wc-processing']  = _x( 'Received', 'Order status', 'bakkbone-florist-companion' );
		$order_statuses['wc-failed']  = _x( 'Payment Unsuccessful', 'Order status', 'bakkbone-florist-companion' );
		return $order_statuses;
	}

	public function bkf_order_status_top_changed( $views ){
		if( isset( $views['wc-completed'] ) ){
			$views['wc-completed'] = str_replace( 'Completed', __( 'Delivered', 'bakkbone-florist-companion'), $views['wc-completed'] );
		}
		if( isset( $views['wc-processing'] ) ){
			$views['wc-processing'] = str_replace( 'Processing', __( 'Received', 'bakkbone-florist-companion'), $views['wc-processing'] );
		}
		if( isset( $views['wc-failed'] ) ){
			$views['wc-failed'] = str_replace( 'Failed', __( 'Payment Unsuccessful', 'bakkbone-florist-companion'), $views['wc-failed'] );
		}
		return $views;
	}
}