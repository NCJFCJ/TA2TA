<div class="export-settings settings-container">
        <div class="heading">
        <h2><?php esc_html_e('Export Data to CSV', 'independent-analytics'); ?></h2>
        <a class="tutorial-link" href="https://independentwp.com/knowledgebase/data/import-export-data/" target="_blank">
            <?php esc_html_e('Read Tutorial', 'independent-analytics'); ?>
        </a>
    </div>
    <p><?php esc_html_e('Export all historical data to CSV.', 'independent-analytics'); ?></p>
    <div class="button-group">
        <button id="iawp-export-views"
                class="iawp-button ghost-purple"><?php esc_html_e('Export Pages', 'independent-analytics'); ?></button>
        <button id="iawp-export-referrers"
                class="iawp-button ghost-purple"><?php esc_html_e('Export Referrers', 'independent-analytics'); ?></button>
        <button id="iawp-export-geo"
                class="iawp-button ghost-purple"><?php esc_html_e('Export Geolocations', 'independent-analytics'); ?></button>
        <button id="iawp-export-devices"
                class="iawp-button ghost-purple"><?php esc_html_e('Export Devices', 'independent-analytics'); ?></button>
        <?php if (iawp_is_pro()): ?>
            <button id="iawp-export-campaigns"
                    class="iawp-button ghost-purple"><?php esc_html_e('Export Campaigns', 'independent-analytics'); ?></button>
        <?php endif; ?>
    </div>
</div>