<div class="blocked-ip-settings settings-container">
    <div class="heading">
        <h2><?php esc_html_e('Block IP Addresses', 'independent-analytics'); ?></h2>
        <a class="tutorial-link" href="https://independentwp.com/knowledgebase/data/how-to-block-ip-addresses/" target="_blank">
            <?php esc_html_e('Read Tutorial', 'independent-analytics'); ?>
        </a>
    </div>
    <p><?php esc_html_e('Block IP addresses from getting tracked in your analytics.', 'independent-analytics'); ?></p>
    <p class="current-ip-status <?php echo $ip_is_blocked ? 'blocked' : 'unblocked'; ?>">
        <?php $ip_is_blocked ? esc_html_e('Your IP address is blocked:', 'independent-analytics') : esc_html_e('Your IP address is not blocked:', 'independent-analytics'); ?>
        <span class="current-ip"><?php echo esc_html($current_ip); ?></span></p>
    <form method='post' action='options.php' id="block-ip-form" class="block-ip-form">
        <input type='hidden' name='option_page' value='iawp_blocked_ip_settings'/>
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="_wp_http_referer"
               value="/wp-admin/admin.php?page=independent-analytics-settings">
        <?php wp_nonce_field('iawp_blocked_ip_settings-options'); ?>
        <div class="inner">
            <div class="block-new-ip duplicator">
                <div class="entry">
                    <input class="new-field" type="text" placeholder="<?php echo '76.98.172.122'; ?>" value="" />
                    <button class="iawp-button purple duplicate-button"><?php esc_html_e('Block New IP', 'independent-analytics'); ?></button>
                </div>
                <div class="blueprint">
                    <div class="entry">
                        <input type="text" readonly 
                            name="iawp_blocked_ips[]" 
                            id="iawp_blocked_ips[]"
                            data-option="iawp_blocked_ips"
                            value="">
                        <button class="remove iawp-button ghost-purple"><?php esc_html_e('Remove IP', 'independent-analytics'); ?></button>
                    </div>
                </div>
                <p class="error-message empty"><?php esc_html_e('Input is empty', 'independent-analytics'); ?></p>
                <p class="error-message exists"><?php esc_html_e('This IP is already blocked', 'independent-analytics'); ?></p>
            </div>
            <div class="saved">
                <h3><?php esc_html_e('Blocked IPs', 'independent-analytics'); ?></h3>
                <?php for ($i = 0; $i < count($ips); $i++): ?>
                    <div class="entry">
                        <input type="text" readonly
                               name="iawp_blocked_ips[<?php echo esc_attr($i); ?>]"
                               id="iawp_blocked_ips[<?php echo esc_attr($i); ?>]"
                               data-option="iawp_blocked_ips"
                               value="<?php echo esc_attr($ips[$i]); ?>">
                        <button class="remove iawp-button ghost-purple"><?php esc_html_e('Remove IP', 'independent-analytics'); ?></button>
                    </div>
                <?php endfor; ?>
                <?php if (count($ips) === 0): ?>
                    <p><?php esc_html_e('No blocked IPs', 'independent-analytics'); ?></p>
                <?php endif; ?>
            </div>
            <div class="save-button-container">
                <?php submit_button(esc_html__('Save IP Addresses', 'independent-analytics'), 'iawp-button purple', 'save-blocked-ip-settings', false); ?>
                <p class="warning-message"><span class="dashicons dashicons-warning"></span> <?php esc_html_e('Unsaved changes', 'independent-analytics'); ?></p>
            </div>
        </div>
    </form>
</div>