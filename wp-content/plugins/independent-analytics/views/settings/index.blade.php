<div id="iawp-settings" class="iawp-settings settings-container">
    <form method="post" action="options.php">
        <?php
        settings_fields('iawp_settings'); ?>
        <?php do_settings_sections('independent-analytics-settings'); ?>
        <?php submit_button(__('Save Settings', 'independent-analytics'), 'iawp-button purple', 'save-settings'); ?>
    </form>
</div>