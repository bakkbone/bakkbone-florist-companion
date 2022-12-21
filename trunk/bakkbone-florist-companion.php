<?php

/**
 * Plugin Name: BAKKBONE Florist Companion
 * Plugin URI: https://www.floristwebsites.au/
 * Description: Provides standardised features for floristry websites.
 * Version: 1.2.0
 * Requires at least: 5.0
 * Requires PHP: 7.3
 * Author: BAKKBONE Australia
 * Author URI: https://www.bakkbone.com.au/
 * License: GNU General Public License (GPL) 3.0 or later
 * License URI: https://www.gnu.org/licenses/gpl.html
 * Text Domain: bakkbone-florist-companion
**/

if (!defined("WPINC")){
	die;
}

define("BKF_EXEC",true);

define("BKF_DEBUG",false);

define("BKF_FILE",__FILE__);

define("BKF_PATH",dirname(__FILE__));

define("BKF_URL",plugins_url("/",__FILE__));

require BKF_PATH . "/incl/setup.php";
require BKF_PATH . "/incl/custom-posts.php";
require BKF_PATH . "/incl/enqueue-styles.php";
require BKF_PATH . "/incl/plugin-options.php";
require BKF_PATH . "/incl/shortcodes.php";
require BKF_PATH . "/incl/admin-notices.php";
require BKF_PATH . "/incl/rest-api.php";
require BKF_PATH . "/incl/core.php";
require BKF_PATH . "/incl/order-status.php";
require BKF_PATH . "/incl/petals/petals.php";
require BKF_PATH . "/incl/emails/status-email.php";

function run_bakkbone_florist_companion()
{
	$plugin = new BakkboneFloristCompanion();  
}

run_bakkbone_florist_companion();
