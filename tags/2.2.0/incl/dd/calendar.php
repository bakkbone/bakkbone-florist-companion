<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdCalendar
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdCalendar{
	
    function __construct(){
		add_action("admin_menu",array($this,"bkfAddCalendarPageOption"),3);
        add_filter('woocommerce_order_data_store_cpt_get_orders_query', array($this, 'bkf_handle_custom_query_var'), PHP_INT_MAX, 2 );
		add_action('wp_ajax_bkf_cal_pdf', array($this, 'bkf_cal_pdf'));
		add_action('wp_ajax_bkf_cal_csv', array($this, 'bkf_cal_csv'));
	}

	function bkfAddCalendarPageOption()
	{
		add_menu_page(
			__("Delivery Calendar","bakkbone-florist-companion"), //$page_title
			__("Delivery Calendar","bakkbone-florist-companion"), //$menu_title
			"edit_shop_orders", //$capability
			"bkf_dc",//$menu_slug
			array($this,"bkfCalendarPageContent"),//$function
			'dashicons-calendar-alt',//icon
			2.3//position
		);
	}

	function bkfCalendarPageContent()
	{
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php  _e("Delivery Calendar","bakkbone-florist-companion") ?></h1>
				<div class="inside">
    <script>

      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
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
		         text: '<?php _e("CSV", "bakkbone-florist-companion"); ?>',
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
		         text: '<?php _e("PDF", "bakkbone-florist-companion"); ?>',
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
			$url = get_edit_post_link($order);
			$date = date("Y-m-d",get_post_meta($order->ID,'_delivery_timestamp',true));
			$recipient = get_post_meta($order->ID, '_shipping_first_name',true).' '.get_post_meta($order->ID, '_shipping_last_name',true);
			$suburb = get_post_meta($order->ID, '_shipping_city',true);
			echo '{ title: \'#'.$order->ID.' - '. $recipient . ', ' . $suburb . '\', url: \''.html_entity_decode($url).'\', start: \''.$date.'\' }, ';
		}
		if(null !== $closed){
			foreach($closed as $ts => $date){
				$string = date("Y-m-d",$ts);
				echo '{ title: \''.$ct.'\', start: \'' . $string . '\', display: \'background\', className: \'closedbg\' }, ';
			}
		}
		if(null !== $full){
			foreach($full as $ts => $date){
				$string = date("Y-m-d",$ts);
				echo '{ title: \''.$ft.'\', start: \''.$string.'\', display: \'background\', className: \'fullbg\' }, ';
			}
		}
		if(get_option('bkf_dd_setting')['monday'] == false){
			echo '{ title: \''.$gt.'\', daysOfWeek: \'1\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['tuesday'] == false){
			echo '{ title: \''.$gt.'\', daysOfWeek: \'2\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['wednesday'] == false){
			echo '{ title: \''.$gt.'\', daysOfWeek: \'3\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['thursday'] == false){
			echo '{ title: \''.$gt.'\', daysOfWeek: \'4\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['friday'] == false){
			echo '{ title: \''.$gt.'\', daysOfWeek: \'5\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['saturday'] == false){
			echo '{ title: \''.$gt.'\', daysOfWeek: \'6\', display: \'background\', className: \'closedbg\' }, ';
		}
		if(get_option('bkf_dd_setting')['sunday'] == false){
			echo '{ title: \''.$gt.'\', daysOfWeek: \'0\', display: \'background\', className: \'closedbg\' }, ';
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
	
	function bkf_cal_pdf(){
		$start = $_REQUEST['starty'] . '-' . $_REQUEST['startm'] . '-' . $_REQUEST['startd'];
		$end = $_REQUEST['endy'] . '-' . $_REQUEST['endm'] . '-' . $_REQUEST['endd'];
		$pdf = new BkfPdf();
		$thepdf = $pdf->calendar($start,$end);
		$ct = __('calendar','bakkbone-florist-companion');
		$thepdf->stream($ct.'-'.$start.'-'.__('to','bakkbone-florist-companion').'-'.$end.'.pdf');
	}

	
	function bkf_cal_csv(){
		$start = $_REQUEST['starty'] . '-' . $_REQUEST['startm'] . '-' . $_REQUEST['startd'];
		$end = $_REQUEST['endy'] . '-' . $_REQUEST['endm'] . '-' . $_REQUEST['endd'];
		$starttime = strtotime($start);
		$endtime = strtotime($end);
		
		$orders = get_posts(array(
			'post_type' => 'shop_order',
			'post_status' => array('new','accept','processing','completed','scheduled','prepared','collect','out','relayed'),
			'numberposts' => '-1',
			'orderby' => 'meta_value_num',
			'meta_key' => '_delivery_timestamp',
			'meta_query' => array(
				array('key' => '_delivery_timestamp', 'value' => $starttime, 'compare' => '>='),
				array('key' => '_delivery_timestamp', 'value' => $endtime, 'compare' => '<')
			)
		));
		
        $upload_dir = wp_upload_dir();
		$filename = $upload_dir['basedir'].'/bkfcsv/'.__('orders','bakkbone-florist-companion').'-'.$start.'-'.__('to','bakkbone-florist-companion').'-'.$end.'.csv';
		$pdfdir = dirname($filename);
		if (!is_dir($pdfdir))
		{
		    mkdir($pdfdir, 0755, true);
		}
		if(file_exists($filename)){
			unlink($filename);
		}
		$afile = fopen($filename, "x+");
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$data = array(
			__('Order ID','bakkbone-florist-companion'),
			$ddtitle,
			__('Items','bakkbone-florist-companion'),
			__('Total','bakkbone-florist-companion'),
			__('Recipient','bakkbone-florist-companion'),
			__('Address','bakkbone-florist-companion'),
			__('Suburb','bakkbone-florist-companion'),
			__('Phone','bakkbone-florist-companion'),
			__('Notes', 'bakkbone-florist-companion')
		);
		fputcsv($afile, $data);
		foreach($orders as $order){
			$o = new WC_Order($order->ID);
			$items = $o->get_items();
			$list = array();
			foreach($items as $item){
				$list[] = $item->get_quantity() . 'x ' . $item->get_name();
			}
			$sa = array();
			if($o->get_shipping_company() !==null && $o->get_shipping_company() !== ''){
				$sa[] = $o->get_shipping_company();
			}
			if($o->get_shipping_address_1() !==null && $o->get_shipping_address_1() !== ''){
				$sa[] = $o->get_shipping_address_1();
			}
			if($o->get_shipping_address_2() !==null && $o->get_shipping_address_2() !== ''){
				$sa[] = $o->get_shipping_address_2();
			}
			$data = array(
				$o->get_id(),
				get_post_meta( $o->get_id(), '_delivery_date', true ),
				implode(", ",$list),
				$o->get_total(),
				$o->get_formatted_shipping_full_name(),
				implode(", ",$sa),
				$o->get_shipping_city(),
				$o->get_shipping_phone(),
				get_post_meta( $o->get_id(), '_shipping_notes', true )
			);
			fputcsv($afile, $data);
		}
		fclose($afile);
		
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($filename) . "\""); 
		readfile($filename); 
		die();
	}

}