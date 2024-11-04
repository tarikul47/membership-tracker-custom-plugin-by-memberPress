<?php
if (!defined('ABSPATH'))
    exit;

class ActivationManager
{
    public function __construct()
    {
        add_action('user_register', [$this, 'add_pending_activation_meta']);
        add_action('bp_core_activated_user', [$this, 'handle_user_activation']);
    }

    public function add_pending_activation_meta($user_id)
    {
        update_user_meta($user_id, '_pending_activation', true);
    }

    public function handle_user_activation($user_id)
    {
        if (get_user_meta($user_id, '_pending_activation', true)) {
            delete_user_meta($user_id, '_pending_activation');
            $new_activations = get_option('new_user_activations', []);
            $new_activations[] = $user_id;
            update_option('new_user_activations', $new_activations);
        }
    }
}
