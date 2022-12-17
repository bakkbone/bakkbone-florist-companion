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
  
  function __construct() {
    add_action( 'init', array($this, 'bkf_register_order_status') );
    add_filter( 'wc_order_statuses', array( $this, 'bkf_add_status') );
    add_filter( 'wc_order_statuses', array( $this, 'bkf_status_reg') );
    add_filter( 'woocommerce_reports_order_statuses', array($this, 'bkf_include_status_to_reports') );
    add_filter( 'woocommerce_order_is_paid_statuses', array($this, 'bkf_status_paid') );
    add_filter( 'bulk_actions-edit-shop_order', array($this, 'bkf_status_bulk_actions'), 10, 1 );
    add_filter( 'bulk_actions-woocommerce_page_wc-orders', array($this, 'bkf_status_bulk_actions'), 10, 1 );
    add_filter( 'wc_order_statuses', array( $this, 'bkf_add_status_to_filter' ) );
    add_action( 'admin_head', array( $this, 'bkf_status_css' ) );
    add_filter( 'woocommerce_admin_order_preview_actions', array( $this, 'bkf_add_custom_status_to_order_preview' ), PHP_INT_MAX, 2 );

  }

  function bkf_register_order_status()
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
  }


  function bkf_add_status($order_statuses)
  {
    $new_order_statuses = array();
    foreach ($order_statuses as $key => $status) {
      $new_order_statuses[$key] = $status;
      if ('wc-completed' === $key) {
        $new_order_statuses['wc-made'] = __('Prepared', 'woocommerce');
        $new_order_statuses['wc-out'] = __('Out for Delivery', 'woocommerce');
        $new_order_statuses['wc-relay'] = __('Relayed', 'woocommerce');
        $new_order_statuses['wc-scheduled'] = __('Scheduled', 'woocommerce');
      }
      return $new_order_statuses;
    }
  }
  
  function bkf_status_reg( array $statuses ){
      		$statuses[ 'wc-made' ] = _x( 'Prepared', 'Order status', 'woocommerce' );
      		$statuses[ 'wc-out' ] = _x( 'Out for Delivery', 'Order status', 'woocommerce' );
      		$statuses[ 'wc-relay' ] = _x( 'Relayed', 'Order status', 'woocommerce' );
      		$statuses[ 'wc-scheduled' ] = _x( 'Scheduled', 'Order status', 'woocommerce' );
      		return $statuses;
  }

  function bkf_include_status_to_reports($order_statuses)
  {
			if ( is_array( $order_statuses ) && in_array( 'completed', $order_statuses, true ) ) {
				return array_merge( $order_statuses, array_keys( 'made', 'out', 'relay', 'scheduled' ) );
			}
			return $order_statuses;
    }

    function bkf_status_paid($statuses)
    {
			return array_merge( $statuses, array_keys( 'made', 'out', 'relay', 'scheduled' ) );
    }
   
    function bkf_status_bulk_actions($bulk_actions){
      $bulk_actions['mark_made'] = __( 'Change status to prepared', 'woocommerce' );		
      $bulk_actions['mark_out'] = __( 'Change status to out for delivery', 'woocommerce' );		
      $bulk_actions['mark_relay'] = __( 'Change status to relayed', 'woocommerce' );		
      $bulk_actions['mark_scheduled'] = __( 'Change status to scheduled', 'woocommerce' );		
      return $bulk_actions;		
    }
    
    function bkf_add_status_to_filter( $order_statuses ) {
			return array_merge( ( '' === $order_statuses ? array() : $order_statuses ), array('Prepared','Out for Delivery','Relayed','Scheduled') );
		}
    
function bkf_status_css() {
		echo '
		<style id="bkf_order_status">
			mark.order-status.status-scheduled {
				color: #ffffff;
				background-color: #000000;
			}
			mark.scheduled::after {
				content: "\e028";
				color: #000000;
			}
			mark.order-status.status-made {
				color: #000000;
				background-color: #7bdb7b;
			}
			mark.made::after {
				content: "\e006";
				color: #7bdb7b;
			}
			mark.order-status.status-out {
				color: #ffffff;
				background-color: #006600;
			}
			mark.out::after {
				content: "\e01a";
				color: #006600;
			}
			mark.order-status.status-relay {
				color: #000000;
				background-color: #a38d00;
			}
			mark.relay::after {
				content: "\e029";
				color: #a38d00;
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

    
    function bkf_add_custom_status_to_order_preview( $actions, $_order ) {
			$status_actions  = array();
			$_status_actions = $this->get_custom_order_statuses_actions( $_order );
			if ( ! empty( $_status_actions ) ) {
				$order_id = ( version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' ) ? $_order->id : $_order->get_id() );
					$status_actions[ 'wc-scheduled' ] = array(
						'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=scheduled&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
						'name'   => 'Scheduled',
						// translators: Custom Order status.
						'title'  => sprintf( __( 'Change order status to scheduled', 'custom-order-statuses-woocommerce' ), 'wc-scheduled' ),
						'action' => 'wc-scheduled',
					);
			}
			if ( $status_actions ) {
				if ( ! empty( $actions['status']['actions'] ) && is_array( $actions['status']['actions'] ) ) {
					$actions['status']['actions'] = array_merge( $actions['status']['actions'], $status_actions );
				} else {
					$actions['status'] = array(
						'group'   => __( 'Change status: ', 'woocommerce' ),
						'actions' => $status_actions,
					);
				}
			}
			return $actions;
		}
    
    
    
  }
