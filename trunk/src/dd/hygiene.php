<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Data_Hygiene
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Data_Hygiene{

	function __construct(){
		global $bkf_dd_cb_db_version;
		global $bkf_dd_sd_ms_db_version;
		global $bkf_dd_ts_db_version;
		global $bkf_dd_db_version;
		
		$bkf_dd_cb_db_version = '1.0';
		$bkf_dd_sd_ms_db_version = '2';
		$bkf_dd_ts_db_version = '1.3';
		$bkf_dd_db_version = '1.0';

		add_action("init", [$this, 'schedule_purges']);
		add_action("bkf_dd_purge", [$this, 'bkfDdPurge']);
		add_action("bkf_cb_purge", [$this, 'bkfCbPurge']);
		add_action("plugins_loaded", [$this, "db_checks"]);
		register_activation_hook( __BKF_FILE__, [$this, 'db_init']);
	}

	function schedule_purges() {
		if ( !as_has_scheduled_action( 'bkf_dd_purge' ) ){
			as_schedule_recurring_action( strtotime( 'tomorrow' ), DAY_IN_SECONDS, 'bkf_dd_purge', [], '', true );
		}
		if ( !as_has_scheduled_action( 'bkf_cb_purge' ) ){
			as_schedule_recurring_action( strtotime( 'tomorrow' ), DAY_IN_SECONDS, 'bkf_cb_purge', [], '', true );
		}
	}

	function bkfDdPurge() {
		$closed = get_option('bkf_dd_closed');
		$full = get_option('bkf_dd_full');
		$datefees = get_option('bkf_dd_ds_fees');
		foreach($closed as $ts => $date){
			if($ts < (string)strtotime(wp_date("Y-m-d"))){
				unset($closed[$ts]);
			}
		}
		foreach($full as $ts => $date){
			if($ts < (string)strtotime(wp_date("Y-m-d"))){
				unset($full[$ts]);
			}
		}
		foreach($datefees as $ts => $fee){
			if($ts < (string)strtotime(wp_date("Y-m-d"))){
				unset($datefees[$ts]);
			}
		}
		update_option('bkf_dd_closed', $closed);
		update_option('bkf_dd_full', $full);
		update_option('bkf_dd_ds_fees', $datefees);
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

	function db_init(){
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		
		global $bkf_dd_db_version;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table_name_dd = $wpdb->prefix . 'bkf_dd_blocks';
		$sql_cb = "CREATE TABLE $table_name_dd (
			unix int(9) NOT NULL AUTO_INCREMENT,
			date text NOT NULL,
			type text NOT NULL,
			PRIMARY KEY  (unix)
		) $charset_collate;";
		dbDelta( $sql_cb );
		add_option( 'bkf_dd_db_version', $bkf_dd_db_version );
		
		global $bkf_dd_cb_db_version;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table_name_cb = $wpdb->prefix . 'bkf_dd_catblocks';
		$sql_cb = "CREATE TABLE $table_name_cb (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			category mediumint(9) NOT NULL,
			date text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql_cb );
		add_option( 'bkf_dd_cb_db_version', $bkf_dd_cb_db_version );
		$installed_ver_cb = get_option( "bkf_dd_cb_db_version" );
		if ( $installed_ver_cb != $bkf_dd_cb_db_version ) {
			$table_name_cb = $wpdb->prefix . 'bkf_dd_catblocks';
			$sql_cb = "CREATE TABLE $table_name_cb (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			category mediumint(9) NOT NULL,
			date text NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql_cb );
			update_option( 'bkf_dd_cb_db_version', $bkf_dd_cb_db_version );
		}
		
		global $bkf_dd_sd_ms_db_version;
		$table_name_sd = $wpdb->prefix . 'bkf_dd_sameday_methods';
		$sql_sd = "CREATE TABLE $table_name_sd (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			method tinytext NOT NULL,
			day tinytext NOT NULL,
			cutoff text NOT NULL,
			`leadtime` smallint NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql_sd );
		update_option( 'bkf_dd_sd_ms_db_version', $bkf_dd_sd_ms_db_version );
		
		global $bkf_dd_ts_db_version;
		$table_name_ts = $wpdb->prefix . 'bkf_dd_timeslots';
		$sql_ts = "CREATE TABLE $table_name_ts (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			method tinytext NOT NULL,
			day text(9) NOT NULL,
			start tinytext NOT NULL,
			end tinytext NOT NULL,
			fee tinytext,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql_ts );
		add_option( 'bkf_dd_ts_db_version', $bkf_dd_ts_db_version );
		$installed_ver_ts = get_option( "bkf_dd_ts_db_version" );
		if ( $installed_ver_ts != $bkf_dd_ts_db_version ) {
			$table_name_ts = $wpdb->prefix . 'bkf_dd_timeslots';
			$sql_ts = "CREATE TABLE $table_name_ts (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				method tinytext NOT NULL,
				day text(9) NOT NULL,
				start tinytext NOT NULL,
				end tinytext NOT NULL,
				fee tinytext,
				PRIMARY KEY  (id)
			) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql_ts );
			update_option( 'bkf_dd_ts_db_version', $bkf_dd_ts_db_version );
		}
	}
	
	function db_checks() {
		$weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
		$bkf_dd_setting = get_option('bkf_dd_setting');
		foreach ($weekdays as &$day) {
			if (!isset($bkf_dd_setting[$day])) {
				$bkf_dd_setting[$day] = false;
			}
			if (!isset($bkf_dd_setting[$day.'lead'])) {
				$bkf_dd_setting[$day.'lead'] = 0;
			}
		}
		update_option('bkf_dd_setting', $bkf_dd_setting);
		
		if (! get_option('bkf_ddf_setting')) {
			update_option('bkf_ddf_setting', array(
				'ddtst'	=> false,
				'ddwft'	=> false,
				'dddft'	=> false
			));
		}
		
		global $bkf_dd_db_version;
		if ( get_site_option( 'bkf_dd_db_version' ) != $bkf_dd_db_version ) {
        	if ( get_site_option( 'bkf_dd_db_version', 0 ) < 1 ) {
        	    $closed = get_option('bkf_dd_closed', []);
        	    $full = get_option('bkf_dd_full', []);
        	    
        	    foreach ( $closed as $unix => $date ) {
        	        global $wpdb;
            		$wpdb->insert(
            			$wpdb->prefix.'bkf_dd_blocks',
            			array(
            				'unix'	    =>	$unix,
            				'date'		=>	$date,
            				'type'	    =>	'closed',
            			)
            		);
        	    }
        	    foreach ( $full as $unix => $date ) {
        	        global $wpdb;
            		$wpdb->insert(
            			$wpdb->prefix.'bkf_dd_blocks',
            			array(
            				'unix'	    =>	$unix,
            				'date'		=>	$date,
            				'type'	    =>	'full',
            			)
            		);
        	    }
        	    delete_option('bkf_dd_closed');
        	    delete_option('bkf_dd_full');
        	} else {
        		global $wpdb;
        		$charset_collate = $wpdb->get_charset_collate();
        		
        		global $bkf_dd_db_version;
        		$table_name_dd = $wpdb->prefix . 'bkf_dd_blocks';
        		$sql_cb = "CREATE TABLE $table_name_dd (
        			unix int(9) NOT NULL AUTO_INCREMENT,
        			date text NOT NULL,
        			type text NOT NULL,
        			PRIMARY KEY  (unix)
        		) $charset_collate;";
        		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        		dbDelta( $sql_cb );
        		add_option( 'bkf_dd_db_version', $bkf_dd_db_version );
        	}
    	}
    	
		
		global $bkf_dd_ts_db_version;
		if ( get_site_option( 'bkf_dd_ts_db_version' ) != $bkf_dd_ts_db_version ) {
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			
			global $bkf_dd_ts_db_version;
			$table_name_ts = $wpdb->prefix . 'bkf_dd_timeslots';
			$sql_ts = "CREATE TABLE $table_name_ts (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				method tinytext NOT NULL,
				day text(9) NOT NULL,
				start tinytext NOT NULL,
				end tinytext NOT NULL,
				fee tinytext,
				PRIMARY KEY  (id)
			) $charset_collate;";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql_ts );
			add_option( 'bkf_dd_ts_db_version', $bkf_dd_ts_db_version );
			$installed_ver_ts = get_option( "bkf_dd_ts_db_version" );
			if ( $installed_ver_ts != $bkf_dd_ts_db_version ) {
				$table_name_ts = $wpdb->prefix . 'bkf_dd_timeslots';
				$sql_ts = "CREATE TABLE $table_name_ts (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					method tinytext NOT NULL,
					day text(9) NOT NULL,
					start tinytext NOT NULL,
					end tinytext NOT NULL,
					fee tinytext,
					PRIMARY KEY  (id)
				) $charset_collate;";
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql_ts );
				update_option( 'bkf_dd_ts_db_version', $bkf_dd_ts_db_version );
			}
		}
		
		global $bkf_dd_cb_db_version;
		if ( get_site_option( 'bkf_dd_cb_db_version' ) != $bkf_dd_cb_db_version ) {
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			
			global $bkf_dd_cb_db_version;
			$table_name_cb = $wpdb->prefix . 'bkf_dd_catblocks';
			$sql_cb = "CREATE TABLE $table_name_cb (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			category mediumint(9) NOT NULL,
			date text NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql_cb );
			add_option( 'bkf_dd_cb_db_version', $bkf_dd_cb_db_version );
			$installed_ver_cb = get_option( "bkf_dd_cb_db_version" );
			if ( $installed_ver_cb != $bkf_dd_cb_db_version ) {
				$table_name_cb = $wpdb->prefix . 'bkf_dd_catblocks';
				$sql_cb = "CREATE TABLE $table_name_cb (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				category mediumint(9) NOT NULL,
				date text NOT NULL,
					PRIMARY KEY  (id)
				) $charset_collate;";
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql_cb );
				update_option( 'bkf_dd_cb_db_version', $bkf_dd_cb_db_version );
			}
		}
		
		if ( get_option( 'bkf_dd_sd_ms_db_version', 0 ) < 1 ) {
			global $wpdb;
			global $bkf_dd_sd_ms_db_version;
			$table_name_sd = $wpdb->prefix . 'bkf_dd_sameday_methods';
			$charset_collate = $wpdb->get_charset_collate();
			$sql_sd = "CREATE TABLE $table_name_sd (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			method tinytext NOT NULL,
			day tinytext NOT NULL,
			cutoff text NOT NULL,
			PRIMARY KEY  (id)
			) $charset_collate;";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql_sd );
			update_option( 'bkf_dd_sd_ms_db_version', 1 );
		}
		
		if ( get_option( 'bkf_dd_sd_ms_db_version' ) < 2 ) {
			global $wpdb;
			global $bkf_dd_sd_ms_db_version;
			$table_name_sdl = $wpdb->prefix . 'bkf_dd_sameday_methods';
			$sql_sdl = "ALTER TABLE $table_name_sdl
			ADD COLUMN `leadtime` SMALLINT NOT NULL;
			";
			$wpdb->query( $sql_sdl );
			update_option( 'bkf_dd_sd_ms_db_version', 2 );
		}
	}

}