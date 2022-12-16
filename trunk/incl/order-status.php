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
    add_filter( 'wc_order_statuses', array($this, 'bkf_add_delivered_to_order_statuses') );
    add_filter( 'woocommerce_reports_order_statuses', array($this, 'include_custom_order_status_to_reports'), 20, 1);
    add_filter( 'woocommerce_order_is_paid_statuses', array($this, 'delivered_woocommerce_order_is_paid_statuses') );
    add_action( 'admin_footer', array($this, 'bkf_bulk_admin_footer'), 11 );
    add_filter( 'bulk_actions-edit-shop_order', array($this, 'add_bulk_actions_change_order_status'), 50, 1 );
    add_filter( 'woocommerce_email_classes', array($this, 'register_email'), 90, 1 );
  }

  function bkf_register_order_status()
  {
    register_post_status('wc-delivered', array(
      'label' => __('TEST', 'bakkbone-florist-companion'),
      'public' => true,
      'show_in_admin_status_list' => true,
      'show_in_admin_all_list' => true,
      'exclude_from_search' => false,
      'label_count' => _n_noop('TEST <span class="count">(%s)</span>', 'TEST <span class="count">(%s)</span>', 'bakkbone-florist-companion')
    ));
  }

  function bkf_add_delivered_to_order_statuses($order_statuses)
  {
    $new_order_statuses = array();
    foreach ($order_statuses as $key => $status) {
      $new_order_statuses[$key] = $status;
      if ('wc-completed' === $key) {
        $new_order_statuses['wc-delivered'] = __('TEST', 'bakkbone-florist-companion');
      }
      return $new_order_statuses;
    }
  }

  function include_custom_order_status_to_reports($statuses)
  {
      if ($statuses)
        $statuses[] = 'delivered';
      return $statuses;
    }

    function delivered_woocommerce_order_is_paid_statuses($statuses)
    {
      $statuses[] = 'delivered';
      return $statuses;
    }
    
    function add_bulk_actions_change_order_status($bulk_actions){
      $bulk_actions['mark_delivered'] = __( 'Change status to TEST', 'woocommerce' );		
      return $bulk_actions;		
    }
    
    function register_email( $emails ) {
      define( 'DELIVERED_WC_EMAIL_PATH', get_stylesheet_directory() );
      require_once 'emails/class-wc-delivered-status-order.php';
      $emails['WC_Delivered_status_Order'] = new WC_Delivered_status_Order();
      return $emails;
    }
  
  }
