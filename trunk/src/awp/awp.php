<?php

final class AutomatorWP_Integration_BKF {

    private static $instance;

    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_BKF();
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->hooks();
        }

        return self::$instance;
    }

    private function constants() {
        define( 'AUTOMATORWP_BKF_VER', '1.0.0' );

        define( 'AUTOMATORWP_BKF_FILE', __FILE__ );

        define( 'AUTOMATORWP_BKF_DIR', plugin_dir_path( __FILE__ ) );

        define( 'AUTOMATORWP_BKF_URL', plugin_dir_url( __FILE__ ) );
    }

    private function includes() {

        if( $this->meets_requirements() ) {

            require_once AUTOMATORWP_BKF_DIR . 'triggers/dd_full.php';
            require_once AUTOMATORWP_BKF_DIR . 'triggers/dd_closed.php';
        }
    }

    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );
        
    }

    function register_integration() {

        automatorwp_register_integration( 'bkf', array(
            'label' => 'FloristPress',
            'icon'  => __BKF_URL__ . 'assets/img/floristpress_awp.svg',
        ) );

    }

    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! defined( '__BKF_VERSION__' ) ) {
            return false;
        }

        return true;

    }

}

function AutomatorWP_Integration_BKF() {
    return AutomatorWP_Integration_BKF::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_BKF' );
