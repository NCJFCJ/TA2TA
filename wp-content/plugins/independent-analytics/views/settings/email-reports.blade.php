<div class="email-reports settings-container">
    <div class="heading">
        <h2><?php esc_html_e('Email Report', 'independent-analytics'); ?></h2>
        <a class="tutorial-link" href="https://independentwp.com/knowledgebase/pro/email-reports/" target="_blank">
            <?php esc_html_e('Read Tutorial', 'independent-analytics'); ?>
        </a>
        <div class="pro-tag"><?php esc_html_e('Pro', 'independent-analytics'); ?></div>
    </div>
    <form method='post' action='options.php' id="email-reports-form" class="email-reports-form">
        <input type='hidden' name='option_page' value='iawp_email_report_settings'/>
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="_wp_http_referer"
               value="/wp-admin/admin.php?page=independent-analytics-settings">
        <?php wp_nonce_field('iawp_email_report_settings-options'); ?>
        <div class="inner">
            <p id="next-email" class="next-email <?php echo $is_scheduled ? 'scheduled' : 'no-email'; ?>" 
                data-test-date="<?php echo wp_strip_all_tags($date_comparison); ?>">
                <span class="dashicons dashicons-yes-alt"></span><span class="dashicons dashicons-dismiss"></span> 
                <?php echo wp_kses_post($scheduled_date); ?>
            </p>
            <div class="delivery-interval iawp-section">
                <h3><?php esc_html_e('Delivery Interval', 'independent-analytics'); ?></h3>
                <select id="iawp_email_report_interval" name="iawp_email_report_interval">
                    <option value="monthly" <?php selected($interval, 'monthly', true); ?>><?php esc_html_e('Monthly', 'independent-analytics'); ?></option>
                    <option value="weekly" <?php selected($interval, 'weekly', true); ?>><?php esc_html_e('Weekly', 'independent-analytics'); ?></option>
                    <option value="daily" <?php selected($interval, 'daily', true); ?>><?php esc_html_e('Daily', 'independent-analytics'); ?></option>
                </select>
                <p id="monthly-interval-note" class="interval-note"><?php esc_html_e('The email will be delivered on the 1st of every month.', 'independent-analytics'); ?></p>
                <p id="weekly-interval-note" class="interval-note"><?php esc_html_e('The email will be delivered on the first day of the week (selected in the settings above).', 'independent-analytics'); ?></p>
                <p id="daily-interval-note" class="interval-note"><?php esc_html_e('The email will be delivered every day.', 'independent-analytics'); ?></p>
            </div>
            <div class="delivery-time iawp-section">
                <h3><?php esc_html_e('Delivery Time', 'independent-analytics'); ?></h3>
                <select id="iawp_email_report_time" name="iawp_email_report_time">
                    <?php for ($i = 0; $i < 24; $i++) {
                        $readable_time = new DateTime(date('Y-m-d') . ' ' . $i . ':00:00');
                        $readable_time = $readable_time->format(get_option('time_format')); ?>
                        <option value="<?php echo esc_attr($i); ?>" <?php selected($time, $i, true); ?>><?php echo esc_html($readable_time); ?></option>
                    <?php
                    } ?>
                </select>
            </div>
            <div class="custom-colors iawp-section">
                <h3><?php esc_html_e('Customize the colors', 'independent-analytics'); ?></h3>
                <div class="custom-colors-list">
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Header background', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[0]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[0]); ?>" />
                    </div>
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Header text', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[1]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[1]); ?>" />
                    </div>
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Sub-header background', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[2]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[2]); ?>" />
                    </div>
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Sub-header text', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[3]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[3]); ?>" />
                    </div>
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Bar chart', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[4]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[4]); ?>" />
                    </div>
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Bar chart accent', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[5]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[5]); ?>" />
                    </div>
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Borders', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[6]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[6]); ?>" />
                    </div>
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Metric background', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[7]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[7]); ?>" />
                    </div>
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Outer background', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[8]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[8]); ?>" />
                    </div>
                    <div class="custom-color">
                        <p class="element-name"><?php esc_html_e('Footer background', 'independent-analytics'); ?></p>
                        <input type="text" class="iawp-color-picker" value="<?php echo sanitize_hex_color($input_default[9]); ?>" data-default-color="<?php echo sanitize_hex_color($default_colors[9]); ?>" />
                    </div>
                </div>
                <input type="hidden" id="iawp_email_report_colors" name="iawp_email_report_colors" value="<?php echo implode(',', $input_default); ?>" />
            </div>
            <div class="email-addresses iawp-section">
                <h3><?php esc_html_e('Add new email addresses', 'independent-analytics'); ?></h3>
                <div class="new-address duplicator">
                    <div class="entry">
                        <input class="new-field" type="email" placeholder="name@email.com" value="" />
                        <button class="iawp-button purple duplicate-button"><?php esc_html_e('Add email', 'independent-analytics'); ?></button>
                    </div>
                    <div class="blueprint">
                        <div class="entry">
                            <input type="text" readonly 
                                name="iawp_email_report_email_addresses[]" 
                                id="iawp_email_report_email_addresses[]" 
                                data-option="iawp_email_report_email_addresses" 
                                value="">
                            <button class="remove iawp-button ghost-purple"><?php esc_html_e('Remove email', 'independent-analytics'); ?></button>
                        </div>
                    </div>
                    <p class="error-message empty"><?php esc_html_e('Input is empty', 'independent-analytics'); ?></p>
                    <p class="error-message exists"><?php esc_html_e('This email already exists', 'independent-analytics'); ?></p>
                </div>
                <div class="saved">
                    <h3><?php esc_html_e('Sending to these addresses', 'independent-analytics'); ?></h3>
                    <?php for ($i = 0; $i < count($emails); $i++) : ?>
                        <div class="entry">
                            <input type="email" readonly
                                id="iawp_email_report_email_addresses[<?php echo esc_attr($i); ?>]" 
                                name="iawp_email_report_email_addresses[<?php echo esc_attr($i); ?>]" 
                                data-option="iawp_email_report_email_addresses"
                                value="<?php echo esc_attr($emails[$i]); ?>" />
                                <button class="remove iawp-button ghost-purple"><?php esc_html_e('Remove email', 'independent-analytics'); ?></button>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="save-button-container">
                <?php submit_button(esc_html__('Save settings', 'independent-analytics'), 'save-email iawp-button purple', 'save-email-report-settings', false); ?>
                <button id="preview-email" class="preview-email iawp-button ghost-purple"><span class="dashicons dashicons-visibility"></span> <?php esc_html_e('Preview email', 'independent-analytics'); ?></button>
                <button id="test-email" class="test-email iawp-button ghost-purple"><span class="dashicons dashicons-email-alt2"></span> <?php esc_html_e('Send test email', 'independent-analytics'); ?></button>
                <p class="warning-message"><span class="dashicons dashicons-warning"></span> <?php esc_html_e('Unsaved changes', 'independent-analytics'); ?></p>
            </div>
        </div>
    </form>
</div>
<div id="email-preview-container" class="email-preview-container">
    <div id="email-preview" class="email-preview"></div>
    <button id="close-email-preview" class="close-email-preview"><span class="dashicons dashicons-dismiss"></span></button>
</div>