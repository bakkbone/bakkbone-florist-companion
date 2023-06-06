<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdCalendar
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfDdCalendar{
	
    function __construct(){
		add_action("admin_menu",array($this,"bkfAddCalendarPageOption"),3);
        add_filter('woocommerce_order_data_store_cpt_get_orders_query', array($this, 'bkf_handle_custom_query_var'), PHP_INT_MAX, 2 );
	}

	function bkfAddCalendarPageOption()
	{
		$admin_page = add_menu_page(
			__("Delivery Calendar","bakkbone-florist-companion"),
			__("Delivery Calendar","bakkbone-florist-companion"),
			"edit_shop_orders",
			"bkf_dc",
			array($this,"bkfCalendarPageContent"),
			'dashicons-calendar-alt',
			2.3
		);
		add_action( 'load-'.$admin_page, array($this, 'bkf_cal_help_tab') );
    }
	
	function bkf_cal_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_cal_help';
		$callback = array($this, 'bkf_cal_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_cal_help(){
		?>
		<h2><?php echo esc_html__('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/day-to-day/delivery-calendar/" target="_blank">https://plugins.bkbn.au/docs/bkf/day-to-day/delivery-calendar/</a>
		<?php
	}
	
	function bkfCalendarPageContent()
	{
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php  echo esc_html__("Delivery Calendar","bakkbone-florist-companion") ?></h1>
				<div class="inside">
    <script>

      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
			timeZone: "<?php echo wp_timezone_string(); ?>",
          initialView: 'dayGridMonth',
			navLinks: true,
			views: {
				dayGridMonth: {
					dayMaxEventRows: 4,
					dayHeaderFormat: {
						weekday: 'long'
					}
				},
				dayGridWeek: {
					dayHeaderFormat: {
						weekday: 'long',
						day: 'numeric',
						month: 'numeric',
						omitCommas: true
					}
				}
			},
		    customButtons: {
		       CSV: {
		         text: '<?php echo esc_html__("CSV", "bakkbone-florist-companion"); ?>',
		         click: function() {
					 var startYear = calendar.view.currentStart.getFullYear();
					 var startMonth = calendar.view.currentStart.getMonth() + 1;
					 var startDate = calendar.view.currentStart.getDate();
					 var endYear = calendar.view.currentEnd.getFullYear();
					 var endMonth = calendar.view.currentEnd.getMonth() + 1;
					 var endDate = calendar.view.currentEnd.getDate();
					 window.location.href = '<?php echo admin_url('admin-ajax.php?action=bkf_cal_csv&starty='); ?>' + startYear + '&startm=' + startMonth + '&startd=' + startDate + '&endy=' + endYear + '&endm=' + endMonth + '&endd=' + endDate;
		         }
		       },
		       PDF: {
		         text: '<?php echo esc_html__("PDF", "bakkbone-florist-companion"); ?>',
		         click: function() {
					 var startYear = calendar.view.currentStart.getFullYear();
					 var startMonth = calendar.view.currentStart.getMonth() + 1;
					 var startDate = calendar.view.currentStart.getDate();
					 var endYear = calendar.view.currentEnd.getFullYear();
					 var endMonth = calendar.view.currentEnd.getMonth() + 1;
					 var endDate = calendar.view.currentEnd.getDate();
					 window.location.href = '<?php echo admin_url('admin-ajax.php?action=bkf_cal_pdf&starty='); ?>' + startYear + '&startm=' + startMonth + '&startd=' + startDate + '&endy=' + endYear + '&endm=' + endMonth + '&endd=' + endDate;
		         }
		       }
			   
		     },
			headerToolbar: {
				start: 'dayGridMonth,listMonth dayGridWeek,listWeek dayGridDay,listDay',
				center: 'title',
				end: 'CSV,PDF today prev,next'
			},
			footerToolbar: {
				start: 'dayGridMonth,listMonth dayGridWeek,listWeek dayGridDay,listDay',
				center: 'CSV,PDF',
				end: 'today prev,next'
			},
			firstDay: 1,
			height: '80vh',
			events:[
				<?php
		$orders = get_posts(array(
			'post_type' => 'shop_order',
			'post_status' => array('new','accept','processing','completed','scheduled','prepared','collect','out','relayed'),
			'numberposts' => '-1'
		));
		$closed = get_option('bkf_dd_closed');
		$full = get_option('bkf_dd_full');
		$ct = __("Closed", "bakkbone-florist-companion");
		$gt = __("Closed (Global)", "bakkbone-florist-companion");
		$ft = __("Fully Booked", "bakkbone-florist-companion");
		
		foreach($orders as $order){
			$id = $order->ID;
			if(get_post_meta($id,'_delivery_timestamp',true) !== ''){
				$url = get_edit_post_link($order);
				$date = wp_date("Y-m-d",get_post_meta($order->ID,'_delivery_timestamp',true));
				$recipient = str_replace('"', '\"', get_post_meta($order->ID, '_shipping_first_name',true).' '.get_post_meta($order->ID, '_shipping_last_name',true));
				$customer = str_replace('"', '\"', get_post_meta($order->ID, '_billing_first_name',true).' '.get_post_meta($order->ID, '_billing_last_name',true));
				$suburb = str_replace('"', '\"', get_post_meta($order->ID, '_shipping_city',true));
				$orderObj = new WC_Order($order->ID);
				if($orderObj->needs_shipping_address()){
					if(get_post_meta($id,'_delivery_timeslot_id',true) !== ''){
						$allts = bkf_get_timeslots_associative();
						$tsid = 'ts'.get_post_meta($id,'_delivery_timeslot_id',true);
						$ts = $allts[$tsid];
						$start = $ts['start'];
						$end = $ts['end'];
						echo '{ title: "#'.$order->ID.' - '. $recipient . ', ' . $suburb . '", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["shipped"] }, ';
					} elseif(get_post_meta($id,'_delivery_timeslot',true) !== '') {
						$timeslot = get_post_meta($id,'_delivery_timeslot',true);
						$array = explode(" - ",$timeslot);
						$start = date("H:i",strtotime($array[0]));
						$end = date("H:i",strtotime($array[1]));
						echo '{ title: "#'.$order->ID.' - '. $recipient . ', ' . $suburb . '", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["shipped"] }, ';
					} else {
						echo '{ title: "#'.$order->ID.' - '. $recipient . ', ' . $suburb . '", url: "'.html_entity_decode($url).'", start: "'.$date.'", classNames: ["shipped"] }, ';
					}
				} else {
					if(get_post_meta($id,'_delivery_timeslot_id',true) !== ''){
						$allts = bkf_get_timeslots_associative();
						$tsid = 'ts'.get_post_meta($id,'_delivery_timeslot_id',true);
						$ts = $allts[$tsid];
						$start = $ts['start'];
						$end = $ts['end'];
						echo '{ title: "#'.$order->ID.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$customer.'", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["unshipped"] }, ';
					} elseif(get_post_meta($id,'_delivery_timeslot',true) !== '') {
						$timeslot = get_post_meta($id,'_delivery_timeslot',true);
						$array = explode(" - ",$timeslot);
						$start = date("H:i",strtotime($array[0]));
						$end = date("H:i",strtotime($array[1]));
						echo '{ title: "#'.$order->ID.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$customer.'", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["unshipped"] }, ';
					} else {
						echo '{ title: "#'.$order->ID.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$customer.'", url: "'.html_entity_decode($url).'", start: "'.$date.'", classNames: ["unshipped"] }, ';
					}
				}
				
			}
		}
		if(null !== $closed){
			foreach($closed as $ts => $date){
				$string = wp_date("Y-m-d",$ts);
				echo '{ title: \''.$ct.'\', start: \'' . $string . '\', display: \'background\', className: \'closedbg\' }, ';
			}
		}
		if(null !== $full){
			foreach($full as $ts => $date){
				$string = wp_date("Y-m-d",$ts);
				echo '{ title: \''.$ft.'\', start: \''.$string.'\', display: \'background\', className: \'fullbg\' }, ';
			}
		}
		if(get_option('bkf_dd_setting')['monday'] == false){
			echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'1\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['tuesday'] == false){
			echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'2\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['wednesday'] == false){
			echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'3\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['thursday'] == false){
			echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'4\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['friday'] == false){
			echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'5\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['saturday'] == false){
			echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'6\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['sunday'] == false){
			echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'0\', display: \'background\', className: \'closedbg\' }, ';
		}
		?>
		]
        });
        calendar.render();
      });

    </script>
	  <div id='calendar'></div>
				</div>
			</div>
		</div>
		<?php
	}
	
    function bkf_handle_custom_query_var( $query, $query_vars ) {
    	if ( ! empty( $query_vars['_delivery_timestamp'] ) ) {
    		$query['meta_query'][] = array(
    			'key' => '_delivery_timestamp',
    			'value' => esc_attr( $query_vars['_delivery_timestamp'] ),
    		);
    	}
    	return $query;
    }
	
}