<div class="iawp-notice <?php echo esc_attr($notice); ?>">
    <div class="icon">
        <span class="dashicons dashicons-warning"></span>
    </div>
    <div class="iawp-message">
        <p><span class="iawp-message-text"><?php echo esc_html($notice_text); ?></span> <a href="<?php echo esc_url($url); ?>" class="link-white" target="_blank"><?php esc_html_e('Learn More', 'independent-analytics'); ?></a></p>
    </div>
    <?php if ($button_text) : ?>
        <div>
            <button id="dismiss-notice" class="iawp-button white"><?php echo esc_html($button_text); ?></button>
        </div>
    <?php endif; ?>
</div>