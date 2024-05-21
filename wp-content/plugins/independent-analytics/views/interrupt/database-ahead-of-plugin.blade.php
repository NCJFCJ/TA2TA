<div class="settings-container interrupt-message"
     data-controller="migration-redirect"
>
    <h2><?php esc_html_e('Newer database version found', 'independent-analytics'); ?></h2>
    <p>
        <?php esc_html_e("It looks like Independent Analytics was rolled back to an older version not compatible with the current version of the database. Unfortunately, it is not possible to revert the database to an older version without causing data loss. Please update Independent Analytics to the latest version available.", 'independent-analytics'); ?>
    </p>
    <p>
        <?php printf(esc_html__('If something went wrong with the latest update, please reach out to us via the %1$sContact Us%2$s menu to the left or by emailing us directly at %3$s.', 'independent-analytics'), '<strong>', '</strong>', '<strong>support@independentwp.com</strong>'); ?>
    </p>
</div>