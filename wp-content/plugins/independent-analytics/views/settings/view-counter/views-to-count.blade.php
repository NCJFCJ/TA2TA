<label class="column-label" for="iawp_view_counter_views_to_count">
    <select name="iawp_view_counter_views_to_count" id="iawp_view_counter_views_to_count">
        <option value="total" <?php selected($value, 'total', true); ?>><?php esc_html_e('All-time', 'independent-analytics'); ?></option>
        <option value="today" <?php selected($value, 'today', true); ?>><?php esc_html_e('Today', 'independent-analytics'); ?></option>
        <option value="last_thirty" <?php selected($value, 'last_thirty', true); ?>><?php esc_html_e('Last 30 days', 'independent-analytics'); ?></option>
        <option value="this_month" <?php selected($value, 'this_month', true); ?>><?php esc_html_e('This month so far', 'independent-analytics'); ?></option>
        <option value="last_month" <?php selected($value, 'last_month', true); ?>><?php esc_html_e('Last month', 'independent-analytics'); ?></option>
    </select>
</label>
