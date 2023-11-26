<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Fees_Date_Specific
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Delivery_Date_Fees_Date_Specific{
	
	private $bkf_dd_ds_fees = [];
	
	function __construct(){
		$this->bkf_dd_ds_fees = get_option("bkf_dd_ds_fees");
		add_action("admin_menu", [$this, "bkfeeds_admin_menu"], 9);
		add_action("admin_init", [$this, "bkfAddDdFeesInit"]);
	}
	
	function bkfeeds_admin_menu(){
		$admin_page = add_submenu_page(
		"bkf_dd",
		__("Date-Specific Fees","bakkbone-florist-companion"),
		'— ' . esc_html__("Date-Specific","bakkbone-florist-companion"),
		"manage_woocommerce",
		"bkf_fees_ds",
		[$this, 'bkf_fees_ds_settings_page'],
		9
		);
		add_action( 'load-'.$admin_page, [$this, 'bkf_dsf_help_tab'] );
	}
	
	function bkf_dsf_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_dsf_help';
		$callback = [$this, 'bkf_dsf_help'];
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_dsf_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/dd/date-specific/" target="_blank">https://docs.floristpress.org/dd/date-specific/</a>
		<?php
	}
		
	function bkf_fees_ds_settings_page(){
		$bkf_dd_ds_fees = get_option("bkf_dd_ds_fees");
		$feesort = $bkf_dd_ds_fees;
		ksort($feesort);
		$bkf_dd_closed = get_option("bkf_dd_closed");
		$closedsort = $bkf_dd_closed;
		ksort($closedsort);
		$bkf_dd_full = get_option("bkf_dd_full");
		$fullsort = $bkf_dd_full;
		ksort($fullsort);
		$nonce = wp_create_nonce("bkf");
		$ajaxurl = admin_url('admin-ajax.php');
		$ct = __("Closed", "bakkbone-florist-companion");
		$gt = __("Closed (Global)", "bakkbone-florist-companion");
		$ft = __("Fully Booked", "bakkbone-florist-companion");
		?>

		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Date-Specific Fees","bakkbone-florist-companion") ?></h1>
			<p style="margin:0;"><?php esc_html_e("Click a fee on the calendar to delete it.", "bakkbone-florist-companion"); ?></p>
				<div class="inside">
					<div class="bkf-form" style="max-width:200px;text-align:center;margin: 12px 0;">
						<form id="add-closed" action="<?php echo $ajaxurl; ?>" />
							<h4 style="margin:0;"><?php esc_html_e('Add Fee', 'bakkbone-florist-companion'); ?></h4>
							<?php wp_nonce_field('bkf', 'nonce'); ?>
							<input type="hidden" name="action" value="bkf_dd_add_fee" />
							<p style="margin:5px 0;"><input type="text" name="date" class="fee-date input-text bkf-form-control" required autocomplete="off" placeholder="<?php esc_html_e("Date", "bakkbone-florist-companion"); ?>" style="max-width:170px;" /></p>
							<p class="bkf-input-icon" style="margin:5px 0;"><input class="input-text bkf-form-control" type="text" name="fee" placeholder="***.**" pattern="\d+\.\d{2,}" style="padding-right:8px;max-width:170px;" required /><i><?php bkf_currency_symbol(true); ?></i></p>
							<p style="margin:5px 0;"><input class="input-text bkf-form-control" type="text" name="title" placeholder="<?php esc_html_e('Fee Title', 'bakkbone-florist-companion'); ?>" required style="max-width:170px;" /></p>
							<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Add Fee', 'bakkbone-florist-companion'); ?>"></p>
						</form>
					</div>
				</div>
				<script id="calendarscript">
					document.addEventListener('DOMContentLoaded', function() {
				var calendarEl = document.getElementById('calendar');
				var calendar = new FullCalendar.Calendar(calendarEl, {
					schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
					eventClick: function(info) {
						if (info.event.extendedProps.function !== null) {
							if (confirm('<?php esc_html_e('Remove fee for this date?', 'bakkbone-florist-companion'); ?>')){
								window.location.href = '<?php echo $ajaxurl . '?action=';?>' + info.event.extendedProps.function + '&date=' + info.event.extendedProps.ts + '<?php echo '&nonce='.$nonce; ?>';
							}
						}
					},
					initialView: 'dayGridMonth',
					views: {
						dayGridMonth: {
							dayMaxEventRows: 6,
							dayHeaderFormat: {
								weekday: 'short'
							}
						}
					},
					headerToolbar: {
						start: 'title',
						center: 'dayGridMonth,listMonth',
						end: 'today prev,next'
					},
					buttonText: {
						today: 'current month'
					},
					firstDay: 1,
					height: '60vh',
					events:[
						<?php
				$fees = get_option('bkf_dd_ds_fees');
				if(null !== $fees){
					foreach($fees as $ts => $array){
						$string = wp_date("Y-m-d",$ts);
						echo '{ title: \''.$array['title'].': '.bkf_currency_symbol().$array['fee'].'\', start: \'' . $string . '\', className: \'closedbg\', function: \'bkf_dd_remove_fee\', ts: \''.$ts.'\' }, ';
					}
				}
				if(null !== $bkf_dd_closed){
					foreach($bkf_dd_closed as $ts => $date){
						$string = wp_date("Y-m-d",$ts);
						echo '{ title: \''.$ct.'\', start: \'' . $string . '\', display: \'background\', className: \'closedbg\', function: null }, ';
					}
				}
				if(null !== $bkf_dd_full){
					foreach($bkf_dd_full as $ts => $date){
						$string = wp_date("Y-m-d",$ts);
						echo '{ title: \''.$ft.'\', start: \''.$string.'\', display: \'background\', className: \'fullbg\', function: null }, ';
					}
				}
				if(get_option('bkf_dd_setting')['monday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'1\', display: \'background\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['tuesday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'2\', display: \'background\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['wednesday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'3\', display: \'background\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['thursday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'4\', display: \'background\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['friday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'5\', display: \'background\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['saturday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'6\', display: \'background\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['sunday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'0\', display: \'background\', className: \'closedbg\', function: null }, ';
				}
				?>
				]
				});
				calendar.render();
				});
			</script>
			  <div class="bkf-box"><div class="inside bkf-inside" style="margin:0;">
			  <div id='calendar'></div></div></div>
		</div></div>
		<script id="datepicker">
			jQuery(document).ready(function( $ ) {
				jQuery(".fee-date").attr( 'readOnly' , 'true' );
				jQuery(".fee-date").datepicker( {
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
   			 var feeDatesList = [<?php
		 		$feedates = get_option('bkf_dd_ds_fees');
				if( !empty($feedates)){
				 $i = 0;
				 $len = count($feedates);
				 foreach($feedates as $ts => $fee){
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
		 for (i = 0; i < feeDatesList.length; i++) {
		   if ((m == feeDatesList[i][0] - 1) && (d == feeDatesList[i][1]) && (y == feeDatesList[i][2]))
		   {
			 return [false,"closed","<?php esc_html_e('Fee Applied', 'bakkbone-florist-companion'); ?>"];
		   }
		 }
		 return [true];
	 }
		 } );
		</script>
		<?php
	}
	
	function bkfAddDdFeesInit(){
		register_setting(
			"bkf_dd_ds_fees_group",
			"bkf_dd_ds_fees",
			array('type' => 'array')
		);
	}
	
}