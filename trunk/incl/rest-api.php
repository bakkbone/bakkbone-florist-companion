<?php

/**
 * @author BAKKBONE Australia
 * @package BkfRestApi
 * @license GNU General Public License (GPL) 3.0
**/


defined("BKF_EXEC") or die("Silent is golden");

class BkfRestApi{


	/**
	 * BkfRestApi:__construct()
	**/
	function __construct()
	{
		// posts
		add_action("rest_api_init", array($this, "bkfDeliverySuburbRestApi"));
		add_action("rest_api_init", array($this, "postRestApi"));
		add_action("rest_api_init", array($this, "pageRestApi"));
		// taxonomies
		add_action("rest_api_init", array($this, "categoryRestApi"));
		add_action("rest_api_init", array($this, "postTagRestApi"));
	}
	/**
	 * BkfRestApi:bkfDeliverySuburbRestApi()
	 * ref: https://developer.wordpress.org/reference/functions/register_rest_field/
	**/
	function bkfDeliverySuburbRestApi(){
	}
	
	/**
	 * BkfRestApi:postRestApi()
	 * ref: https://developer.wordpress.org/reference/functions/register_rest_field/
	**/
	function postRestApi(){
	}
	
	/**
	 * BkfRestApi:pageRestApi()
	 * ref: https://developer.wordpress.org/reference/functions/register_rest_field/
	**/
	function pageRestApi(){
	}
	
	/**
	 * BkfRestApi:categoryRestApi()
	 * ref: https://developer.wordpress.org/reference/functions/register_rest_field/
	**/
	function categoryRestApi(){
	}
	
	/**
	 * BkfRestApi:postTagRestApi()
	 * ref: https://developer.wordpress.org/reference/functions/register_rest_field/
	**/
	function postTagRestApi(){
	}
	
	
	
}
