<label class="column-label" for="iawp_view_counter_position">
    <select name="iawp_view_counter_position" id="iawp_view_counter_position">
        <option value="before" <?php selected($position, 'before', true); ?>><?php esc_html_e('Before the content', 'independent-analytics'); ?></option>
        <option value="after" <?php selected($position, 'after', true); ?>><?php esc_html_e('After the content', 'independent-analytics'); ?></option>
        <option value="both" <?php selected($position, 'both', true); ?>><?php esc_html_e('Before and after the content', 'independent-analytics'); ?></option>
    </select>
</label>
