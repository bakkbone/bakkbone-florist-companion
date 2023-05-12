<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdBlocks
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

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
		add_action("admin_footer",array($this,"bkfDdbAdminFooter"));
		add_action("admin_enqueue_scripts",array($this,"bkfDdbAdminEnqueueScripts"));
    	add_action('wp_ajax_bkf_dd_add_closed', array($this, 'bkf_dd_add_closed') ); 
    	add_action('wp_ajax_bkf_dd_remove_closed', array($this, 'bkf_dd_remove_closed') ); 
    	add_action('wp_ajax_bkf_dd_add_full', array($this, 'bkf_dd_add_full') ); 
    	add_action('wp_ajax_bkf_dd_remove_full', array($this, 'bkf_dd_remove_full') ); 
    }
	
	function bkfScheduleDdPurge() {
		if ( false === as_has_scheduled_action( 'bkf_dd_purge' ) ){
			as_schedule_recurring_action( strtotime( 'tomorrow' ), DAY_IN_SECONDS, 'bkf_dd_purge', array(), '', true );
		}
	}
	
	function bkfDdPurge() {
		$closed = get_option('bkf_dd_closed');
		$full = get_option('bkf_dd_full');
		foreach($closed as $ts => $date){
			if($ts < (string)strtotime(date("Y-m-d"))){
				unset($closed[$ts]);
				update_option('bkf_dd_closed', $closed);
			}
		}
		foreach($full as $ts => $date){
			if($ts < (string)strtotime(date("Y-m-d"))){
				unset($full[$ts]);
				update_option('bkf_dd_full', $full);
			}
		}
	}

    function bkf_admin_menu(){
        $admin_page = add_submenu_page(
        "bkf_dd",
        __("Blocked Delivery Dates","bakkbone-florist-companion"),
        __("Blocked Dates","bakkbone-florist-companion"),
        "manage_options",
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
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_ddb_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/delivery-dates/blocked-dates" target="_blank">https://docs.bkbn.au/v/bkf/delivery-dates/blocked-dates</a>
		<?php
	}
	
    function bkf_dd_blocks_page()
    {
        $bkf_dd_closed = get_option("bkf_dd_closed");
		$closedsort = $bkf_dd_closed;
		ksort($closedsort);
        $bkf_dd_full = get_option("bkf_dd_full");
		$fullsort = $bkf_dd_full;
		ksort($fullsort);
	    $closednonce = wp_create_nonce("add-closed");
		$rcnonce = wp_create_nonce("remove-closed");
	    $fullnonce = wp_create_nonce("add-full");
		$rfnonce = wp_create_nonce("remove-full");
	    $ajaxurl = admin_url('admin-ajax.php');
		$phtext = __("Date", "bakkbone-florist-companion");
		$ubtext = __("Unblock Date", "bakkbone-florist-companion");
        ?>

        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Delivery Date Blocks","bakkbone-florist-companion") ?></h1><div style="width:100%;display:flex;">
                <div class="inside bkf-inside" style="width:50%;">
					<h2 style="margin-top:0;"><?php _e('Closure Dates', 'bakkbone-florist-companion') ?></h2>
					<div class="bkf-form" style="max-width:200px;text-align:center;margin: 12px auto;">
						<form id="add-closed" action="<?php echo $ajaxurl; ?>" />
							<h4 style="margin:0;"><?php _e('Add Closure Date', 'bakkbone-florist-companion'); ?></h4>
							<input type="hidden" name="nonce" value="<?php echo $closednonce; ?>" />
							<input type="hidden" name="action" value="bkf_dd_add_closed" />
							<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phtext; ?>" autocomplete="off" /></p>
							<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php _e('Add Date as Closed', 'bakkbone-florist-companion'); ?>"></p>
						</form>
					</div><?php if(!empty(get_option('bkf_dd_closed'))){ ?>
					<ul style="display:grid;grid-template-columns:auto auto;width:100%;"><?php foreach($closedsort as $ts => $ds){
						echo '<li class="bkf-list-group" style="display:flex;flex-direction:row;justify-content:space-between;align-items:center;"><p>' . $ds . '</p><a style="height:20px;" href="' . $ajaxurl . '?action=bkf_dd_remove_closed&ts='.$ts.'&nonce='.$rcnonce.'"><div class="bkf-close tooltip"><span class="tooltiptext">'.$ubtext.'</span></div></a></li>';
					}; ?>
				</ul><? } ?>
                </div>
                <div class="inside bkf-inside" style="width:50%;">
					<h2 style="margin-top:0;"><?php _e('Fully Booked Dates', 'bakkbone-florist-companion') ?></h2>
					<div class="bkf-form" style="max-width:200px;text-align:center;margin: 12px auto;">
						<form id="add-full" action="<?php echo $ajaxurl; ?>" />
							<h4 style="margin:0;"><?php _e('Add Fully Booked Date', 'bakkbone-florist-companion'); ?></h4>
							<input type="hidden" name="nonce" value="<?php echo $fullnonce; ?>" />
							<input type="hidden" name="action" value="bkf_dd_add_full" />
							<p style="margin:5px 0;"><input style="margin-left:0;" type="text" name="date" class="closure-date input-text bkf-form-control" required placeholder="<?php echo $phtext; ?>" autocomplete="off" /></p>
							<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php _e('Add Date as Fully Booked', 'bakkbone-florist-companion'); ?>"></p>
						</form>
					</div><?php if(!empty(get_option('bkf_dd_full'))){ ?>
					<ul style="display:grid;grid-template-columns:auto auto;width:100%;"><?php foreach($fullsort as $ts => $ds){
						echo '<li class="bkf-list-group" style="display:flex;flex-direction:row;justify-content:space-between;align-items:center;"><p>' . $ds . '</p><a style="height:20px;" href="' . $ajaxurl . '?action=bkf_dd_remove_full&ts='.$ts.'&nonce='.$rfnonce.'"><div class="bkf-close tooltip"><span class="tooltiptext">'.$ubtext.'</span></div></a></li>';
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
				$(".closure-date").attr( 'readOnly' , 'true' );
    	        $(".closure-date").datepicker( {
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
					 $jsdate = date('n,j,Y',$ts);
					 if ($i == $len - 1) {
					 echo '['.$jsdate.']';    
			 }else{
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
					 $jsdate = date('n,j,Y',$ts);
					 if ($i == $len - 1) {
					 echo '['.$jsdate.']';
				 }else{
					 echo '['.$jsdate.'],';		 	
					 }
					 $i++;
				 };}; ?>];
		 
		 function blockedDates(date) {
			 var m = date.getMonth();
			 var d = date.getDate();
			 var y = date.getFullYear();
			 
			 <?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
				if (date.getDay() == 1) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
				if (date.getDay() == 2) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
				if (date.getDay() == 3) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
				if (date.getDay() == 4) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
				if (date.getDay() == 5) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
				if (date.getDay() == 6) {
				return [false, "closed", '<?php echo $gt; ?>'];
				}<?php }; ?>
				<?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
				if (date.getDay() == 0) {
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
    
	function bkfAddDdBlocksInit()
	{
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
	
	function bkfDdbAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_ddb")
		{

		}
	}
	
	function bkfDdbAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_ddb")
		{
			wp_enqueue_script( 'jquery-ui-datepicker' );
	        wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.13.2/themes/overcast/jquery-ui.css' );
	        wp_enqueue_style( 'jquery-ui' );
		}
	}
	
    function bkf_dd_add_closed(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "add-closed")) {
          exit("No funny business please");
        }
		if(null !== get_option('bkf_dd_closed') && !empty(get_option('bkf_dd_closed'))){
			$option = get_option('bkf_dd_closed');
		}else{
			$option = array();
		}
		$date = $_REQUEST['date'];
		$ts = (string)strtotime($date);
		$option[$ts] = $date;
		update_option('bkf_dd_closed', $option);
			
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {        
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }
   die();
    }

    function bkf_dd_remove_closed(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "remove-closed")) {
          exit("No funny business please");
        }
		$option = get_option('bkf_dd_closed');
		$date = $_REQUEST['ts'];
		unset($option[$date]);
		update_option('bkf_dd_closed', $option);
			
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {        
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }
   die();
    }
	
    function bkf_dd_add_full(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "add-full")) {
          exit("No funny business please");
        }
		if(null !== get_option('bkf_dd_full') && !empty(get_option('bkf_dd_full'))){
			$option = get_option('bkf_dd_full');
		}else{
			$option = array();
		}
		$date = $_REQUEST['date'];
		$ts = (string)strtotime($date);
		$option[$ts] = $date;
		update_option('bkf_dd_full', $option);
			
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {        
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }
   die();
    }

    function bkf_dd_remove_full(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "remove-full")) {
          exit("No funny business please");
        }
		$option = array();
		$option = get_option('bkf_dd_full');
		$date = $_REQUEST['ts'];
		unset($option[$date]);
		update_option('bkf_dd_full', $option);
			
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