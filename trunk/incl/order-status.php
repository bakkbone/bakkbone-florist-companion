<?php

/**
 * @author BAKKBONE Australia
 * @package BkfOrderStatus
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

/**
 * BkfOrderStatus
**/
class BkfOrderStatus{
  
  public function __construct() {
    add_action( 'init', array($this, 'bkf_register_order_status'), 10, 2 );
    add_filter( 'wc_order_statuses', array( $this, 'bkf_add_status'), 10, 2 );
    add_filter( 'woocommerce_reports_order_statuses', array($this, 'bkf_include_status_to_reports'), 10, 2 );
    add_filter( 'woocommerce_order_is_paid_statuses', array($this, 'bkf_status_paid'), 10, 2 );
    add_filter( 'bulk_actions-edit-shop_order', array($this, 'bkf_status_bulk_actions'), 10, 2 );
    add_filter( 'bulk_actions-woocommerce_page_wc-orders', array($this, 'bkf_status_bulk_actions'), 10, 2 );
    add_action( 'admin_head', array( $this, 'bkf_status_css' ), 10, 2 );
    add_filter( 'woocommerce_admin_order_actions', array( $this, 'bkf_order_actions' ), 10, 2 );
    add_filter( 'wc_order_statuses', array( $this, 'bkf_rename_order_status_msg'), 20, 1 );
    foreach( array( 'post', 'shop_order' ) as $hook )
    add_filter( "views_edit-shop_order", array( $this, 'bkf_order_status_top_changed') );
    add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bkf_bulk_actions_order_status'), 20, 1 );
  }

  public function bkf_register_order_status()
  {
    register_post_status('wc-made', array(
      'label' => __('Prepared', 'woocommerce'),
      'public' => true,
      'show_in_admin_status_list' => true,
      'show_in_admin_all_list' => true,
      'exclude_from_search' => false,
      'label_count' => _n_noop('Prepared <span class="count">(%s)</span>', 'Prepared <span class="count">(%s)</span>', 'woocommerce')
    ));
    register_post_status('wc-out', array(
      'label' => __('Out for Delivery', 'woocommerce'),
      'public' => true,
      'show_in_admin_status_list' => true,
      'show_in_admin_all_list' => true,
      'exclude_from_search' => false,
      'label_count' => _n_noop('Out for Delivery <span class="count">(%s)</span>', 'Out for Delivery <span class="count">(%s)</span>', 'woocommerce')
    ));
    register_post_status('wc-relay', array(
      'label' => __('Relayed', 'woocommerce'),
      'public' => true,
      'show_in_admin_status_list' => true,
      'show_in_admin_all_list' => true,
      'exclude_from_search' => false,
      'label_count' => _n_noop('Relayed <span class="count">(%s)</span>', 'Relayed <span class="count">(%s)</span>', 'woocommerce')
    ));
    register_post_status('wc-scheduled', array(
      'label' => __('Scheduled', 'woocommerce'),
      'public' => true,
      'show_in_admin_status_list' => true,
      'show_in_admin_all_list' => true,
      'exclude_from_search' => false,
      'label_count' => _n_noop('Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>', 'woocommerce')
    ));
    $bkfoptions = get_option("bkf_options_setting");
    if($bkfoptions["bkf_petals"] == "1") {
    register_post_status('wc-new', array(
      'label' => __('New Petals Order', 'woocommerce'),
      'public' => true,
      'show_in_admin_status_list' => true,
      'show_in_admin_all_list' => true,
      'exclude_from_search' => false,
      'label_count' => _n_noop('New Petals Order <span class="count">(%s)</span>', 'New Petals Order <span class="count">(%s)</span>', 'woocommerce')
    ));
    register_post_status('wc-accept', array(
      'label' => __('Accepted (Petals)', 'woocommerce'),
      'public' => true,
      'show_in_admin_status_list' => true,
      'show_in_admin_all_list' => true,
      'exclude_from_search' => false,
      'label_count' => _n_noop('Accepted (Petals) <span class="count">(%s)</span>', 'Accepted (Petals) <span class="count">(%s)</span>', 'woocommerce')
    ));
    register_post_status('wc-reject', array(
      'label' => __('Rejected (Petals)', 'woocommerce'),
      'public' => true,
      'show_in_admin_status_list' => true,
      'show_in_admin_all_list' => true,
      'exclude_from_search' => false,
      'label_count' => _n_noop('Rejected (Petals) <span class="count">(%s)</span>', 'Rejected (Petals) <span class="count">(%s)</span>', 'woocommerce')
    ));
    }
  }

  public function bkf_add_status( array $statuses )
  {
    $bkf_order_statuses = array();
        $bkf_order_statuses['wc-made'] = __('Prepared', 'woocommerce');
        $bkf_order_statuses['wc-out'] = __('Out for Delivery', 'woocommerce');
        $bkf_order_statuses['wc-relay'] = __('Relayed', 'woocommerce');
        $bkf_order_statuses['wc-scheduled'] = __('Scheduled', 'woocommerce');
    $bkfoptions = get_option("bkf_options_setting");
    if($bkfoptions["bkf_petals"] == "1") {
        $bkf_order_statuses['wc-new'] = __('New Petals Order', 'woocommerce');
        $bkf_order_statuses['wc-accept'] = __('Accepted (Petals)', 'woocommerce');
        $bkf_order_statuses['wc-reject'] = __('Rejected (Petals)', 'woocommerce');
      }
      $statuses = array_merge( $statuses, $bkf_order_statuses );
      return $statuses;
    }
  
