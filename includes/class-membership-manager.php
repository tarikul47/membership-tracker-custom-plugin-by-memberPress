<?php
if (!defined('ABSPATH'))
    exit;

class MembershipManager
{
    public function __construct()
    {
        add_filter('manage_users_columns', [$this, 'add_membership_column']);
        add_action('manage_users_custom_column', [$this, 'display_membership_dropdown'], 10, 3);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_membership_scripts']);

    }

    public function add_membership_column($columns)
    {
        // we remove memberpress user column
        unset($columns['posts']); // 1 
        unset($columns['mepr_products']); // 1 
        unset($columns['mepr_last_login']); // 2 
        unset($columns['mepr_num_logins']); // 3
        $columns['membership'] = 'Membership';
        return $columns;
    }

    public function display_membership_dropdown($output, $column_name, $user_id)
    {
        if ($column_name == 'membership') {
            $memberships = MeprProduct::get_all();
            $user = new MeprUser($user_id);
            $current_membership_id = '';
            $subscriptions = $user->active_product_subscriptions('ids');

            if (!empty($subscriptions)) {
                $current_membership_id = $subscriptions[0];
            }

            $dropdown = '<select class="membership-dropdown" data-user-id="' . $user_id . '">';
            $dropdown .= !$current_membership_id ? '<option value="">Select Membership</option>' : '<option value="cancel">Cancel Membership</option>';

            foreach ($memberships as $membership) {
                $selected = ($membership->ID == $current_membership_id) ? 'selected' : '';
                $dropdown .= '<option value="' . $membership->ID . '" ' . $selected . '>' . esc_html($membership->post_title) . '</option>';
            }

            $dropdown .= '</select>';
            $output = $dropdown;
        }

        return $output;
    }

    public function enqueue_membership_scripts()
    {
        wp_enqueue_script('membership-assign-script', MEMBERSHIP_TRACKER_PLUGIN_URL . 'assets/js/membership-assign-script.js', [], time(), true);
        wp_localize_script(
            'membership-assign-script',
            'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('membership-assign')
            )
        );

        ?>
        <style>
        th#membership {
    		width: 12%;
		}
           .membership-dropdown {
            width: 160px !important;
			}
        </style>
        <?php
    }
}