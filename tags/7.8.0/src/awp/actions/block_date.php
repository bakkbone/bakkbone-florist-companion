<?php
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BKF_Block_Date extends AutomatorWP_Integration_Action {

    public $integration = 'bkf';
    public $action = 'bkf_block_date';

    public function register(){
        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Block a Delivery Date', 'bakkbone-florist-companion' ),
            'select_option'     => __( 'Block a Delivery Date', 'bakkbone-florist-companion' ),
            'edit_label'        => sprintf( __( 'Mark %1$s as %2$s', 'bakkbone-florist-companion' ), '{date}', '{type}' ),
            'log_label'         => sprintf( __( 'Mark %1$s as %2$s', 'bakkbone-florist-companion' ), '{date}', '{type}' ),
            'options'           => array(
                'date'        => [
                    'default'           => __('a date', 'bakkbone-florist-companion'),
                    'fields'            => [
                        'date' => array(
                            'name'          => __( 'Delivery Date:', 'bakkbone-florist-companion' ),
                            'type'          => 'text',
                            'default'       => '',
                            'desc'          => __('This date must be in this specific date format: Tuesday, 26 August 2025', 'bakkbone-florist-companion')
                        ),
                    ]
                ],
                'type'        => [
                    'default'           => __('Fully Booked', 'bakkbone-florist-companion'),
                    'fields'            => [
                        'type' => array(
                            'name'      => __( 'Block Type:', 'bakkbone-florist-companion' ),
                            'type'      => 'select',
                            'default'   => __('Fully Booked', 'bakkbone-florist-companion'),
                            'options_cb'=> [$this, 'dd_types']
                        ),
                    ]
                ]
            )
        ) );
    }
    
    public function dd_types( $field ){
        $options = [
            'full'      => __('Fully Booked', 'bakkbone-florist-companion'),
            'closed'    => __('Closed', 'bakkbone-florist-companion')
        ];
        return $options;
    }
    
    public function execute( $action, $user_id, $action_options, $automation ) {

        $this->result = '';
        
        $type = $action_options['type'];
        $date = $action_options['date'];
        $unix = (string)strtotime($action_options['date']);

        if ( empty( $type ) && empty( $date ) ) {
            $this->result = __( 'Please, select a date to block and a block type.', 'bakkbone-florist-companion' );
            return;
        } else if ( empty( $type ) ) {
            $this->result = __( 'Please, select a block type.', 'bakkbone-florist-companion' );
            return;
        } else if ( empty( $date ) ) {
            $this->result = __( 'Please, select a date to block.', 'bakkbone-florist-companion' );
            return;
        }
        
        bkf_dd_block($unix, $date, $type);
        
        $this->result = sprintf( __( 'The date %1$s has been marked as %2$s.', 'bakkbone-florist-companion' ), $date, $type );
        
    }

    public function hooks() {
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );
        parent::hooks();
    }

    public function log_meta( $log_meta, $action, $user_id, $action_options, $automation ) {
        if( $action->type !== $this->action ) {
            return $log_meta;
        }
        $log_meta['result'] = $this->result;
        return $log_meta;
    }

    public function log_fields( $log_fields, $log, $object ) {

        if( $log->type !== 'action' ) {
            return $log_fields;
        }
        if( $object->type !== $this->action ) {
            return $log_fields;
        }
        $log_fields['result'] = [
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        ];
        return $log_fields;
    }
}

new AutomatorWP_BKF_Block_Date();