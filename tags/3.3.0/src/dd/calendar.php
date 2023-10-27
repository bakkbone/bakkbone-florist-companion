<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Calendar
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BKF_Delivery_Date_Calendar{
	
	function __construct(){
		add_action("admin_menu",array($this,"page"),3);
	}

	function page(){
		$admin_page = add_menu_page(
			__("Delivery Calendar","bakkbone-florist-companion"),
			__("Delivery Calendar","bakkbone-florist-companion"),
			"manage_woocommerce",
			"bkf_dc",
			array($this,"bkfCalendarPageContent"),
			'dashicons-calendar-alt',
			2.4
		);
		add_action( 'load-'.$admin_page, [$this, 'bkf_cal_help_tab'] );
	}
	
	function bkf_cal_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_cal_help';
		$callback = [$this, 'bkf_cal_help'];
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_cal_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/day-to-day/delivery-calendar/" target="_blank">https://docs.floristpress.org/day-to-day/delivery-calendar/</a>
		<?php
	}
	
	function bkfCalendarPageContent(){
	    $startyear = wp_date("Y");
	    $startmonth = wp_date("m") - 3;
	    $startmonth = substr("0{$startmonth}", -2);
	    $startday = "01";
	    $startdate = $startyear."-".$startmonth."-".$startday;
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php  esc_html_e("Delivery Calendar","bakkbone-florist-companion") ?></h1>
				<div class="inside">
	<script>
	  document.addEventListener('DOMContentLoaded', function() {
		var calendarEl = document.getElementById('calendar');
		var calendar = new FullCalendar.Calendar(calendarEl, {
			timeZone: "<?php echo wp_timezone_string(); ?>",
			initialView: 'dayGridMonth',
			navLinks: true,
			validRange: {
			    start: "<?php echo $startdate; ?>"
			},
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
				 text: '<?php esc_html_e("CSV", "bakkbone-florist-companion"); ?>',
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
				 text: '<?php esc_html_e("PDF", "bakkbone-florist-companion"); ?>',
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
			eventClick: function(info) {
				info.jsEvent.preventDefault();
				if (jQuery('#bkf_fullcalendar_modal').length) {
					jQuery('#bkf_fullcalendar_modal').remove();
				}
				if (info.event.url.length) {
					var modalContent = '<div id="bkf_fullcalendar_modal" title="' + info.event.title + '">' + info.event.extendedProps.content + '</div>';
					jQuery('body').append(modalContent);
					jQuery('#bkf_fullcalendar_modal').dialog({
						modal: true,
						minWidth: 350,
						draggable: false,
						resizable: false,
						position: {
							my: 'left top',
							at: 'left bottom',
							of: info.el
						},
						buttons: [
							{
								text: "<?php echo esc_html(sprintf(__('Print %s','bakkbone-florist-companion'),get_option('bkf_pdf_setting')['ws_title'])); ?>",
								icon: "ui-icon-print",
								click: function(){window.location.href = info.event.extendedProps.wsurl;}
							},
							{
								text: "<?php esc_html_e('View Order','bakkbone-florist-companion'); ?>",
								icon: "ui-icon-extlink",
								click: function(){window.open(info.event.url)}
							},
						]
					});
				}
			},
			firstDay: 1,
			height: '80vh',
			events:[
				<?php
		$oldest = strtotime($startdate);
		$orders = wc_get_orders(array(
			'status'   => array('wc-new','wc-accept','wc-processing','wc-completed','wc-scheduled','wc-prepared','wc-collect','wc-out','wc-relayed','wc-invoiced'),
			'limit'   => '-1',
			'meta_key'      => '_delivery_timestamp',
			'meta_value'    => $oldest,
			'meta_compare'  => '>=',
		));
		foreach($orders as $k => $o){
		    if($o->get_meta('_delivery_timestamp',true) < $oldest){
		        unset($orders[$k]);
		    }
		}
		$closed = get_option('bkf_dd_closed');
		$full = get_option('bkf_dd_full');
		$ct = __("Closed", "bakkbone-florist-companion");
		$gt = __("Closed (Global)", "bakkbone-florist-companion");
		$ft = __("Fully Booked", "bakkbone-florist-companion");
		
		foreach($orders as $order){
			$id = $order->get_id();
			if($order->get_meta('_delivery_timestamp',true) !== ''){
				$url = $order->get_edit_order_url();
				$ws = admin_url('admin-ajax.php?action=bkfdw&order_id='.$id.'&nonce='.wp_create_nonce('bkf'));
				$date = wp_date("Y-m-d",$order->get_meta('_delivery_timestamp',true));
				$recipient = str_replace('"', '\"', $order->get_meta('_shipping_first_name',true).' '.$order->get_meta('_shipping_last_name',true));
				$customer = str_replace('"', '\"', $order->get_meta('_billing_first_name',true).' '.$order->get_meta('_billing_last_name',true));
				$suburb = str_replace('"', '\"', $order->get_meta('_shipping_city',true));
				if($order->needs_shipping_address()){
					$address = $order->get_formatted_shipping_address().'<br/>'.esc_html($order->get_shipping_phone()).'<br/>'.esc_html($order->get_meta('_shipping_notes', true));
					if($order->get_meta('_delivery_timeslot_id',true) !== ''){
						$allts = bkf_get_timeslots_associative();
						$tsid = 'ts'.$order->get_meta('_delivery_timeslot_id',true);
						$ts = $allts[$tsid];
						if($ts){
							$start = $ts['start'];
							$end = $ts['end'];
							echo str_replace(["\r","\n"],"<br/>",'{ title: "#'.$order->ID.' - '. $recipient . ', ' . $suburb . '", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["shipped"], wsurl: "'.$ws.'", content: "'.bkf_get_timeslot_string($order->get_meta('_delivery_timeslot_id',true)).'<br/>'.$address.'" }, ');
						} else {
							$timeslot = $order->get_meta('_delivery_timeslot',true);
							$array = explode(" - ",$timeslot);
							$start = date("H:i",strtotime($array[0]));
							$end = date("H:i",strtotime($array[1]));
							echo str_replace(["\r","\n"],"<br/>",'{ title: "#'.$order->ID.' - '. $recipient . ', ' . $suburb . '", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["shipped"], wsurl: "'.$ws.'", content: "'.$timeslot.'<br/>'.$address.'" }, ');
						}
					} elseif($order->get_meta('_delivery_timeslot',true) !== '') {
						$timeslot = $order->get_meta('_delivery_timeslot',true);
						$array = explode(" - ",$timeslot);
						$start = date("H:i",strtotime($array[0]));
						$end = date("H:i",strtotime($array[1]));
						echo str_replace(["\r","\n"],"<br/>",'{ title: "#'.$order->ID.' - '. $recipient . ', ' . $suburb . '", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["shipped"], wsurl: "'.$ws.'", content: "'.$timeslot.'<br/>'.$address.'" }, ');
					} else {
						echo str_replace(["\r","\n"],"<br/>",'{ title: "#'.$order->ID.' - '. $recipient . ', ' . $suburb . '", url: "'.html_entity_decode($url).'", start: "'.$date.'", classNames: ["shipped"], wsurl: "'.$ws.'", content: "'.$address.'" }, ');
					}
				} else {
					if($order->get_meta('_delivery_timeslot_id',true) !== ''){
						$allts = bkf_get_timeslots_associative();
						$tsid = 'ts'.$order->get_meta('_delivery_timeslot_id',true);
						$ts = $allts[$tsid];
						if($ts){
							$start = $ts['start'];
							$end = $ts['end'];
							echo str_replace(["\r","\n"],"<br/>",'{ title: "#'.$order->ID.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$customer.'", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["unshipped"], wsurl: "'.$ws.'", content: "'.bkf_get_timeslot_string($order->get_meta('_delivery_timeslot_id',true)).'" }, ');
						} else {
							$timeslot = $order->get_meta('_delivery_timeslot',true);
							$array = explode(" - ",$timeslot);
							$start = date("H:i",strtotime($array[0]));
							$end = date("H:i",strtotime($array[1]));
							echo str_replace(["\r","\n"],"<br/>",'{ title: "#'.$order->ID.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$customer.'", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["unshipped"], wsurl: "'.$ws.'", content: "'.$timeslot.'" }, ');
						}
					} elseif($order->get_meta('_delivery_timeslot',true) !== '') {
						$timeslot = $order->get_meta('_delivery_timeslot',true);
						$array = explode(" - ",$timeslot);
						$start = date("H:i",strtotime($array[0]));
						$end = date("H:i",strtotime($array[1]));
						echo str_replace(["\r","\n"],"<br/>",'{ title: "#'.$order->ID.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$customer.'", url: "'.html_entity_decode($url).'", start: "'.$date.' '.$start.':00", end: "'.$date.' '.$end.':00", classNames: ["unshipped"], wsurl: "'.$ws.'", content: "'.$timeslot.'" }, ');
					} else {
						echo str_replace(["\r","\n"],"<br/>",'{ title: "#'.$order->ID.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$customer.'", url: "'.html_entity_decode($url).'", start: "'.$date.'", classNames: ["unshipped"], wsurl: "'.$ws.'", content: "'.$order->get_meta('_delivery_date',true).'" }, ');
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
	
}