<?php
if (!defined('ABSPATH'))
    exit;

class MembershipTracker
{
    public function __construct()
    {
        $this->load_dependencies();
        $this->initialize_hooks();
    }

    private function load_dependencies()
    {
        require_once plugin_dir_path(__FILE__) . 'class-membership-manager.php';
        require_once plugin_dir_path(__FILE__) . 'class-activation-manager.php';
        require_once plugin_dir_path(__FILE__) . 'class-admin-notice.php';
        require_once plugin_dir_path(__FILE__) . 'class-ajax-handler.php';
    }

    private function initialize_hooks()
    {
        new MembershipManager();
        new ActivationManager();
        new AdminNotice();
        new AjaxHandler();
    }
}
