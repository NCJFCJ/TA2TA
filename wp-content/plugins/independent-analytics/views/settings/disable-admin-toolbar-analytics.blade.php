<label class="column-label" for="iawp_disable_admin_toolbar_analytics">
    <input type="checkbox" name="iawp_disable_admin_toolbar_analytics"
           id="iawp_disable_admin_toolbar_analytics" <?php checked(true, $value, true); ?>>
    <span><?php esc_html_e('Disable admin toolbar stats', 'independent-analytics'); ?></span>
    <p class="description"><?php esc_html_e("This hides the stats that appear in the admin toolbar when you view a post or page.", 'independent-analytics'); ?></p>
</label>
