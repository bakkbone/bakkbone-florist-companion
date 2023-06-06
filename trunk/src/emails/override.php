<?php

/**
 * @author BAKKBONE Australia
 * @package Bkf_Email_Override
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class Bkf_Email_Override {

	public function __construct() {
		add_filter('email_change_email', array($this, 'email_change_email'), PHP_INT_MAX, 3);
		add_filter('wp_new_user_notification_email_admin', array($this, 'wp_new_user_notification_email_admin'), PHP_INT_MAX, 3);
		add_filter('invited_user_email', array($this, 'invited_user_email'), PHP_INT_MAX, 4);
		add_filter('recovery_mode_email', array($this, 'recovery_mode_email'), PHP_INT_MAX, 2);
		add_filter('password_change_email', array($this, 'password_change_email'), PHP_INT_MAX, 3);
		add_filter('auto_core_update_email', array($this, 'auto_core_update_email'), PHP_INT_MAX, 4);
		add_filter('new_user_email_content', array($this, 'new_user_email_content'), PHP_INT_MAX, 2);
		add_filter('new_admin_email_content', array($this, 'new_admin_email_content'), PHP_INT_MAX, 2);
		add_filter('new_network_admin_email_content', array($this, 'new_network_admin_email_content'), PHP_INT_MAX, 2);
		add_filter('update_welcome_user_email', array($this, 'update_welcome_user_email'), PHP_INT_MAX, 4);
		add_filter('recovery_email_support_info', array($this, 'recovery_email_support_info'), PHP_INT_MAX, 1);
		add_filter('site_admin_email_change_email', array($this, 'site_admin_email_change_email'), PHP_INT_MAX, 3);
		add_filter('auto_plugin_theme_update_email', array($this, 'auto_plugin_theme_update_email'), PHP_INT_MAX, 4);
		add_filter('wp_new_user_notification_email', array($this, 'wp_new_user_notification_email'), PHP_INT_MAX, 3);
		add_filter('network_admin_email_change_email', array($this, 'network_admin_email_change_email'), PHP_INT_MAX, 4);
		add_filter('user_request_action_email_content', array($this, 'user_request_action_email_content'), PHP_INT_MAX, 2);
		add_filter('user_request_action_email_headers', array($this, 'user_request_action_email_headers'), PHP_INT_MAX, 5);
		add_filter('retrieve_password_notification_email', array($this, 'retrieve_password_notification_email'), PHP_INT_MAX, 1);
		add_filter('user_request_confirmed_email_content', array($this, 'user_request_confirmed_email_content'), PHP_INT_MAX, 2);
		add_filter('wp_password_change_notification_email', array($this, 'wp_password_change_notification_email'), PHP_INT_MAX, 3);
	}
	
	function email_change_email($email, $userold, $usernew){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function wp_new_user_notification_email_admin($email, $user, $blogname){
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function invited_user_email($email, $uid, $role, $key){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function recovery_mode_email($email, $url){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function password_change_email($email, $user, $userdata){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function auto_core_update_email($email, $type, $update, $result){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function new_user_email_content($email, $data){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf(
		/* translators: New email address notification email subject. %s: Site title. */
			__( '[%s] Email Change Request', 'bakkbone-florist-companion' ),
			$blogname
		);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email = $wc_email->style_inline($wrapped_message);
		
		return $email;
	}
	
	function new_admin_email_content($email, $data){
		$heading = sprintf(
			/* translators: Email change notification email subject. %s: Network title. */
			__( '[%s] Network Admin Email Change Request' ),
			wp_specialchars_decode( get_site_option( 'site_name' ), ENT_QUOTES )
		);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email = $wc_email->style_inline($wrapped_message);
		
		return $email;
	}
	
	function new_network_admin_email_content($email, $data){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf(
			/* translators: New admin email address notification email subject. %s: Site title. */
			__( '[%s] New Admin Email Address', 'bakkbone-florist-companion' ),
			$blogname
		);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email = $wc_email->style_inline($wrapped_message);
		
		return $email;
	}
	
	function update_welcome_user_email($email, $uid, $pass, $meta){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function recovery_email_support_info($message){
		$message .= ' '.sprintf(__('If the stack trace below references BAKKBONE Florist Companion, you may wish to check the documentation at %s, or contact BAKKBONE Support at %s.','bakkbone-florist-companion'), 'https://plugins.bkbn.au/docs/bkf/', 'https://wordpress.org/support/plugin/bakkbone-florist-companion/');
		
		return $message;
	}
	
	function site_admin_email_change_email($email, $old, $new){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function auto_plugin_theme_update_email($email, $type, $success, $fail){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function wp_new_user_notification_email($email, $user, $blogname){
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function network_admin_email_change_email($email, $old, $new, $nid){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function user_request_action_email_content($email, $data){
		/* translators: Confirm privacy data request notification email subject. 1: Site title, 2: Name of the action. */
		$heading = sprintf( __( '[%1$s] Confirm Action: %2$s', 'bakkbone-florist-companion' ), $data['sitename'], $data['description'] );
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email = $wc_email->style_inline($wrapped_message);
		
		return $email;
	}
	
	function retrieve_password_notification_email($email){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
	function user_request_confirmed_email_content($email, $data){
		$heading = sprintf(
		/* translators: Privacy data request confirmed notification email subject. 1: Site title, 2: Name of the confirmed action. */
		__( '[%1$s] Action Confirmed: %2$s' ),
		$data['sitename'],
		$email['description']
	);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email = $wc_email->style_inline($wrapped_message);
		
		return $email;
	}
	
	function wp_privacy_personal_data_email_content($email, $rid, $data){
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		/* translators: Personal data export notification email subject. %s: Site title. */
		$heading = sprintf( __( '[%s] Personal Data Export', 'bakkbone-florist-companion' ), $blogname );
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email = $wc_email->style_inline($wrapped_message);
		
		return $email;
	}
	
	function wp_password_change_notification_email($email, $user, $blogname){
		$heading = sprintf($email['subject'], $blogname);
		$mailer = WC()->mailer();
		$wrapped_message = $mailer->wrap_message($heading, $email['message']);
		$wc_email = new WC_Email;
		$email['message'] = $wc_email->style_inline($wrapped_message);
		$email['headers'] = array('Content-Type: text/html; charset=UTF-8');
		
		return $email;
	}
	
}