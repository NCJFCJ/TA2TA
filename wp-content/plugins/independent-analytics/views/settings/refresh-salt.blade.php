<label class="column-label" for="iawp_refresh_salt">
    <input type="checkbox" name="iawp_refresh_salt" id="iawp_refresh_salt" <?php checked(true, $refresh_salt, true); ?>>
    <span><?php esc_html_e('Refresh the visitor salt every day', 'independent-analytics'); ?></span>
    <p class="description">
        <?php echo esc_html__("This improves data privacy at the expense of unique visitor count accuracy.", 'independent-analytics') . ' <a href="https://independentwp.com/knowledgebase/data/refresh-visitor-salt/" target="_blank">'. esc_html__('Learn more.', 'independent-analytics') .'</a>'; ?>
    </p>
</label>
