<?php
/**
 * @author BAKKBONE Australia
 * @package \Delivery_Date\Category_Block
 * @license GNU General Public License (GPL) 3.0
**/

namespace BKF\Delivery_Date;

defined("BKF_EXEC") or die("Ah, sweet silence.");

class Category_Block{

	function __construct(){
		global $bkf_dd_cb_db_version;
		$bkf_dd_cb_db_version = '1.0';
		register_activation_hook( __FILE__, array($this, 'bkf_dd_cbs_db_init'));
		add_action("init", array($this, "bkfScheduleCbPurge"));
		add_action("bkf_cb_purge", array($this, "bkfCbPurge"));
		add_action("admin_menu", array($this,"bkf_admin_menu"),6);
		add_action("admin_init",array($this,"bkfAddDdCbInit"));
		add_action("plugins_loaded",array($this,"bkf_update_db_check"));
	}

	function bkfScheduleCbPurge() {
		if ( false === as_has_scheduled_action( 'bkf_cb_purge' ) ){
			as_schedule_recurring_action( strtotime( 'tomorrow' ), DAY_IN_SECONDS, 'bkf_cb_purge', [], '', true );
		}
	}

	function bkfCbPurge() {
		global $wpdb;
		$cb = [];
		$catblocks = $wpdb->get_results(
			"
				SELECT id, category, date
				FROM {$wpdb->prefix}bkf_dd_catblocks
			"
		);
		foreach($catblocks as $catblock){
			$cb[] = array(
				'id'		=>	$catblock->id,
				'category'	=>	$catblock->category,
				'date'		=>	$catblock->date
			);
		}
		foreach($cb as $cblock){
				if(strtotime($cblock['date']) < time()){
				$wpdb->delete(
				$wpdb->prefix.'bkf_dd_catblocks',
				array(
					'id'	=>	$cblock['id']
				)
			);
			}
		}

	}
	
	function bkf_dd_cbs_db_init(){
		global $wpdb;
		global $bkf_dd_cb_db_version;

		$table_name = $wpdb->prefix . 'bkf_dd_catblocks';
	
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			category mediumint(9) NOT NULL,
			date text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		add_option( 'bkf_dd_cb_db_version', $bkf_dd_cb_db_version );
		
		$installed_ver = get_option( "bkf_dd_cb_db_version" );

		if ( $installed_ver != $bkf_dd_cb_db_version ) {

			$table_name = $wpdb->prefix . 'bkf_dd_catblocks';

			$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			category mediumint(9) NOT NULL,
			date text NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( 'bkf_dd_cb_db_version', $bkf_dd_cb_db_version );
		}		
	}
	
	function bkf_update_db_check() {
		global $bkf_dd_cb_db_version;
		if ( get_site_option( 'bkf_dd_cb_db_version' ) != $bkf_dd_cb_db_version ) {
			global $wpdb;
			global $bkf_dd_cb_db_version;

			$table_name = $wpdb->prefix . 'bkf_dd_catblocks';
	
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			category mediumint(9) NOT NULL,
			date text NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			add_option( 'bkf_dd_cb_db_version', $bkf_dd_cb_db_version );
		
			$installed_ver = get_option( "bkf_dd_cb_db_version" );

			if ( $installed_ver != $bkf_dd_cb_db_version ) {

				$table_name = $wpdb->prefix . 'bkf_dd_catblocks';

				$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				category mediumint(9) NOT NULL,
				date text NOT NULL,
					PRIMARY KEY  (id)
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );

				update_option( 'bkf_dd_cb_db_version', $bkf_dd_cb_db_version );
			}
		}
	}
	
	function bkf_admin_menu(){
		$admin_page = add_submenu_page(
		"bkf_dd",
		__("Product Category Blocks","bakkbone-florist-companion"),
		__("Categories","bakkbone-florist-companion"),
		"manage_woocommerce",
		"bkf_ddcb",
		array($this, "bkf_dd_cb_page"),
		6
		);
		add_action( 'load-'.$admin_page, array($this, 'bkf_cb_help_tab') );
	}
	
	function bkf_cb_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_cb_help';
		$callback = array($this, 'bkf_cb_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_cb_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/dd/categories/" target="_blank">https://plugins.bkbn.au/docs/bkf/dd/categories/</a>
		<?php
	}
	
	function bkf_dd_cb_page(){
		$nonce = wp_create_nonce("bkf");
		
		$product_categories = get_terms(array('taxonomy'=>'product_cat','hide_empty' => false));
		
		$days = get_option('bkf_dd_setting');
		$daysoff = get_option('bkf_dm_setting');
		global $wpdb;
		$cb = [];
		$catblocks = $wpdb->get_results(
			"
				SELECT id, category, date
				FROM {$wpdb->prefix}bkf_dd_catblocks
			"
		);
		foreach($catblocks as $catblock){
			$cb[] = array(
				'id'		=>	$catblock->id,
				'category'	=>	$catblock->category,
				'date'		=>	$catblock->date
			);
		}
		uasort($cb, function($a,$b){
			return strcmp(strtotime($a['date']),strtotime($b['date']));} );
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Product Category Blocks","bakkbone-florist-companion") ?></h1>
			<p style="margin: 0;"><?php esc_html_e('Dates entered below will be unavailable for the relevant product category. Click a block on the calendar to delete it.','bakkbone-florist-companion') ?></p>
			<form class="bkf-form" id="addform" action="<?php echo admin_url('admin-ajax.php') ?>">
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
			</form>
		<script>
			jQuery(document).ready(function( $ ) {
				jQuery("#add-date").attr( 'readOnly' , 'true' );
				jQuery("#add-date").datepicker( {
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
				echo '{ title: \''.get_term( $thiscb['category'] )->name.'\', start: \'' . $string . '\', className: \'uabg\', function: \'bkf_cb_del\', blockId: \''.$thiscb['id'].'\' }, ';
				
			}
			
			?>
			]
			});
			calendar.render();
		  });

		</script>
		  <div id="calendar"></div>
		</div><?php
	}
	
	function bkfAddDdCbInit(){
		register_setting(
			"bkf_dd_cb_group",
			"bkf_dd_catblocks",
			array('type' => 'array')
		);
	}
		
}