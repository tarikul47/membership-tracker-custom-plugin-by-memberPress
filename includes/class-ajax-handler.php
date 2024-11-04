<?php
if (!defined('ABSPATH'))
    exit;

class AjaxHandler
{
    public function __construct()
    {
        add_action('wp_ajax_clear_new_user_activations', [$this, 'clear_new_user_activations']);
        add_action('wp_ajax_assign_membership', [$this, 'assign_membership_to_user']);

    }

    public function assign_membership_to_user()
    {
        check_ajax_referer('membership-assign', 'nonce');

        $user_id = intval($_POST['user_id']);
        $membership_id = $_POST['membership_id'];

        if ($user_id) {
            if ($membership_id === 'cancel') {
                $user = new MeprUser($user_id);
                $active_product_ids = $user->active_product_subscriptions('ids');

                if (!empty($active_product_ids)) {
                    foreach ($active_product_ids as $product_id) {
                        $transactions = MeprTransaction::get_all_complete_by_user_id($user_id, 'created_at DESC', '', false, true, true);
                        foreach ($transactions as $transaction) {
                            if ($transaction->product_id == $product_id) {
                                $transaction = new MeprTransaction($transaction->id);
                                $transaction->destroy();
                            }
                        }
                    }
                    wp_send_json_success(['message' => 'Membership canceled successfully.']);
                } else {
                    wp_send_json_error(['message' => 'No active memberships to cancel.']);
                }
            } else {
                $txn = new MeprTransaction();
                $txn->user_id = $user_id;
                $txn->product_id = intval($membership_id);
                $txn->status = MeprTransaction::$complete_str;
                $txn->store();
                wp_send_json_success(['message' => 'Membership assigned successfully.']);
            }
        } else {
            wp_send_json_error(['message' => 'Failed to assign or cancel membership.']);
        }
        wp_die();
    }
    public function clear_new_user_activations()
    {
        delete_option('new_user_activations');
        wp_send_json_success();
    }

}
