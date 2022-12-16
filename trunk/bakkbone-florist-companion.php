<?php

/**
 * Plugin Name: BAKKBONE Florist Companion
 * Plugin URI: https://www.floristwebsites.au/
 * Description: Provides standardised features for floristry websites.
 * Version: 1.1.1
 * Requires at least: 5.0
 * Requires PHP: 7.3
 * Author: BAKKBONE Australia
 * Author URI: https://www.bakkbone.com.au/
 * License: GNU General Public License (GPL) 3.0 or later
 * License URI: https://www.gnu.org/licenses/gpl.html
 * Text Domain: bakkbone-florist-companion
**/


// If this file is called directly, abort.
if (!defined("WPINC")){
	die;
}


/**
 * Silent is golden
**/
define("BKF_EXEC",true);


/**
 * Debug
**/
define("BKF_DEBUG",false);


/**
 * Plugin File
**/
define("BKF_FILE",__FILE__);


/**
 * Plugin Path
**/
define("BKF_PATH",dirname(__FILE__));


/**
 * Plugin Base URL
**/
define("BKF_URL",plugins_url("/",__FILE__));

require BKF_PATH . "/incl/setup.php";
require BKF_PATH . "/incl/custom-posts.php";
require BKF_PATH . "/incl/enqueue-styles.php";
require BKF_PATH . "/incl/plugin-options.php";
require BKF_PATH . "/incl/short-codes.php";
require BKF_PATH . "/incl/admin-notices.php";
require BKF_PATH . "/incl/rest-api.php";
require BKF_PATH . "/incl/fields.php";
require BKF_PATH . "/incl/order-status.php";
require BKF_PATH . "/incl/petals.php";
require BKF_PATH . "/incl/order-statuses/delivered-email.php";


/**
 * Begins execution of the plugin.
**/
function run_bakkbone_florist_companion()
{
	$plugin = new BakkboneFloristCompanion();  
}

run_bakkbone_florist_companion();
