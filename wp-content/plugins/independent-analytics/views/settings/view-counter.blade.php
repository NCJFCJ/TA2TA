<div class="view-counter-settings settings-container">
    <a class="tutorial-link absolute" href="https://independentwp.com/knowledgebase/dashboard/display-view-counter/" target="_blank">
        <?php esc_html_e('Read Tutorial', 'independent-analytics'); ?>
    </a>
    <form method="post" action="options.php">
        <?php settings_fields('iawp_view_counter_settings'); ?>
        <?php do_settings_sections('independent-analytics-view-counter-settings'); ?>
        <div class="shortcode-note">
            <h3><?php esc_html_e('Using the shortcode','independent-analytics'); ?></h3>
            <p><?php esc_html_e('You can output the view counter in a custom location using the shortcode:', 'independent-analytics'); ?></p>
            <p><code>[iawp_view_counter label="Views:" icon="1"]</code></p>
            <p><?php printf(
                esc_html__('Use %1$s to hide the icon and %2$s to hide the label.', 'independent-analytics'),
                '<code>icon="0"</code>',
                '<code>label=""</code>'
            ); ?></p>
        </div>
        <?php submit_button(__('Save Settings', 'independent-analytics'), 'iawp-button purple', 'save-view-counter-settings'); ?>
    </form>
</div>