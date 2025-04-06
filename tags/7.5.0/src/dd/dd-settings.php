<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Settings
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

use Automattic\WooCommerce\Admin\PluginsHelper;

class BKF_Delivery_Date_Settings extends WC_Settings_Page {

	function __construct() {
		$this->id		= 'bkf_dd';
		$this->label	= _x('Delivery Dates', 'Settings tab label', 'bakkbone-florist-companion');
		parent::__construct();

		add_action('woocommerce_admin_field_bkf_weekday', [$this, 'woocommerce_admin_field_bkf_weekday']);
		add_action('woocommerce_after_settings_bkf_dd', [$this, 'woocommerce_after_settings_bkf_dd']);
	}

	function output() {
		global $current_section, $hide_save_button;

		$sections = [
			'mlt',
			'ddb',
			'cb',
			'ts',
			'ds'
		];

		if (in_array($current_section, $sections)) {
			$hide_save_button = true;
		} else {
			parent::output();
		}
	}

	function get_own_sections()	{
		$sections = [
			''		=> __('General', 'bakkbone-florist-companion'),
			'wd'	=> __('Weekdays', 'bakkbone-florist-companion'),
			'mlt'	=> __('Method Lead Times', 'bakkbone-florist-companion'),
			'mr'	=> __('Method Restrictions', 'bakkbone-florist-companion'),
			'ddb'	=> __('Blocked Dates', 'bakkbone-florist-companion'),
			'cb'	=> __('Category Blocks', 'bakkbone-florist-companion'),
			'ts'	=> __('Timeslots', 'bakkbone-florist-companion'),
			'fee'	=> __('Fees', 'bakkbone-florist-companion'),
			'ds'	=> __('Date Fees', 'bakkbone-florist-companion'),
		];
		return $sections;
	}

	function woocommerce_admin_field_bkf_weekday($value) {
		?>
		<script type="text/javascript" id="enable_disable_<?php echo esc_attr($value['id']); ?>">
			jQuery(document.body).on('change', '#bkf-dd-<?php echo esc_attr($value['id']); ?>', function($){
				<?php echo esc_attr($value['id']); ?> = jQuery('#bkf-dd-<?php echo esc_attr($value['id']); ?>').is(':checked');

				<?php echo esc_attr($value['id']); ?>ElSd = jQuery('#bkf-sd-<?php echo esc_attr($value['id']); ?>');

				<?php echo esc_attr($value['id']); ?>ElLt = jQuery('#bkf-sd-<?php echo esc_attr($value['id']); ?>lead');

				if (<?php echo esc_attr($value['id']); ?>) {
					<?php echo esc_attr($value['id']); ?>ElSd.prop('disabled', false);
					<?php echo esc_attr($value['id']); ?>ElLt.prop('disabled', false);
					<?php echo esc_attr($value['id']); ?>ElSd.prop('required', true);
					<?php echo esc_attr($value['id']); ?>ElLt.prop('required', true);
				} else {
					<?php echo esc_attr($value['id']); ?>ElSd.prop('disabled', true);
					<?php echo esc_attr($value['id']); ?>ElLt.prop('disabled', true);
					<?php echo esc_attr($value['id']); ?>ElSd.prop('required', false);
					<?php echo esc_attr($value['id']); ?>ElLt.prop('required', false);
				}
			});
		</script>
		<?php
		$day = $value['id'];
		$tooltip_html = $value['desc_tip'] ? wc_help_tip($value['desc']) : '';
		if($value['value']){
			$checked = "checked";
		} else {
			$checked = "";
		}
		?>
		<tr valign="top" class="petals_url"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
			<th scope="row" class="titledesc">
				<label for="bkf-dd-<?php echo esc_attr($day); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
		<fieldset class="show_options_if_checked"><label><input id="bkf-dd-<?php echo esc_attr($day); ?>" <?php echo $checked ?> type="checkbox" class="form-control" name="bkf_dd_setting[<?php echo esc_attr($day); ?>]" /><?php esc_html_e("Enabled","bakkbone-florist-companion") ?></label></fieldset>
		<?php
		if($value['value']){
			$disabled = " required";
		} else {
			$disabled = " disabled";
		}

		?>
		<fieldset class="hidden_option"><table><tr><th><label for="bkf-sd-<?php echo esc_attr($day); ?>"><?php esc_html_e('Cutoff Time', 'bakkbone-florist-companion'); echo wc_help_tip(esc_html__('Time by which a customer must order for delivery on this day. This works in tandem with the days field below.', 'bakkbone-florist-companion')); ?></label></th><td><input class="form-control" id="bkf-sd-<?php echo esc_attr($day); ?>" type="time" step="60" name="bkf_sd_setting[<?php echo esc_attr($day); ?>]" placeholder="" value="<?php echo esc_attr($value['time']); ?>"<?php echo $disabled; ?> /></td></tr></table>
		<table><tr><th><label for="bkf-sd-<?php echo esc_attr($day); ?>lead"><?php esc_html_e('Day(s) ahead', 'bakkbone-florist-companion'); echo wc_help_tip(esc_html__('Number of days in advance a customer must order for delivery on this day. Enter 0 to allow same-day delivery.', 'bakkbone-florist-companion')); ?></label></th><td><input class="small-text form-control" id="bkf-sd-<?php echo esc_attr($day); ?>lead" type="number" step="1" min="0" max="<?php echo get_option('bkf_ddi_setting')['ddi'] * 7 - 1; ?>" name="bkf_sd_setting[<?php echo esc_attr($day); ?>lead]" placeholder="" value="<?php echo esc_attr($value['lead']); ?>"<?php echo $disabled; ?> /></fieldset></td></tr></table>
		</td></tr>
		<?php
	}

