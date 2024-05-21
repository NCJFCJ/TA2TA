<label class="column-label" for="iawp_view_counter_post_types[<?php echo $counter; ?>]">
    <input type="checkbox" name="iawp_view_counter_post_types[<?php echo $counter; ?>]" id="iawp_view_counter_post_types[<?php echo $counter; ?>]" <?php checked(true, in_array($post_type->name, $saved), true); ?> value="<?php echo $post_type->name; ?>">
    <span><?php echo $post_type->label; ?></span>
</label>