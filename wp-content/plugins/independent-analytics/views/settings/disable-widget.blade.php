<label class="column-label" for="iawp_disable_widget">
    <input type="checkbox" name="iawp_disable_widget"
           id="iawp_disable_widget" <?php checked(true, $value, true); ?>>
    <span><?php esc_html_e('Disable dashboard widget', 'independent-analytics'); ?></span>
    <p class="description"><?php esc_html_e("This hides the dashboard widget that appears in the Dashboard WordPress admin page.", 'independent-analytics'); ?></p>
</label>