	function woocommerce_after_settings_bkf_dd() {
		if (isset($_GET['section']) && $_GET['section'] == 'mlt') {
			$nonce = wp_create_nonce("bkf");
			$wd = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
			global $wpdb;
			$co = bkf_get_method_specific_leadtimes();
			$sm = [];
			$allzones = WC_Data_Store::load('shipping-zone');
			$rawzones = $allzones->get_zones();
			$zones = [];
			$zones[] = new WC_Shipping_Zone( 0 );
			foreach($rawzones as $rawzone){
				$zones[] = new WC_Shipping_Zone( $rawzone );
			}
			foreach($zones as $zone){
				$methods = $zone->get_shipping_methods();
				$zone_name = $zone->get_zone_name();
				foreach($methods as $method){
					$method_is_taxable = $method->is_taxable();
					$method_is_enabled = $method->is_enabled();
					$method_instance_id = $method->get_instance_id();
					$method_title = $method->get_method_title();
					$method_description = $method->get_method_description();
					$method_user_title = $method->get_title();
					$method_rate_id = $method->get_rate_id();
					$sm[] = array(
						'enabled'		=>	$method_is_enabled,
						'taxable'		=>	$method_is_taxable,
						'instanceid'	=>	$method_instance_id,
						'title'			=>	$method_title,
						'description'	=>	$method_description,
						'usertitle'		=>	$method_user_title,
						'rateid'		=>	$method_rate_id,
						'zone'			=>	$zone_name
					);
				}
			}
			$wds = get_option('bkf_dd_setting');
			$dms = get_option('bkf_dm_setting');
			?>
			<h2><?php esc_html_e("Method-Specific Lead Times","bakkbone-florist-companion") ?></h2>
			<div id="bkf_dd_mlt_settings-description"><p><?php esc_html_e('Here you can add lead times per weekday for specific delivery methods. If no lead time is specified below for a specific day/method, your global defaults will apply.','bakkbone-florist-companion'); ?></p></div>
			<div style="display:grid;grid-template-columns:auto auto;width:100%;">
				<?php foreach($sm as $smethod){?><div class="inside bkf-inside">
					<h2 style="margin:0;text-align:center;color:black;"><?php echo $smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle']; ?></h2>
					<p style="margin-top:0;text-align:center;color:black;"><strong><?php esc_html_e('Zone: ', 'bakkbone-florist-companion'); ?></strong><?php echo $smethod['zone']; ?></p>
					<div style="columns: 100px 3;">
					<?php foreach($wd as $day){
						if(!isset($wds[$day]) || $wds[$day] == false){
							echo '<p style="break-inside:avoid;margin-top:0;">'.sprintf(__('%s disabled globally','bakkbone-florist-companion'), ucwords($day)).'</p>';
						} else {
							if(!empty($dms[$day]) && in_array($smethod['rateid'],$dms[$day])){
									echo '<p style="break-inside:avoid;margin-top:0;">'.sprintf(__('%s disabled for this method','bakkbone-florist-companion'), ucwords($day)).'</p>';
							} else {
								if(!empty($co[$smethod['rateid'].'-'.$day])){
									$thisco = $co[$smethod['rateid'].'-'.$day];
									echo '<p style="break-inside:avoid;margin-top:0;"><strong>'.ucwords($thisco['day']).':</strong> '.sprintf(__('%1s, %2s day(s) ahead', 'bakkbone-florist-companion'), date("g:i a", strtotime($thisco['cutoff'])), $thisco['leadtime']).' <em><a href="'.admin_url('admin-ajax.php?action=bkf_sd_del&nonce='.$nonce.'&id='.$thisco['id']).'">'.esc_html__('Reset','bakkbone-florist-companion').'</a></em></p>';
								} else {
									$maxlead = get_option('bkf_ddi_setting')['ddi'] * 7 - 1;
									/* translators: %1s: cutoff time. %2s: lead days. */
								echo '<form style="break-inside:avoid;margin-top:0;" class="bkf-form" id="addsd-'.$smethod['rateid'].'-'.$day.'" action="'.admin_url('admin-ajax.php').'"><p style="margin:0"><strong>'.ucwords($day).'</strong><br>('.sprintf(__('%1s, %2s day(s) ahead', 'bakkbone-florist-companion'), date("g:i a", strtotime(get_option('bkf_sd_setting')[$day])), get_option('bkf_sd_setting')[$day.'lead']).')</p><input type="hidden" name="action" value="bkf_sd_add" />
									<input type="hidden" name="method" value="'.$smethod['rateid'].'" />'.wp_nonce_field('bkf', 'nonce', true, false).'
									<input type="hidden" name="day" value="'.$day.'" />
									<input type="time" class="bkf-form-control" required id="'.$smethod['rateid'].'-'.$day.'-cutoff" name="cutoff" step="300" />
									<input type="number" class="bkf-form-control" required id="'.$smethod['rateid'].'-'.$day.'-leadtime" name="leadtime" step="1" min="0" max="'.$maxlead.'" />
									<input type="submit" value="'.esc_html__('Set Cutoff','bakkbone-florist-companion').'" id="'.$smethod['rateid'].'-'.$day.'-submit" class="button button-primary" /></form>';
							}
							}
						}
				}
					?></div></div><?php
				}
				?></div><?php
		} elseif (isset($_GET['section']) && $_GET['section'] == 'ddb') {
			$bkf_dd_closed = get_option("bkf_dd_closed");
			$closedsort = $bkf_dd_closed;
			ksort($closedsort);
			$bkf_dd_full = get_option("bkf_dd_full");
			$fullsort = $bkf_dd_full;
			ksort($fullsort);
			$nonce = wp_create_nonce("bkf");
			$ajaxurl = admin_url('admin-ajax.php');
			$phtext = __("Date", "bakkbone-florist-companion");
			$phstext = __("Start Date", "bakkbone-florist-companion");
			$phetext = __("End Date", "bakkbone-florist-companion");
			$ubtext = __("Unblock Date", "bakkbone-florist-companion");
			?>

			<h2><?php esc_html_e("Delivery Date Blocks","bakkbone-florist-companion") ?></h2>
			<div id="bkf_dd_ddb_settings-description"><p><?php esc_html_e("Click a block on the calendar to delete it.", "bakkbone-florist-companion"); ?></p></div>
			<div style="display:flex;">
				<div class="bkf-form" style="max-width:200px;text-align:center;margin:0 5px 5px 0;">
					<form id="add-closed" action="<?php echo $ajaxurl; ?>" />
						<h4 style="margin:0;"><?php esc_html_e('Add Closure Date', 'bakkbone-florist-companion'); ?></h4>
						<?php wp_nonce_field('bkf', 'nonce'); ?>
						<input type="hidden" name="action" value="bkf_dd_add_closed" />
						<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phtext; ?>" autocomplete="off" /></p>
						<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Add Date as Closed', 'bakkbone-florist-companion'); ?>"></p>
					</form>
				</div>
				<div class="bkf-form" style="max-width:200px;text-align:center;margin:0 5px 5px 0;">
					<form id="add-closed-range" onsubmit="return bkfValidateFormClosedRange()" action="<?php echo $ajaxurl; ?>" />
						<h4 style="margin:0;"><?php esc_html_e('Add Closure Date Range', 'bakkbone-florist-companion'); ?></h4>
						<?php wp_nonce_field('bkf', 'nonce'); ?>
						<input type="hidden" name="action" value="bkf_dd_add_closed_range" />
						<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date1" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phstext; ?>" autocomplete="off" /></p>
						<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date2" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phetext; ?>" autocomplete="off" /></p>
						<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Add Dates as Closed', 'bakkbone-florist-companion'); ?>"></p>
					</form>
				</div>
				<div class="bkf-form" style="max-width:200px;text-align:center;margin:0 5px 5px 0;">
					<form id="add-full" action="<?php echo $ajaxurl; ?>" />
						<h4 style="margin:0;"><?php esc_html_e('Add Fully Booked Date', 'bakkbone-florist-companion'); ?></h4>
						<?php wp_nonce_field('bkf', 'nonce'); ?>
						<input type="hidden" name="action" value="bkf_dd_add_full" />
						<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phtext; ?>" autocomplete="off" /></p>
						<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Add Date as Fully Booked', 'bakkbone-florist-companion'); ?>"></p>
					</form>
				</div>
				<div class="bkf-form" style="max-width:200px;text-align:center;margin:0 0 5px 0;">
					<form id="add-full-range" onsubmit="return bkfValidateFormFullRange()" action="<?php echo $ajaxurl; ?>" />
						<h4 style="margin:0;"><?php esc_html_e('Add Fully Booked Date Range', 'bakkbone-florist-companion'); ?></h4>
						<?php wp_nonce_field('bkf', 'nonce'); ?>
						<input type="hidden" name="action" value="bkf_dd_add_full_range" />
						<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date1" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phstext; ?>" autocomplete="off" /></p>
						<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date2" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phetext; ?>" autocomplete="off" /></p>
						<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Add Dates as Fully Booked', 'bakkbone-florist-companion'); ?>"></p>
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
							if (confirm('<?php esc_html_e('Remove block for this date?', 'bakkbone-florist-companion'); ?>')){
								window.location.href = '<?php echo $ajaxurl . '?action=';?>' + info.event.extendedProps.function + '&ts=' + info.event.extendedProps.ts + '<?php echo '&nonce='.$nonce; ?>';
							}
						}
					},
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
						today: '<?php esc_html_e('Current month', 'bakkbone-florist-companion'); ?>'
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
						echo '{ title: \''.$ct.'\', start: \'' . $string . '\', className: \'closedbg\', function: \'bkf_dd_remove_closed\', ts: \''.$ts.'\' }, ';
					}
				}

				if(null !== $full){
					foreach($full as $ts => $date){
						$string = wp_date("Y-m-d",$ts);
						echo '{ title: \''.$ft.'\', start: \''.$string.'\', className: \'fullbg\', function: \'bkf_dd_remove_full\', ts: \''.$ts.'\' }, ';
					}
				}

				if(get_option('bkf_dd_setting')['monday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'1\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['tuesday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'2\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['wednesday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'3\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['thursday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'4\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['friday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'5\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['saturday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'6\', className: \'closedbg\', function: null }, ';
				}
				if(get_option('bkf_dd_setting')['sunday'] == false){
					echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'0\', className: \'closedbg\', function: null }, ';
				}

				?>
				]
				});
				calendar.render();
			  });
			</script>
		<div id='calendar'></div>
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
		} elseif (isset($_GET['section']) && $_GET['section'] == 'cb') {
			$nonce = wp_create_nonce("bkf");

			$product_categories = get_terms(array('taxonomy'=>'product_cat','hide_empty' => false));

			$days = get_option('bkf_dd_setting');
			$daysoff = get_option('bkf_dm_setting');
			$cb = bkf_get_catblocks();
			uasort($cb, function($a,$b){return strcmp(strtotime($a['date']),strtotime($b['date']));} );
		?>
			<h2><?php esc_html_e("Product Category Blocks","bakkbone-florist-companion") ?></h2>
			<div id="bkf_dd_cb_settings-description"><p><?php esc_html_e('Dates entered below will be unavailable for the relevant product category. Click a block on the calendar to delete it.','bakkbone-florist-companion') ?></p></div>
			<div><form class="bkf-form" id="addform" action="<?php echo admin_url('admin-ajax.php') ?>">
			  <h4 style="margin:0;"><?php esc_html_e('Add Category Block Date', 'bakkbone-florist-companion'); ?></h4>
							<input type="hidden" name="action" value="bkf_cb_add" />
							<?php wp_nonce_field('bkf', 'nonce'); ?>
							<?php
							$args = array(
								"show_option_none" => "Select a category...",
								"option_none_value" => "",
								"value" => "term_id",
								"hierarchical" => true,
								"taxonomy" => "product_cat",
								"show_count" => false,
								"hide_empty" => false,
								"echo" => true,
								"id" => "add-cat",
								"name" => "category",
								"class" => "regular-text bkf-form-control"
							);
							wp_dropdown_categories($args);
							?>
							<label style="display:inline-block;"><?php esc_html_e('Date: ', 'bakkbone-florist-companion'); ?><input type="text" class="bkf-form-control" id="add-date" name="date" required /></label>
							<input type="submit" value="<?php esc_html_e('Add Date','bakkbone-florist-companion'); ?>" id="add-submit" class="button button-primary" />
			</form></div>
			<div><form class="bkf-form" id="addrangeform" onsubmit="return bkfValidateFormCatBlockRange()" action="<?php echo admin_url('admin-ajax.php') ?>">
			  <h4 style="margin:0;"><?php esc_html_e('Add Category Block Date Range', 'bakkbone-florist-companion'); ?></h4>
							<input type="hidden" name="action" value="bkf_cb_add_range" />
							<?php wp_nonce_field('bkf', 'nonce'); ?>
							<?php
							$args = array(
								"show_option_none" => "Select a category...",
								"option_none_value" => "",
								"value" => "term_id",
								"hierarchical" => true,
								"taxonomy" => "product_cat",
								"show_count" => false,
								"hide_empty" => false,
								"echo" => true,
								"id" => "add-cat-bulk",
								"name" => "category",
								"class" => "regular-text bkf-form-control"
							);
							wp_dropdown_categories($args);
							?>
							<label style="display:inline-block;"><?php esc_html_e('Start Date: ', 'bakkbone-florist-companion'); ?><input type="text" class="bkf-form-control" id="add-date1" name="date1" required /></label>
							<label style="display:inline-block;"><?php esc_html_e('End Date: ', 'bakkbone-florist-companion'); ?><input type="text" class="bkf-form-control" id="add-date2" name="date2" required /></label>
							<input type="submit" value="<?php esc_html_e('Add Dates','bakkbone-florist-companion'); ?>" id="add-submit" class="button button-primary" />
			</form></div>
		<script>
			jQuery(document).ready(function( $ ) {
				jQuery("#add-date, #add-date1, #add-date2").attr( 'readOnly' , 'true' );
				jQuery("#add-date, #add-date1, #add-date2").datepicker( {
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
				  return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
			  }<?php }; ?>
 			 <?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
			  if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 2) {
				   return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
			   }<?php }; ?>
  			 <?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
			   if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 3) {
					return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				}<?php }; ?>
   			 <?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 4) {
					 return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				 }<?php }; ?>
				 <?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
				 if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 5) {
					  return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				  }<?php }; ?>
	 			 <?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
				  if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 6) {
					   return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				   }<?php }; ?>
	  			 <?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
				   if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 0) {
						return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
					}<?php }; ?>

		 for (i = 0; i < closedDatesList.length; i++) {
		   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
		   {
		   	 return [false,"closed","Closed"];
		   }
		 }
		 for (i = 0; i < fullDatesList.length; i++) {
		   if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
		   {
			 return [false,"booked","Fully Booked"];
		   }
		 }
		 return [true];
	 }
		 } );
		</script>

		<script id="bkf_fullcalendar_init">
		  document.addEventListener('DOMContentLoaded', function() {
			var calendarEl = document.getElementById('calendar');
			var calendar = new FullCalendar.Calendar(calendarEl, {
				schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
				eventClick: function(info) {
					if (info.event.extendedProps.function !== null) {
						if (confirm('<?php esc_html_e('Remove block for this date?', 'bakkbone-florist-companion'); ?>')){
							window.location.href = '<?php echo admin_url('admin-ajax.php?action=');?>' + info.event.extendedProps.function + '&id=' + info.event.extendedProps.blockId + '<?php echo '&nonce='.$nonce; ?>';
						}
					}
				},
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
							echo '{ title: \''.$ct.'\', start: \'' . $string . '\', className: \'closedbg\', function: null }, ';
						}
					}

					if(null !== $full){
						foreach($full as $ts => $date){
							$string = wp_date("Y-m-d",$ts);
							echo '{ title: \''.$ft.'\', start: \''.$string.'\', className: \'fullbg\', function: null }, ';
						}

						if(get_option('bkf_dd_setting')['monday'] == false){
							echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'1\', className: \'closedbg\', function: null }, ';
						}
						if(get_option('bkf_dd_setting')['tuesday'] == false){
							echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'2\', className: \'closedbg\', function: null }, ';
						}
						if(get_option('bkf_dd_setting')['wednesday'] == false){
							echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'3\', className: \'closedbg\', function: null }, ';
						}
						if(get_option('bkf_dd_setting')['thursday'] == false){
							echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'4\', className: \'closedbg\', function: null }, ';
						}
						if(get_option('bkf_dd_setting')['friday'] == false){
							echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'5\', className: \'closedbg\', function: null }, ';
						}
						if(get_option('bkf_dd_setting')['saturday'] == false){
							echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'6\', className: \'closedbg\', function: null }, ';
						}
						if(get_option('bkf_dd_setting')['sunday'] == false){
							echo '{ title: \''.$gt.'\', startRecur: "'.bkf_get_monday().'", daysOfWeek: \'0\', className: \'closedbg\', function: null }, ';
						}
					}

			foreach($cb as $thiscb){
				$time = strtotime($thiscb['date']);
				$string = wp_date("Y-m-d",$time);
				echo '{ title: \''.wp_specialchars_decode(addslashes(get_term( $thiscb['category'] )->name)).'\', start: \'' . $string . '\', className: \'uabg\', function: \'bkf_cb_del\', blockId: \''.$thiscb['id'].'\' }, ';

			}

			?>
			]
			});
			calendar.render();
		  });

		</script>
		  <div id="calendar"></div>
		<?php
		} elseif (isset($_GET['section']) && $_GET['section'] == 'ts') {
			$nonce = wp_create_nonce("bkf");
			$allzones = WC_Data_Store::load('shipping-zone');
			$rawzones = $allzones->get_zones();
			$zones = [];
			$zones[] = new WC_Shipping_Zone( 0 );
			foreach($rawzones as $rawzone){
				$zones[] = new WC_Shipping_Zone( $rawzone );
			}

			$sm = [];
			foreach($zones as $zone){
				$methods = $zone->get_shipping_methods();

				foreach($methods as $method){
					$method_is_taxable = $method->is_taxable();
					$method_is_enabled = $method->is_enabled();
					$method_instance_id = $method->get_instance_id();
					$method_title = $method->get_method_title();
					$method_description = $method->get_method_description();
					$method_user_title = $method->get_title();
					$method_rate_id = $method->get_rate_id();
					$sm[] = array(
						'enabled'		=>	$method_is_enabled,
						'taxable'		=>	$method_is_taxable,
						'instanceid'	=>	$method_instance_id,
						'title'			=>	$method_title,
						'description'	=>	$method_description,
						'usertitle'		=>	$method_user_title,
						'rateid'		=>	$method_rate_id
					);
				}
			}

			$days = get_option('bkf_dd_setting');
			$daysoff = get_option('bkf_dm_setting') !== '' ? get_option('bkf_dm_setting') : [];
			$ts = bkf_get_timeslots();

			$tslist = [];

			foreach($days as $day => $on){
				if($on){
					$tslist[$day] = [];
					foreach($ts as $tslot){
						if($day == $tslot['day']){
							$tslist[$day][] = $tslot;
						}
					}
				}
			}

			$daymethods = [];
			foreach($tslist as $day => $slots){
				$methods = [];
				foreach($slots as $thisslot){
					$methods[] = $thisslot['method'];
				}
				$daymethods[$day] = $methods;
			}

		?>
			<h2><?php esc_html_e("Delivery Timeslots","bakkbone-florist-companion") ?></h2>
			<div id="bkf_dd_cb_settings-description"><p><?php esc_html_e('Only weekdays enabled on the Delivery Dates page will be displayed below. Any method within a day with no timeslots added will not require timeslots at checkout.','bakkbone-florist-companion') ?><br><em><?php esc_html_e('Fees are optional - leave fee blank if not required for a timeslot.','bakkbone-florist-companion'); ?></em></p></div>

			<form class="bkf-form" id="addform" action="<?php echo admin_url('admin-ajax.php') ?>">
							<input type="hidden" name="action" value="bkf_ts_add" />
							<?php wp_nonce_field('bkf', 'nonce'); ?>
								<select id="add-day" name="day" class="bkf-form-control" required>
									<option value="" disabled selected><?php esc_html_e('Select a day...','bakkbone-florist-companion'); ?></option>
									<?php
									foreach($days as $day => $on){
										if($on == 1){
											echo '<option value="'.$day.'">'.ucwords($day).'</option>';
										}
									}
										?>
									</select>
							<select disabled class="bkf-form-control" name="method" id="add-method" required>
									<option value="" disabled selected><?php esc_html_e('Select a delivery method...','bakkbone-florist-companion'); ?></option>
							</select>
							<label><?php esc_html_e('Start: ', 'bakkbone-florist-companion'); ?><input type="time" class="bkf-form-control" id="add-start" name="start" required /></label>
							<label><?php esc_html_e('End: ', 'bakkbone-florist-companion'); ?><input type="time" class="bkf-form-control" id="add-end" name="end" required /></label>
							<label><?php esc_html_e('Fee: ', 'bakkbone-florist-companion'); ?><div class="bkf-input-icon"><input type="text" placeholder="***.**" class="bkf-form-control" id="add-fee" name="fee" pattern="\d+\.\d{2,}" /><i><?php bkf_currency_symbol(true); ?></i></div></label>
							<input type="submit" value="<?php esc_html_e('Add Timeslot','bakkbone-florist-companion'); ?>" id="add-submit" class="button button-primary" />
							<div style="max-width:350px;" id="add-error" class="bkf-error bkf-hidden"><p><?php esc_html_e('End time must be greater than start time.','bakkbone-florist-companion'); ?></p></div>
			</form>
						<script id="addform">
							jQuery(function($){
								const startElement = document.getElementById("add-start");
								const endElement = document.getElementById("add-end");
								const dayElement = document.getElementById("add-day");
								const methodElement = document.getElementById("add-method");
								const deliveryMethods = [
									<?php foreach($days as $day => $on){
										if($on == 1){
											echo '{ day: "'.$day.'", methods: [';
												foreach($sm as $smethod){
													if(array_key_exists($day,$daysoff)){
														if(!in_array($smethod['rateid'],$daysoff[$day])){
															echo '{ name: "'.$smethod['title'].' #'.$smethod['instanceid'].': '.$smethod['usertitle'].'", id: "'.$smethod['rateid'].'" },';
														}
													} else {
														echo '{ name: "'.$smethod['title'].' #'.$smethod['instanceid'].': '.$smethod['usertitle'].'", id: "'.$smethod['rateid'].'" },';
													}
												}
											echo '] },';
										}
									} ?>];

								dayElement.addEventListener('change', (event) => {
									var day = dayElement.value;
									if( day !== '' ) {
										methodElement.removeAttribute('disabled');
					  				  jQuery(methodElement).empty($);
									  let defaultOption = new Option('<?php esc_html_e('Select a delivery method...', 'bakkbone-florist-companion'); ?>', '');
									  methodElement.add(defaultOption, 0);
									  methodElement.options[0].disabled = true;
										deliveryMethods.forEach(checkAvail);
										function checkAvail(method){
											if(method.day == day){
												method.methods.forEach(doAvail);
												function doAvail(thismethod){
													let newOption = new Option(thismethod.name, thismethod.id)
													methodElement.add(newOption, undefined);
												}
											}
										}
									}
								});

								startElement.addEventListener('change', (event) => {
									var start = startElement.value;
									var end = endElement.value;
									if( start !== '' && end !== '' && start >= end ) {
										jQuery("#addform").addClass( 'bkf-invalid' );
										jQuery("#addform").removeClass( 'bkf-validated' );
										jQuery("#add-error").removeClass( 'bkf-hidden' );
									} else if(start == '' || end == '') {
										jQuery("#addform").removeClass( 'bkf-invalid' );
										jQuery("#addform").removeClass( 'bkf-validated' );
										jQuery("#add-error").addClass( 'bkf-hidden' );
								  } else {
										jQuery("#addform").addClass( 'bkf-validated' );
										jQuery("#add-error").addClass( 'bkf-hidden' );
									}
								});

								endElement.addEventListener('change', (event) => {
									var start = startElement.value;
									var end = endElement.value;
									if( start !== '' && end !== '' && start >= end ) {
										jQuery("#addform").addClass( 'bkf-invalid' );
										jQuery("#addform").removeClass( 'bkf-validated' );
										jQuery("#add-error").removeClass( 'bkf-hidden' );
									} else if(start == '' || end == '') {
										jQuery("#addform").removeClass( 'bkf-invalid' );
										jQuery("#addform").removeClass( 'bkf-validated' );
										jQuery("#add-error").addClass( 'bkf-hidden' );
								  } else {
										jQuery("#addform").addClass( 'bkf-validated' );
										jQuery("#add-error").addClass( 'bkf-hidden' );
									}
								});
							});
							</script>
			<div style="columns: 500px 3;width:100%;">
				<?php
				foreach($days as $day => $on){
					if($on == 1){?><div class="inside bkf-inside" style="break-inside:avoid;margin-top:0;margin-left:0;margin-right:0;">
						<h2 style="margin-top:0;text-align:center;"><?php echo ucwords($day); ?></h2>
						<?php foreach($sm as $smethod){
							if(in_array($smethod['rateid'], $daymethods[$day])){
								echo '<div class="bkf-form" id="'.$day.'-'.$smethod['instanceid'].'"><p style="margin:0;"><strong>'.$smethod['title'].' #'.$smethod['instanceid'].': </strong>'.$smethod['usertitle'].'</p>';
								foreach($ts as $tslot){
									if($tslot['day'] == $day && $tslot['method'] == $smethod['rateid']){
											echo '<p>'.date("g:i a", strtotime($tslot['start'])).' - '.date("g:i a", strtotime($tslot['end']));
											if($tslot['fee'] !== '' && $tslot['fee'] !== null){
												echo ', '.bkf_currency_symbol().$tslot['fee'];
											}
											echo ' <em><a href="'.admin_url('admin-ajax.php?action=bkf_ts_del&nonce='.$nonce.'&id='.$tslot['id']).'">'.esc_html__('Delete','bakkbone-florist-companion').'</a></em></p>';
										}
									}
									?></div><?php
								}
							}?>
						</div><?php
					}
			}
		} elseif (isset($_GET['section']) && $_GET['section'] == 'ds') {
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

			<h2><?php esc_html_e("Date-Specific Fees","bakkbone-florist-companion") ?></h2>
			<div id="bkf_dd_ds_settings-description"><p><?php esc_html_e("Click a fee on the calendar to delete it.", "bakkbone-florist-companion"); ?></p></div>
				<div class="inside">
					<div class="bkf-form" style="max-width:200px;text-align:center;margin: 12px 0;">
						<form id="add-fee" action="<?php echo $ajaxurl; ?>" />
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
			  <div id='calendar'></div>
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
	}

	function get_settings_for_default_section() {
		$settings = [];
		$settings[] = [
			'title' => __('General Settings', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_dd_general_settings'
		];
		$settings[] = [
			'title'	=> __('Pre-ordering', 'bakkbone-florist-companion'),
			'type'	=> 'number',
			'desc'	=> __('Maximum number of weeks in the future to enable at checkout.', 'bakkbone-florist-companion'),
			'id'	=> 'bkf_ddi_setting[ddi]',
		];
		$settings[] = [
			'title'		=> __('Title', 'bakkbone-florist-companion'),
			'type'		=> 'radio',
			'desc'		=> __('What shall we call this field at checkout?', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_ddi_setting[ddt]',
			'options'	=> [
				__('Delivery Date', 'bakkbone-florist-companion')				=> __('Delivery Date', 'bakkbone-florist-companion'),
				__('Delivery/Collection Date', 'bakkbone-florist-companion')	=> __('Delivery/Collection Date', 'bakkbone-florist-companion'),
				__('Order Date', 'bakkbone-florist-companion')					=> __('Order Date', 'bakkbone-florist-companion'),
				__('Collection Date', 'bakkbone-florist-companion')				=> __('Collection Date', 'bakkbone-florist-companion')
			]
		];
		$settings[] = [
			'title'			=> __('Field label', 'bakkbone-florist-companion'),
			'type'			=> 'text',
			'desc'			=> __('Label between the title and the datepicker field. Leave blank to use the default.', 'bakkbone-florist-companion'),
			'desc_tip'		=> true,
			'id'			=> 'bkf_dd_field_label',
			'placeholder'	=> __("We'll schedule your order for:", 'bakkbone-florist-companion')
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_dd_general_settings'
		];
		return apply_filters( 'bkf_dd_general_settings', $settings );
	}

	function get_settings_for_wd_section() {
		$settings = [];
		$settings[] = [
			'title' => __('Delivery Weekdays', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'desc'	=> __("Select which days of the week you deliver, then set your order lead times. For same day delivery, set the number on the right to 0, and the time to your same day delivery cutoff. To set a lead time, enter the number of days' notice on the right and the time on the cutoff day to close orders. For example, To close orders on Monday afternoon for Wednesday, you might enter 5:00 pm and 2 to close orders at 5pm, 2 days prior.", 'bakkbone-florist-companion'),
			'id'    => 'bkf_dd_wd_settings'
		];
		$weekdays = [
			'monday'	=> __('Monday', 'bakkbone-florist-companion'),
			'tuesday'	=> __('Tuesday', 'bakkbone-florist-companion'),
			'wednesday'	=> __('Wednesday', 'bakkbone-florist-companion'),
			'thursday'	=> __('Thursday', 'bakkbone-florist-companion'),
			'friday'	=> __('Friday', 'bakkbone-florist-companion'),
			'saturday'	=> __('Saturday', 'bakkbone-florist-companion'),
			'sunday'	=> __('Sunday', 'bakkbone-florist-companion')
		];
		foreach ($weekdays as $day => $title) {
			$settings[] = [
				'title'	=> $title,
				'type'	=> 'bkf_weekday',
				'id'	=> $day,
				'value'	=> isset(get_option('bkf_dd_setting')[$day]) ? get_option('bkf_dd_setting')[$day] : false,
				'time'	=> isset(get_option('bkf_sd_setting')[$day]) ? get_option('bkf_sd_setting')[$day] : '',
				'lead'	=> isset(get_option('bkf_sd_setting')[$day.'lead']) ? get_option('bkf_sd_setting')[$day.'lead'] : '0',
			];
		}
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_dd_wd_settings'
		];
		return apply_filters( 'bkf_dd_wd_settings', $settings );
	}

	function get_settings_for_mr_section() {
		$methodslist = bkf_get_shipping_rates();
		$methods = [];
		foreach ($methodslist as &$value) {
			$methods[$value['rateid']] = $value['title'].' #'.$value['instanceid'].' - '.$value['usertitle'];
		}
		$weekdays = [
			'monday'	=> __('Monday', 'bakkbone-florist-companion'),
			'tuesday'	=> __('Tuesday', 'bakkbone-florist-companion'),
			'wednesday'	=> __('Wednesday', 'bakkbone-florist-companion'),
			'thursday'	=> __('Thursday', 'bakkbone-florist-companion'),
			'friday'	=> __('Friday', 'bakkbone-florist-companion'),
			'saturday'	=> __('Saturday', 'bakkbone-florist-companion'),
			'sunday'	=> __('Sunday', 'bakkbone-florist-companion')
		];
		$settings = [];
		$settings[] = [
			'title' => __('Delivery Method Restrictions', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'desc'	=> __("If a particular delivery method is unavailable on a certain day you are otherwise open, indicate this here. Save your weekdays to display the correct fields below. Remember, delivery methods selected below will <strong>not</strong> be available on the day indicated.<br><em>Hold down Ctrl (Windows) or Cmd (Mac) while selecting, to choose multiple options.</em>", 'bakkbone-florist-companion'),
			'id'    => 'bkf_dd_mr_settings'
		];
		foreach ($weekdays as $day => $title) {
			if(!isset(get_option("bkf_dd_setting")[$day]) || !(get_option("bkf_dd_setting")[$day])){
				$attributes = [
					'disabled'	=> 'disabled'
				];
			}
			$settings[] = [
				'title'		=> $title,
				'type'		=> 'multiselect',
				'id'		=> 'bkf_dm_setting['.$day.']',
				'options'	=> $methods,
			];
		}
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_dd_mr_settings'
		];
		return apply_filters( 'bkf_dd_mr_settings', $settings );
	}

	function get_settings_for_fee_section() {
		$weekdays = [
			'monday'	=> __('Monday', 'bakkbone-florist-companion'),
			'tuesday'	=> __('Tuesday', 'bakkbone-florist-companion'),
			'wednesday'	=> __('Wednesday', 'bakkbone-florist-companion'),
			'thursday'	=> __('Thursday', 'bakkbone-florist-companion'),
			'friday'	=> __('Friday', 'bakkbone-florist-companion'),
			'saturday'	=> __('Saturday', 'bakkbone-florist-companion'),
			'sunday'	=> __('Sunday', 'bakkbone-florist-companion')
		];

		$settings = [];
		$settings[] = [
			'title' => __('Taxable Status', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_dd_fee_tax_settings'
		];
		$settings[] = [
			'title'		=> __('Timeslot Fees', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Timeslot Fees are taxable', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_ddf_setting[ddtst]',
			'value'		=> get_option('bkf_ddf_setting')['ddtst'] ? 'yes' : 'no',
		];
		$settings[] = [
			'title'		=> __('Weekday Fees', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Weekday Fees are taxable', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_ddf_setting[ddwft]',
			'value'		=> get_option('bkf_ddf_setting')['ddwft'] ? 'yes' : 'no',
		];
		$settings[] = [
			'title'		=> __('Date-Specific Fees', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Date-Specific Fees are taxable', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_ddf_setting[dddft]',
			'value'		=> get_option('bkf_ddf_setting')['dddft'] ? 'yes' : 'no',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_dd_fee_tax_settings'
		];
		$settings[] = [
			'title' => __('Weekday Fees', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'desc'	=> __("Optionally set an additional fee for delivery on specific weekdays. Leave a row blank to not apply a fee.", 'bakkbone-florist-companion'),
			'id'    => 'bkf_dd_fee_wd_settings'
		];
		foreach ($weekdays as $day => $title) {
			$attr = [
				'pattern'	=> '\d+\.\d{2,}',
			];
			if (!isset(get_option('bkf_dd_setting')[$day]) || !get_option('bkf_dd_setting')[$day]) {
				$attr['disabled'] = 'disabled';
			}
			$settings[] = [
				'title'				=> $title,
				'type'				=> 'text',
				'id'				=> 'bkf_wf_setting['.$day.']',
				'placeholder'		=> '*.**',
				'custom_attributes'	=> $attr
			];
		}
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_dd_fee_wd_settings'
		];
		return apply_filters( 'bkf_dd_fee_settings', $settings );
	}
}

new BKF_Delivery_Date_Settings;