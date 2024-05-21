<label class="column-label" for="iawp_view_counter_private">
    <input type="checkbox" name="iawp_view_counter_private" id="iawp_view_counter_private" <?php checked(true, $private, true); ?>>
    <span><?php esc_html_e('Make private', 'independent-analytics'); ?></span>
    <p class="description"><?php esc_html_e('Only logged-in visitors will see it.', 'independent-analytics'); ?></p>
</label>
