<?php
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BKF_DD_Full extends AutomatorWP_Integration_Trigger {
    
    public $integration = 'bkf';
    public $trigger = 'bkf_dd_full';
    
    public function register() {
        
        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Delivery Date marked as Fully Booked', 'bakkbone-florist-companion' ),
            'select_option'     => __( 'Delivery Date marked as <strong>Fully Booked</strong>', 'bakkbone-florist-companion' ),
            'edit_label'        => __( 'Delivery Date marked as Fully Booked', 'bakkbone-florist-companion' ),
            'log_label'         => __( 'Delivery Date marked as Fully Booked', 'bakkbone-florist-companion' ),
            'action'            => 'updated_option',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => [],
            'tags' => array_merge(
                array(
                    'deliverydate' => array(
                        'label'     => __( 'Delivery Date', 'bakkbone-florist-companion' ),
                        'type'      => 'text',
                        'preview'   => __( 'Tuesday, 26 August 2025', 'bakkbone-florist-companion' ),
                    ),
                ),
                array(
                    'unix' => array(
                        'label'     => __( 'Delivery Date (UNIX)', 'bakkbone-florist-companion' ),
                        'type'      => 'integer',
                        'preview'   => '1756137600',
                    ),
                ),
                )
            )
        );
        
    }
    
    public function listener( $option, $old_value, $value ) {
        
        if ($option == 'bkf_dd_full') {
            $new_options = [];
            $new_options = array_diff($value, $old_value);
            if (!empty($new_options)) {
                foreach ( $new_options as $unix => $date ){
                    automatorwp_trigger_event( array(
                        'trigger'       => $this->trigger,
                        'deliverydate'  => $date,
                        'unix'          => $unix,
                    ) );
                }
            }
        }
        
    }
    
    public function pass_scheduled_trigger( $input ) {
        automatorwp_trigger_event($input);
    }
    
    public function hooks() {
        
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );
        add_filter( 'automatorwp_trigger_tags_replacements', array( $this, 'tags_replacements' ), 10, 4 );
        add_filter( 'automatorwp_get_trigger_tag_replacement_times_no_user_triggers', array( $this, 'add_type_to_array' ) );
        add_filter( 'automatorwp_get_trigger_last_completion_log_no_user_types', array( $this, 'add_type_to_array' ) );
        add_filter( 'automatorwp_get_action_last_completion_log_no_user_types', array( $this, 'add_type_to_array' ) );
        parent::hooks();
        
    }
    
    function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {
        
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }
        
        $log_meta['deliverydate'] = ( isset( $event['deliverydate'] ) ? $event['deliverydate'] : '' );
        $log_meta['unix'] = ( isset( $event['unix'] ) ? $event['unix'] : '' );
        
        return $log_meta;
        
    }
    
    public function log_fields( $log_fields, $log, $object ) {
        
        if( $log->type !== 'trigger' ) {
            return $log_fields;
        }
        
        if( $object->type !== $this->trigger ) {
            return $log_fields;
        }
        
        $log_fields['deliverydate'] = array(
            'name' => __( 'Delivery Date', 'bakkbone-florist-companion' ),
            'desc' => __( 'The date that has been blocked.', 'bakkbone-florist-companion' ),
            'type' => 'text',
        );
        $log_fields['unix'] = array(
            'name' => __( 'Delivery Date (UNIX)', 'bakkbone-florist-companion' ),
            'desc' => __( 'The date that has been blocked, as a UNIX date.', 'bakkbone-florist-companion' ),
            'type' => 'integer',
        );
        
        return $log_fields;
        
    }
    
    public function tags_replacements( $replacements, $trigger, $user_id, $log ) {
        
        global $automatorwp_event;
        
        if( $trigger->type === $this->trigger ) {
            wc_get_logger()->info(wp_json_encode($log));
            if( is_array($automatorwp_event) && isset($automatorwp_event['deliverydate']) && isset($automatorwp_event['unix']) ) {
                $replacements['deliverydate'] = $automatorwp_event['deliverydate'];
                $replacements['unix'] = $automatorwp_event['unix'];
            }
        }
        
        return $replacements;
        
    }
    
    public function add_type_to_array( $no_user_triggers ) {
        
        $no_user_triggers[] = $this->trigger;
        return $no_user_triggers;
        
    }
    
}

new AutomatorWP_BKF_DD_Full();