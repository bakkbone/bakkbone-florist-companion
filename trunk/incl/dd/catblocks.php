<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdCBOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdCBOptions{

    function __construct(){
		global $bkf_dd_cb_db_version;
		$bkf_dd_cb_db_version = '1.0';
		register_activation_hook( __FILE__, array($this, 'bkf_dd_cbs_db_init'));
        add_action("admin_menu", array($this,"bkf_admin_menu"));
        add_action("admin_init",array($this,"bkfAddDdCbInit"));
		add_action("admin_footer",array($this,"bkfDdcbAdminFooter"));
		add_action("admin_enqueue_scripts",array($this,"bkfDdcbAdminEnqueueScripts"));
		add_action("plugins_loaded",array($this,"bkf_update_db_check"));
    	add_action('wp_ajax_bkf_cb_add', array($this, 'bkf_cb_add') ); 
    	add_action('wp_ajax_bkf_cb_del', array($this, 'bkf_cb_del') ); 
    }
	
	function bkf_dd_ts_db_init(){
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
        add_submenu_page(
        "bkf_options",//parent slug
        __("Product Category Blocks","bakkbone-florist-companion"),//page title
        '— ' . __("Categories","bakkbone-florist-companion"),//menu title
        "manage_options",//capability
        "bkf_ddcb",//menu slug
        array($this, "bkf_dd_cb_page"),//callback
        32
        );
    }

    function bkf_dd_cb_page()
    {
		date_default_timezone_set(wp_timezone_string());
		$addnonce = wp_create_nonce("cb-add");
		$delnonce = wp_create_nonce("cb-del");
		
		$product_categories = get_terms(array('taxonomy'=>'product_cat','hide_empty' => false));
		
		$days = get_option('bkf_dd_setting');
		$daysoff = get_option('bkf_dm_setting');
		global $wpdb;
		$cb = array();
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
            <h1><?php _e("Product Category Blocks","bakkbone-florist-companion") ?></h1>
			<p><?php _e('Dates entered below will be unavailable for the relevant product category.','bakkbone-florist-companion') ?></p>
			<form class="bkf-form" id="addform" action="<?php echo admin_url('admin-ajax.php') ?>">
							<input type="hidden" name="action" value="bkf_cb_add" />
							<input type="hidden" name="nonce" value="<?php echo $addnonce; ?>" />
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
							<label><?php _e('Date: ', 'bakkbone-florist-companion'); ?><input type="text" class="bkf-form-control" id="add-date" name="date" required /></label>
							<input type="submit" value="<?php _e('Add Date','bakkbone-florist-companion'); ?>" id="add-submit" class="button button-primary" />
			</form>
			<div style="display:grid;grid-template-columns:auto auto;width:100%;">
                <?php
				foreach($product_categories as $thiscat){
					$catarray = $thiscat->to_array();
				?><div class="inside bkf-inside">
					<h2 style="margin-top:0;text-align:center;"><?php echo $catarray['name']; ?></h2>
					<?php foreach($cb as $thiscblock){
							if($thiscblock['category'] == $catarray['term_id']){
									echo '<p>'.$thiscblock['date'].' <em><a href="'.admin_url('admin-ajax.php?action=bkf_cb_del&nonce='.$delnonce.'&id='.$thiscblock['id']).'">'.__('Delete','bakkbone-florist-companion').'</a></em>';
									echo '</p>';
							}else{}
						}
						?></div><?php
					
			?>
				<?php
		}
		?></div>
	    <script>

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
							$string = date("Y-m-d",$ts);
							echo '{ title: \''.$ct.'\', start: \'' . $string . '\', className: \'closedbg\' }, ';
						}
					}
				
					if(null !== $full){
						foreach($full as $ts => $date){
							$string = date("Y-m-d",$ts);
							echo '{ title: \''.$ft.'\', start: \''.$string.'\', className: \'fullbg\' }, ';
						}
					
						if(get_option('bkf_dd_setting')['monday'] == false){
							echo '{ title: \''.$gt.'\', daysOfWeek: \'1\', className: \'closedbg\' }, ';
						}
						if(get_option('bkf_dd_setting')['tuesday'] == false){
							echo '{ title: \''.$gt.'\', daysOfWeek: \'2\', className: \'closedbg\' }, ';
						}
						if(get_option('bkf_dd_setting')['wednesday'] == false){
							echo '{ title: \''.$gt.'\', daysOfWeek: \'3\', className: \'closedbg\' }, ';
						}
						if(get_option('bkf_dd_setting')['thursday'] == false){
							echo '{ title: \''.$gt.'\', daysOfWeek: \'4\', className: \'closedbg\' }, ';
						}
						if(get_option('bkf_dd_setting')['friday'] == false){
							echo '{ title: \''.$gt.'\', daysOfWeek: \'5\', className: \'closedbg\' }, ';
						}
						if(get_option('bkf_dd_setting')['saturday'] == false){
							echo '{ title: \''.$gt.'\', daysOfWeek: \'6\', className: \'closedbg\' }, ';
						}
						if(get_option('bkf_dd_setting')['sunday'] == false){
							echo '{ title: \''.$gt.'\', daysOfWeek: \'0\', className: \'closedbg\' }, ';
						}
					}
			
			foreach($cb as $thiscb){
				$time = strtotime($thiscb['date']);
				$string = date("Y-m-d",$time);
				echo '{ title: \''.get_term( $thiscb['category'] )->name.'\', start: \'' . $string . '\', className: \'uabg\' }, ';
				
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
    
	function bkfAddDdCbInit()
	{
		register_setting(
			"bkf_dd_cb_group",// group
			"bkf_dd_catblocks", //setting name
			array('type' => 'array') //sanitize_callback
		);
	}
	
	function bkfDdcbAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_ddcb")
		{

		}
	}
	
	function bkfDdcbAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "florist-options_page_bkf_ddcb")
		{
		
		}
	}
	
	function bkf_cb_add(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "cb-add")) {
          exit("No funny business please");
        }
		$date = $_REQUEST['date'];
		$category = $_REQUEST['category'];
		
		global $wpdb;
		$wpdb->insert(
		$wpdb->prefix.'bkf_dd_catblocks',
		array(
			'date'	=>	$date,
			'category'=>$category
		)
	);
			
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {        
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }
   die();		
	}

	function bkf_cb_del(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "cb-del")) {
          exit("No funny business please");
        }
		$id = $_REQUEST['id'];
		
		global $wpdb;
		$wpdb->delete(
		$wpdb->prefix.'bkf_dd_catblocks',
		array(
			'id'	=>	$id
		)
	);
			
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {        
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }
   die();		
	}
	
}