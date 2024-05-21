<label class="column-label" for="iawp_track_authenticated_users">
    <input type="checkbox" name="iawp_track_authenticated_users"
           id="iawp_track_authenticated_users" <?php checked(true, $track_authenticated_users, true); ?>>
    <span><?php esc_html_e('Track logged-in users', 'independent-analytics'); ?></span>
</label>