    public function bkf_include_status_to_reports($order_statuses)
    {
        if ( is_array( $order_statuses ) && in_array( 'completed', $order_statuses, true ) ) {
            $bkfoptions = get_option("bkf_options_setting");
            if($bkfoptions["bkf_petals"] == "1") {
                return array_merge( $order_statuses, array_keys( 'made', 'out', 'relay', 'scheduled', 'new', 'accept', 'reject' ) );
            }else{
                return array_merge( $order_statuses, array_keys( 'made', 'out', 'relay', 'scheduled' ) );
            }
        }
        return $order_statuses;
    }

    public function bkf_status_paid($statuses)
    {
    $bkfoptions = get_option("bkf_options_setting");
    if($bkfoptions["bkf_petals"] == "1") {
			return array_merge( $statuses, array_keys( 'made', 'out', 'relay', 'scheduled', 'new', 'accept', 'reject' ) );
    }else{
			return array_merge( $statuses, array_keys( 'made', 'out', 'relay', 'scheduled' ) );
    }
    }
   
    public function bkf_status_bulk_actions($bulk_actions){
      $bulk_actions['mark_scheduled'] = __( 'Change status to scheduled', 'woocommerce' );		
      $bulk_actions['mark_made'] = __( 'Change status to prepared', 'woocommerce' );		
      $bulk_actions['mark_out'] = __( 'Change status to out for delivery', 'woocommerce' );		
      $bulk_actions['mark_relay'] = __( 'Change status to relayed', 'woocommerce' );		
      return $bulk_actions;		
    }
    
    public function bkf_order_actions( $actions, $order ) {
        $default_actions = array();
        $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
        if ( $order->has_status( array( 'processing', 'accept' ) ) ) {
            $actions['scheduled'] = array(
                'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=scheduled&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
                'name'      => __( 'Scheduled', 'woocommerce' ),
                'action'    => 'view scheduled',
                );
        }
        if ( $order->has_status( array( 'processing', 'accept', 'scheduled' ) ) ) {
            $actions['made'] = array(
                'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=made&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
                'name'      => __( 'Prepared', 'woocommerce' ),
                'action'    => 'view made',
                );
        }
        if ( $order->has_status( array( 'processing', 'accept', 'scheduled', 'made' ) ) ) {
            $actions['out'] = array(
                'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=out&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
                'name'      => __( 'Out for Delivery', 'woocommerce' ),
                'action'    => 'view out',
                );
        }
        if ( $order->has_status( array( 'processing', 'accept', 'made', 'scheduled', 'out' ) ) ) {
			$default_actions['complete'] = array(
			    'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'   => __( 'Delivered', 'woocommerce' ),
				'action' => 'view complete'
				);
        }
        
        $actions = array_merge( $default_actions, $actions );
        return $actions;
    }
    
    public function bkf_status_css() {
    $bkfoptions = get_option("bkf_options_setting");
		echo '<style id="bkf_order_status">
			mark.order-status.status-scheduled {
				color: #ffffff;
				background-color: #000000;
			}
			mark.scheduled::after {
				content: "\\e028";
				color: #000000;
			}
			.view.scheduled::after {
			    font-family: WooCommerce !important;
			    color: #000000 !important;
			    content: "\\e028" !important;
			}
			mark.order-status.status-made {
				color: #000000;
				background-color: #7bdb7b;
			}
			mark.made::after {
				content: "\\e006";
				color: #7bdb7b;
			}
			.view.made::after {
			    font-family: WooCommerce !important;
			    color: #7bdb7b !important;
			    content: "\\e006" !important;
			}
			mark.order-status.status-out {
				color: #ffffff;
				background-color: #006600;
			}
			mark.out::after {
				content: "\\e01a";
				color: #006600;
			}
			.view.out::after {
			    font-family: WooCommerce !important;
			    color: #006600 !important;
			    content: "\\e01a" !important;
			}
			mark.order-status.status-relay {
				color: #000000;
				background-color: #a38d00;
			}
			mark.relay::after {
				content: "\\e029";
				color: #a38d00;
			}
			.view.relay::after {
			    font-family: WooCommerce !important;
			    color: #a38d00 !important;
			    content: "\\e029" !important;
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

    public function bkf_rename_order_status_msg( $order_statuses ) {
    $order_statuses['wc-completed']  = _x( 'Delivered', 'Order status', 'woocommerce' );
    return $order_statuses;
    }

    public function bkf_order_status_top_changed( $views ){
    if( isset( $views['wc-completed'] ) )
        $views['wc-completed'] = str_replace( 'Completed', __( 'Delivered', 'woocommerce'), $views['wc-completed'] );
    }
    
    public function bkf_bulk_actions_order_status( $actions ) {
    $actions['mark_completed']  = __( 'Change status to delivered', 'woocommerce' );
    return $actions;
    }
    
  }