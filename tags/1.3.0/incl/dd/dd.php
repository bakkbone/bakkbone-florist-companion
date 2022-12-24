<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDd
 * @license GNU General Public License (GPL) 3.0
**/


defined("BKF_EXEC") or die("Silence is golden");

/**
 * BkfDd
**/
class BkfDd{

    private $bkf_dd_setting = array();

    function __construct(){
        $this->bkf_dd_setting = get_option("bkf_dd_setting");
        add_action("admin_menu",array($this,"bkf_admin_menu"));
    }

function bkf_admin_menu(
    "bkf_options",//parent slug
    "Delivery Dates",//page title
    "Delivery Dates",//menu title
    "manage_options",//capability
    "bkf_dd",//menu slug
    array($this, "bkf_dd_settings_page"),//callback
)

function bkf_dd_settings_page()
{
    $this->bkf_dd_setting = get_option("bkf_dd_setting");
    ?>
    <div class="wrap">
        <h1><?php _e("Delivery Date Settings","bakkbone-florist-companion") ?></h1>
        <div class="bkf-box">
            <div class="inside">
                <form method="post" action="options.php">
                    <?php settings_fields("bkf_dd_options_group"); ?>
                    <?php do_settings_sections("bkf-dd"); ?>
                    <?php submit_button(); ?>
                </form>
            </div>
        </div>
    </div>
    <?php
}



}