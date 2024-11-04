<?php
/*
Plugin Name: Membership Tracker
Plugin URI: mailto:tarikul47@gmail.com
Description: Tracks and manages user memberships with MemberPress and BuddyBoss Platform integration.
Version: 1.0.0
Author: Tarikul Islam
Author URI: mailto:tarikul47@gmail.com
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

define('MEMBERSHIP_TRACKER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MEMBERSHIP_TRACKER_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Check if BuddyBoss Platform and MemberPress Pro are active
 */
function membership_tracker_check_required_plugins()
{
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');

    // Check if required plugins are active
    if (!is_plugin_active('buddyboss-platform/buddyboss-platform.php') || !is_plugin_active('memberpress/memberpress.php')) {
        // Deactivate Membership Tracker plugin
        deactivate_plugins(plugin_basename(__FILE__));

        // Add admin notice for missing plugins
        add_action('admin_notices', 'membership_tracker_missing_plugins_notice');
    }
}

/**
 * Show admin notice if dependencies are missing
 */
function membership_tracker_missing_plugins_notice()
{
    ?>
    <div class="error notice">
        <p><?php _e('Membership Tracker requires MemberPress Pro to be installed and activated. Please install and activate.', 'membership-tracker'); ?>
        </p>
    </div>
    <?php
}

/**
 * Initialize the plugin
 */
function membership_tracker_init()
{
    // Check dependencies every load
    if (!is_plugin_active('memberpress/memberpress.php')) {
        return; // Exit if dependencies are not met
    }

    // Load main plugin class
    require_once MEMBERSHIP_TRACKER_PLUGIN_PATH . 'includes/class-membership-tracker.php';

    // Initialize the plugin
    new MembershipTracker();
}

// Run dependency check on activation
register_activation_hook(__FILE__, 'membership_tracker_check_required_plugins');

// Initialize plugin only if dependencies are met
add_action('plugins_loaded', 'membership_tracker_init');