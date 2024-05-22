<div class="settings-container interrupt-message"
     data-controller="migration-redirect"
>
    <h2><?php esc_html_e('Missing Database Tables', 'independent-analytics'); ?></h2>
    <p>
        <?php esc_html_e('The database tables created by Independent Analytics are missing. If you are in the middle of migrating your site to/from staging, you can ignore this message. If you have deleted the tables on purpose and want to continue using Independent Analytics, please follow these steps:', 'independent-analytics'); ?>
    </p>
    <ol>
        <li><?php printf(__('Visit the hidden %s Options page %s in your dashboard', 'independent-analytics'), '<a href="' . admin_url("options.php") .'" target="_blank" class="link-purple" style="text-decoration:underline;">', '</a>'); ?></li>
        <li><?php printf(esc_html__('Locate the option called "%s"', 'independent-analytics'), 'iawp_db_version'); ?></li>
        <li><?php printf(esc_html__('Change %s to %s and save', 'independent-analytics'), 'iawp_db_version', '0'); ?></li>
        <li><?php esc_html_e('Reload this page and the analytics dashboard will appear', 'independent-analytics'); ?></li>
    </ol>
    <p><?php esc_html_e('If you need further guidance, please follow this tutorial:','independent-analytics'); ?></p>
    <p><a href="https://independentwp.com/knowledgebase/common-questions/missing-database-tables-error/" class="link-purple" target="_blank" style="text-decoration:underline;"><?php esc_html_e('Missing Database Tables: How to Fix this Error','independent-analytics'); ?></a></p>
</div>