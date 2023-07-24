<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdBlocks
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfDdBlocks{

	private $bkf_dd_closed = array();
	private $bkf_dd_full = array();

	function __construct(){
		$this->bkf_dd_closed = get_option("bkf_dd_closed");
		$this->bkf_dd_full = get_option("bkf_dd_full");
		add_action("init", array($this, "bkfScheduleDdPurge"));
		add_action("bkf_dd_purge", array($this, "bkfDdPurge"));
		add_action("admin_menu", array($this,"bkf_admin_menu"),5);
		add_action("admin_init",array($this,"bkfAddDdBlocksInit"));
	}
	
	function bkfScheduleDdPurge() {
		if ( false === as_has_scheduled_action( 'bkf_dd_purge' ) ){
			as_schedule_recurring_action( strtotime( 'tomorrow' ), DAY_IN_SECONDS, 'bkf_dd_purge', array(), '', true );
		}
	}
	
	function bkfDdPurge() {
		$closed = get_option('bkf_dd_closed');
		$full = get_option('bkf_dd_full');
		$datefees = get_option('bkf_dd_ds_fees');
		foreach($closed as $ts => $date){
			if($ts < (string)strtotime(wp_date("Y-m-d"))){
				unset($closed[$ts]);
				update_option('bkf_dd_closed', $closed);
			}
		}
		foreach($full as $ts => $date){
			if($ts < (string)strtotime(wp_date("Y-m-d"))){
				unset($full[$ts]);
				update_option('bkf_dd_full', $full);
			}
		}
		foreach($datefees as $ts => $fee){
			if($ts < (string)strtotime(wp_date("Y-m-d"))){
				unset($datefees[$ts]);
				update_option('bkf_dd_ds_fees', $datefees);
			}
		}
	}

	function bkf_admin_menu(){
		$admin_page = add_submenu_page(
		"bkf_dd",
		__("Blocked Delivery Dates","bakkbone-florist-companion"),
		__("Blocked Dates","bakkbone-florist-companion"),
		"manage_woocommerce",
		"bkf_ddb",
		array($this, "bkf_dd_blocks_page"),
		5
		);
		add_action( 'load-'.$admin_page, array($this, 'bkf_ddb_help_tab') );
	}
	
	function bkf_ddb_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_ddb_help';
		$callback = array($this, 'bkf_ddb_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_ddb_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/dd/blocked-dates/" target="_blank">https://plugins.bkbn.au/docs/bkf/dd/blocked-dates/</a>
		<?php
	}
	
	function bkf_dd_blocks_page(){
		$bkf_dd_closed = get_option("bkf_dd_closed");
		$closedsort = $bkf_dd_closed;
		ksort($closedsort);
		$bkf_dd_full = get_option("bkf_dd_full");
		$fullsort = $bkf_dd_full;
		ksort($fullsort);
		$nonce = wp_create_nonce("bkf");
		$ajaxurl = admin_url('admin-ajax.php');
		$phtext = __("Date", "bakkbone-florist-companion");
		$ubtext = __("Unblock Date", "bakkbone-florist-companion");
		?>

		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Delivery Date Blocks","bakkbone-florist-companion") ?></h1><div style="width:100%;display:flex;">
				<div class="inside bkf-inside" style="width:50%;">
					<h2 style="margin-top:0;"><?php esc_html_e('Closure Dates', 'bakkbone-florist-companion') ?></h2>
					<div class="bkf-form" style="max-width:200px;text-align:center;margin: 12px auto;">
						<form id="add-closed" action="<?php echo $ajaxurl; ?>" />
							<h4 style="margin:0;"><?php esc_html_e('Add Closure Date', 'bakkbone-florist-companion'); ?></h4>
							<?php wp_nonce_field('bkf', 'nonce'); ?>
							<input type="hidden" name="action" value="bkf_dd_add_closed" />
							<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phtext; ?>" autocomplete="off" /></p>
							<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Add Date as Closed', 'bakkbone-florist-companion'); ?>"></p>
						</form>
					</div><?php if(!empty(get_option('bkf_dd_closed'))){ ?>
					<ul style="display:grid;grid-template-columns:auto auto;width:100%;"><?php foreach($closedsort as $ts => $ds){
						echo '<li class="bkf-list-group" style="display:flex;flex-direction:row;justify-content:space-between;align-items:center;"><p>' . $ds . '</p><a style="height:20px;" href="' . $ajaxurl . '?action=bkf_dd_remove_closed&ts='.$ts.'&nonce='.$nonce.'"><div class="bkf-close tooltip"><span class="tooltiptext">'.$ubtext.'</span></div></a></li>';
					}; ?>
				</ul><? } ?>
				</div>
				<div class="inside bkf-inside" style="width:50%;">
					<h2 style="margin-top:0;"><?php esc_html_e('Fully Booked Dates', 'bakkbone-florist-companion') ?></h2>
					<div class="bkf-form" style="max-width:200px;text-align:center;margin: 12px auto;">
						<form id="add-full" action="<?php echo $ajaxurl; ?>" />
							<h4 style="margin:0;"><?php esc_html_e('Add Fully Booked Date', 'bakkbone-florist-companion'); ?></h4>
							<?php wp_nonce_field('bkf', 'nonce'); ?>
							<input type="hidden" name="action" value="bkf_dd_add_full" />
							<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phtext; ?>" autocomplete="off" /></p>
							<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Add Date as Fully Booked', 'bakkbone-florist-companion'); ?>"></p>
						</form>
					</div><?php if(!empty(get_option('bkf_dd_full'))){ ?>
					<ul style="display:grid;grid-template-columns:auto auto;width:100%;"><?php foreach($fullsort as $ts => $ds){
						echo '<li class="bkf-list-group" style="display:flex;flex-direction:row;justify-content:space-between;align-items:center;"><p>' . $ds . '</p><a style="height:20px;" href="' . $ajaxurl . '?action=bkf_dd_remove_full&ts='.$ts.'&nonce='.$nonce.'"><div class="bkf-close tooltip"><span class="tooltiptext">'.$ubtext.'</span></div></a></li>';
					}; ?>
				</ul><? } ?>
				</div>
			</div>
			<script id="calendarscript">

			  document.addEventListener('DOMContentLoaded', function() {
				var calendarEl = document.getElementById('calendar');
				var calendar = new FullCalendar.Calendar(calendarEl, {
				  initialView: 'dayGridMonth',
					views: {
						dayGridMonth: {
							dayMaxEventRows: 6,
							dayHeaderFormat: {
								weekday: 'long'
							}
						}
					},
					headerToolbar: {
						start: 'title',
						center: '',
						end: 'today prev,next'
					},
					buttonText: {
						today: 'current month'
					},
					firstDay: 1,
					height: '60vh',
					events:[
						<?php
				$closed = get_option('bkf_dd_closed');
				$full = get_option('bkf_dd_full');
				$ct = __('Closed','bakkbone-florist-companion');
				$gt = __('Closed (Global)','bakkbone-florist-companion');
				$ft = __('Fully Booked','bakkbone-florist-companion');
				if(null !== $closed){
					foreach($closed as $ts => $date){
						$string = wp_date("Y-m-d",$ts);
						echo '{ title: \''.$ct.'\', start: \'' . $string . '\', className: \'closedbg\' }, ';
					}
				}
				
				if(null !== $full){
					foreach($full as $ts => $date){
						$string = wp_date("Y-m-d",$ts);
						echo '{ title: \''.$ft.'\', start: \''.$string.'\', className: \'fullbg\' }, ';
					}
					
					if(get_option('bkf_dd_setting')['monday'] == false){
						echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'1\', className: \'closedbg\' }, ';
					}
					if(get_option('bkf_dd_setting')['tuesday'] == false){
						echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'2\', className: \'closedbg\' }, ';
					}
					if(get_option('bkf_dd_setting')['wednesday'] == false){
						echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'3\', className: \'closedbg\' }, ';
					}
					if(get_option('bkf_dd_setting')['thursday'] == false){
						echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'4\', className: \'closedbg\' }, ';
					}
					if(get_option('bkf_dd_setting')['friday'] == false){
						echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'5\', className: \'closedbg\' }, ';
					}
					if(get_option('bkf_dd_setting')['saturday'] == false){
						echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'6\', className: \'closedbg\' }, ';
					}
					if(get_option('bkf_dd_setting')['sunday'] == false){
						echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'0\', className: \'closedbg\' }, ';
					}
				}
				?>
				]
				});
				calendar.render();
			  });

			</script>
			  <div class="bkf-box"><div class="inside bkf-inside">
			  <div id='calendar'></div></div></div>
		</div></div>
		<script id="datepicker">
			jQuery(document).ready(function( $ ) {
				jQuery(".closure-date").attr( 'readOnly' , 'true' );
				jQuery(".closure-date").datepicker( {
					firstDay: 1,
					minDate: 0,
					dateFormat: "DD, d MM yy",
					hideIfNoPrevNext: true,
					constrainInput: true,
					beforeShowDay: blockedDates
				} );
  			 var closedDatesList = [<?php
		 		$closeddates = get_option('bkf_dd_closed');
				if( !empty($closeddates)){
				 $i = 0;
				 $len = count($closeddates);
				 foreach($closeddates as $date){
					 $ts = strtotime($date);
					 $jsdate = wp_date('n,j,Y',$ts);
					 if ($i == $len - 1) {
					 echo '['.$jsdate.']';	
			 } else {
					 echo '['.$jsdate.'],';		 	
					 }
					 $i++;
			 };}; ?>];
   			 var fullDatesList = [<?php
		 		$fulldates = get_option('bkf_dd_full');
				if( !empty($fulldates)){
				 $i = 0;
				 $len = count($fulldates);
				 foreach($fulldates as $date){
					 $ts = strtotime($date);
					 $jsdate = wp_date('n,j,Y',$ts);
					 if ($i == $len - 1) {
					 echo '['.$jsdate.']';
				 } else {
					 echo '['.$jsdate.'],';		 	
					 }
					 $i++;
				 };}; ?>];
		 
		 function blockedDates(date) {
			 var w = date.getDay();
			 var m = date.getMonth();
			 var d = date.getDate();
			 var y = date.getFullYear();
			 
			 <?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 1) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 2) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 3) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 4) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 5) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 6) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 0) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
			 
		 for (i = 0; i < closedDatesList.length; i++) {
		   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
		   {
		   	 return [false,"closed","<?php echo $ct; ?>"];
		   }
		 }
		 for (i = 0; i < fullDatesList.length; i++) {
		   if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
		   {
			 return [false,"booked","<?php echo $ft; ?>"];
		   }
		 }
		 return [true];
	 }
		 } );
		</script>
		<?php
	}
	
	function bkfAddDdBlocksInit(){
		register_setting(
			"bkf_dd_blocks_group",
			"bkf_dd_closed",
			array('type' => 'array')
		);
		register_setting(
			"bkf_dd_blocks_group",
			"bkf_dd_full",
			array('type' => 'array')
		);
	}
		
}