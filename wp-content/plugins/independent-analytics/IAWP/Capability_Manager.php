<?php

namespace IAWP;

/** @internal */
class Capability_Manager
{
    /**
     * @return array
     */
    public static function all_capabilities() : array
    {
        return ['iawp_read_only_access' => \esc_html__('View analytics', 'independent-analytics'), 'iawp_full_access' => \esc_html__('View analytics and edit settings', 'independent-analytics')];
    }
    public static function reset_capabilities() : void
    {
        $roles = \wp_roles()->roles;
        $edits = [];
        foreach ($roles as $role => $role_details) {
            $edits[$role] = null;
        }
        self::edit_all_capabilities($edits);
    }
    /**
     * Accepts an array of key value pairs where the key is the role and the value is the capability to use
     *
     * @param $capabilities
     * @return void
     */
    public static function edit_all_capabilities($capabilities) : void
    {
        foreach ($capabilities as $role => $cap) {
            $user_role = \get_role($role);
            // For the role, remove all previous capabilities
            foreach (self::all_capabilities() as $capability => $label) {
                $user_role->remove_cap($capability);
            }
            if (!empty($cap)) {
                $user_role->add_cap($cap);
            }
        }
    }
    /**
     * @return bool
     */
    public static function can_view() : bool
    {
        return self::is_admin() || \current_user_can('iawp_read_only_access') || \current_user_can('iawp_full_access');
    }
    /**
     * @return bool
     */
    public static function can_edit() : bool
    {
        return self::is_admin() || \current_user_can('iawp_full_access');
    }
    /**
     * Returns a capability string for admin menu pages. Admins can always see pages. Other roles
     * can only see pages if they have the special capability applied.
     *
     * @return string
     */
    public static function can_view_string() : string
    {
        if (self::is_admin()) {
            return 'manage_options';
        }
        if (self::can_edit()) {
            return 'iawp_full_access';
        }
        if (self::can_view()) {
            return 'iawp_read_only_access';
        }
        return 'manage_options';
    }
    /**
     * @return bool
     */
    public static function is_admin() : bool
    {
        foreach (\wp_get_current_user()->roles as $role) {
            if ($role === 'administrator') {
                return \true;
            }
        }
        return \false;
    }
    public static function white_labeled() : bool
    {
        if (self::is_admin()) {
            return \false;
        }
        return \IAWPSCOPED\iawp()->get_option('iawp_white_label', \false);
    }
}
