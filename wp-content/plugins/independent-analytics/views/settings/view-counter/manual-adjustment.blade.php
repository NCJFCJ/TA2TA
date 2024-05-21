<label class="column-label" for="iawp_view_counter_manual_adjustment">
    <input type="checkbox" name="iawp_view_counter_manual_adjustment" id="iawp_view_counter_manual_adjustment" <?php checked(true, $value, true); ?>>
    <span><?php esc_html_e('Allow manual adjustment', 'independent-analytics'); ?></span>
    <p class="description"><?php esc_html_e('Enables an option in the post editor to manually increase the view count, so you can preserve values from your prior view counter plugin.', 'independent-analytics'); ?></p>
</label>