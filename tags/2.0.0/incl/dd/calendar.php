<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdCalendar
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdCalendar{
	
    function __construct(){
		add_action("admin_menu",array($this,"bkfAddCalendarPageOption"));
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
			3//position
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
					dayMaxEventRows: 6,
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
			headerToolbar: {
				start: 'dayGridMonth,listMonth dayGridWeek,listWeek dayGridDay,listDay',
				center: 'title',
				end: 'today prev,next'
			},
			footerToolbar: {
				start: 'dayGridMonth,listMonth dayGridWeek,listWeek dayGridDay,listDay',
				center: '',
				end: 'today prev,next'
			},
			buttonText: {
				
			},

			firstDay: 1,
			height: '80vh',
			events:[
				<?php
				$orders = get_posts(array(
			'post_type' => 'shop_order',
			'post_status' => array('new','accept','processing','completed','scheduled','prepared','out','relayed')
		));
		$closed = get_option('bkf_dd_closed');
		$full = get_option('bkf_dd_full');
		
		foreach($orders as $order){
			$id = $order->ID;
			$url = get_edit_post_link($order);
			$date = date("Y-m-d",get_post_meta($order->ID,'_delivery_timestamp',true));
			$recipient = get_post_meta($order->ID, '_shipping_first_name',true) . ' ' . get_post_meta($order->ID, '_shipping_last_name',true);
			$suburb = get_post_meta($order->ID, '_shipping_city',true);
			echo '{ title: \'Order #'.$order->ID.' - '. $recipient . ', ' . $suburb . '\', url: \''.html_entity_decode($url).'\', start: \''.$date.'\' }, ';
		}
		if(null !== $closed){
			foreach($closed as $ts => $date){
				$string = date("Y-m-d",$ts);
				echo '{ title: \'Closed\', start: \'' . $string . '\', display: \'background\', className: \'closedbg\' }, ';
			}
		}
		if(null !== $full){
			foreach($full as $ts => $date){
				$string = date("Y-m-d",$ts);
				echo '{ title: \'Fully Booked\', start: \''.$string.'\', display: \'background\', className: \'fullbg\' }, ';
			}
			if(get_option('bkf_dd_setting')['monday'] == false){
				echo '{ title: \'Closed\', daysOfWeek: \'1\', display: \'background\', className: \'closedbg\' }, ';
			}
			if(get_option('bkf_dd_setting')['tuesday'] == false){
				echo '{ title: \'Closed\', daysOfWeek: \'2\', display: \'background\', className: \'closedbg\' }, ';
			}
			if(get_option('bkf_dd_setting')['wednesday'] == false){
				echo '{ title: \'Closed\', daysOfWeek: \'3\', display: \'background\', className: \'closedbg\' }, ';
			}
			if(get_option('bkf_dd_setting')['thursday'] == false){
				echo '{ title: \'Closed\', daysOfWeek: \'4\', display: \'background\', className: \'closedbg\' }, ';
			}
			if(get_option('bkf_dd_setting')['friday'] == false){
				echo '{ title: \'Closed\', daysOfWeek: \'5\', display: \'background\', className: \'closedbg\' }, ';
			}
			if(get_option('bkf_dd_setting')['saturday'] == false){
				echo '{ title: \'Closed\', daysOfWeek: \'6\', display: \'background\', className: \'closedbg\' }, ';
			}
			if(get_option('bkf_dd_setting')['sunday'] == false){
				echo '{ title: \'Closed\', daysOfWeek: \'0\', display: \'background\', className: \'closedbg\' }, ';
			}
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