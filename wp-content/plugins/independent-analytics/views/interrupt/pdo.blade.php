<div class="settings-container interrupt-message"
     data-controller="migration-redirect"
>
    <h2><?php esc_html_e('Missing PDO extension', 'independent-analytics'); ?></h2>
    <p>
        <?php echo esc_html__('Your site needs the PDO PHP module enabled for Independent Analytics to run. You can ask your web host to enable this for you, or follow our guide to enable it yourself:', 'independent-analytics'); ?>
    </p>
    <p>
        <a href="https://independentwp.com/knowledgebase/common-questions/pdo-error/"
            class="iawp-button purple"
            target="_blank">
            <span class="dashicons dashicons-sos"></span>
            <span>{{ __('Follow Tutorial', 'independent-analytics') }}</span>
        </a>
    </p>
</div>