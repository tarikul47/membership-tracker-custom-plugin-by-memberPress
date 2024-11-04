<?php
if (!defined('ABSPATH'))
    exit;

class AdminNotice
{
    public function __construct()
    {
        add_action('admin_notices', [$this, 'display_admin_notice_on_activation']);
        add_action('admin_menu', [$this, 'show_registration_count_in_menu'], 99);
        add_action('current_screen', [$this, 'clear_activations_on_users_page']);
    }

    public function display_admin_notice_on_activation()
    {
        $new_activations = get_option('new_user_activations', []);
        if (!empty($new_activations)) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>New user\'s activated:</p>';
            foreach ($new_activations as $user_id) {
                $user_info = get_userdata($user_id);
                $profile_link = esc_url(get_edit_user_link($user_id));
                echo '<p>' . esc_html($user_info->display_name) . ' - ' . esc_html($user_info->user_email) . '</p>';
            }
            echo '</div>';
        }
    }

    public function show_registration_count_in_menu($menu)
    {
        $new_activations = get_option('new_user_activations', []);
        $count = count($new_activations);

        if ($count > 0) {
            global $menu;
            foreach ($menu as $key => $item) {
                if ('users.php' === $item[2]) {
                    $menu[$key][0] .= ' <span class="update-plugins count-' . esc_attr($count) . '"><span class="plugin-count">' . esc_html($count) . '</span></span>';
                }
            }
        }
    }

    public function clear_activations_on_users_page()
    {
        $screen = get_current_screen();
        if (isset($screen->id) && 'users' === $screen->id) {
            delete_option('new_user_activations');
        }
    }
}
