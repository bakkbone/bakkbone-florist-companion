<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdFeesDS
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdFeesDS{
	
    private $bkf_dd_ds_fees = array();
	
    function __construct(){
		$this->bkf_dd_ds_fees = get_option("bkf_dd_ds_fees");
        add_action("admin_menu", array($this,"bkfeeds_admin_menu"),8.22);
    	add_action("admin_init",array($this,"bkfAddDdFeesInit"));
		add_action("admin_footer",array($this,"bkfFeesAdminFooter"));
		add_action("admin_enqueue_scripts",array($this,"bkfFeesAdminEnqueueScripts"));
    	add_action('wp_ajax_bkf_dd_add_fee', array($this, 'bkf_dd_add_fee') ); 
    	add_action('wp_ajax_bkf_dd_remove_fee', array($this, 'bkf_dd_remove_fee') ); 
	}
	
    function bkfeeds_admin_menu(){
        $admin_page = add_submenu_page(
        "bkf_dd",
        __("Date-Specific Fees","bakkbone-florist-companion"),
        '— ' . __("Date-Specific","bakkbone-florist-companion"),
        "manage_options",
        "bkf_fees_ds",
        array($this, "bkf_fees_ds_settings_page"),
        8.22
        );
		add_action( 'load-'.$admin_page, array($this, 'bkf_dsf_help_tab') );
    }
	
	function bkf_dsf_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_dsf_help';
		$callback = array($this, 'bkf_dsf_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_dsf_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/delivery-dates/fees/date-specific" target="_blank">https://docs.bkbn.au/v/bkf/delivery-dates/fees/date-specific</a>
		<?php
	}
	    
    function bkf_fees_ds_settings_page()
    {
        $bkf_dd_ds_fees = get_option("bkf_dd_ds_fees");
		$feesort = $bkf_dd_ds_fees;
		ksort($feesort);
        $bkf_dd_closed = get_option("bkf_dd_closed");
		$closedsort = $bkf_dd_closed;
		ksort($closedsort);
        $bkf_dd_full = get_option("bkf_dd_full");
		$fullsort = $bkf_dd_full;
		ksort($fullsort);
	    $nonce = wp_create_nonce("add-fee");
		$rnonce = wp_create_nonce("remove-fee");
	    $ajaxurl = admin_url('admin-ajax.php');
		$ct = __("Closed", "bakkbone-florist-companion");
		$gt = __("Closed (Global)", "bakkbone-florist-companion");
		$ft = __("Fully Booked", "bakkbone-florist-companion");
        ?>

        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Date-Specific Fees","bakkbone-florist-companion") ?></h1>
                <div class="inside">
					<div class="bkf-form" style="max-width:200px;text-align:center;margin: 12px auto;">
						<form id="add-closed" action="<?php echo $ajaxurl; ?>" />
							<h4 style="margin:0;"><?php _e('Add Fee', 'bakkbone-florist-companion'); ?></h4>
							<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
							<input type="hidden" name="action" value="bkf_dd_add_fee" />
							<p style="margin:5px 0;"><input type="text" name="date" class="fee-date input-text bkf-form-control" required autocomplete="off" placeholder="<?php _e("Date", "bakkbone-florist-companion"); ?>"/></p>
							<p style="margin:5px 0;"><input class="input-text bkf-form-control" type="text" name="fee" placeholder="***.**" pattern="\d+\.\d{2,}" required /></p>
							<p style="margin:5px 0;"><input class="input-text bkf-form-control" type="text" name="title" placeholder="<?php _e('Fee Title', 'bakkbone-florist-companion'); ?>" required /></p>
							<p style="margin:5px 0;"><input type="submit" class="button button-primary button-large" value="<?php _e('Add Fee', 'bakkbone-florist-companion'); ?>"></p>
						</form>
					</div><?php if(!empty(get_option('bkf_dd_ds_fees'))){ ?>
					<ul style="display:grid;grid-template-columns:auto auto;width:100%;"><?php foreach($feesort as $ts => $ds){
						echo '<li class="bkf-list-group" style="display:flex;flex-direction:row;justify-content:space-between;align-items:center;"><p>' . wp_date("l, j F Y", $ts) . ' - ' . stripslashes($ds['title']) . ": " . get_woocommerce_currency_symbol(get_woocommerce_currency()) . $ds['fee'] . '</p><a style="height:20px;" href="' . $ajaxurl . '?action=bkf_dd_remove_fee&date='.$ts.'&nonce='.$rnonce.'"><div class="bkf-close tooltip"><span class="tooltiptext">'.__('Delete', 'bakkbone-florist-companion').'</span></div></a></li>';
					}; ?>
				</ul><? } ?>
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
						echo '{ title: \''.$array['title'].': '.$array['fee'].'\', start: \'' . $string . '\', className: \'closedbg\' }, ';
					}
				}
				if(null !== $bkf_dd_closed){
					foreach($bkf_dd_closed as $ts => $date){
						$string = wp_date("Y-m-d",$ts);
						echo '{ title: \''.$ct.'\', start: \'' . $string . '\', display: \'background\', className: \'closedbg\' }, ';
					}
				}
				if(null !== $bkf_dd_full){
					foreach($bkf_dd_full as $ts => $date){
						$string = wp_date("Y-m-d",$ts);
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
			  <div class="bkf-box"><div class="inside bkf-inside">
			  <div id='calendar'></div></div></div>
        </div></div>
    	<script id="datepicker">
    	    jQuery(document).ready(function( $ ) {
				$(".fee-date").attr( 'readOnly' , 'true' );
    	        $(".fee-date").datepicker( {
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
					 $jsdate = wp_date('n,j,Y',$ts);
					 if ($i == $len - 1) {
					 echo '['.$jsdate.']';
				 }else{
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
	     for (i = 0; i < feeDatesList.length; i++) {
	       if ((m == feeDatesList[i][0] - 1) && (d == feeDatesList[i][1]) && (y == feeDatesList[i][2]))
	       {
	         return [false,"closed","<?php _e('Fee Applied', 'bakkbone-florist-companion'); ?>"];
	       }
	     }
		 return [true];
	 }
	     } );
    	</script>
        <?php
    }
	
	function bkfAddDdFeesInit()
	{
		register_setting(
			"bkf_dd_ds_fees_group",
			"bkf_dd_ds_fees",
			array('type' => 'array')
		);
	}
	
	function bkfFeesAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_fees_ds")
		{

		}
	}
	
	function bkfFeesAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_fees_ds")
		{
			wp_enqueue_script( 'jquery-ui-datepicker' );
	        wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.13.2/themes/overcast/jquery-ui.css' );
	        wp_enqueue_style( 'jquery-ui' );
		}
	}
	
    function bkf_dd_add_fee(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "add-fee")) {
          exit("No funny business please");
        }
		if(null !== get_option('bkf_dd_ds_fees') && !empty(get_option('bkf_dd_ds_fees'))){
			$option = get_option('bkf_dd_ds_fees');
		}else{
			$option = array();
		}
		$date = $_REQUEST['date'];
		$ts = (string)strtotime($date);
		$fee = $_REQUEST['fee'];
		$title = $_REQUEST['title'];
		$option[$ts] = array(
			'fee' => $fee,
			'title' => $title
		);
		update_option('bkf_dd_ds_fees', $option);
			
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {        
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }
   die();
    }

    function bkf_dd_remove_fee(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "remove-fee")) {
          exit("No funny business please");
        }
		$option = get_option('bkf_dd_ds_fees');
		$ts = $_REQUEST['date'];
		unset($option[$ts]);
		update_option('bkf_dd_ds_fees', $option);
			
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