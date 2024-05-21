<select name="iawp_dow" id="iawp_dow" value="<?php echo esc_attr($day_of_week); ?>">
    <?php foreach ($days as $index => $day): ?>
        <option value="<?php echo esc_attr($index); ?>" <?php selected($index, $day_of_week, true); ?>>
            <?php echo esc_html($day); ?>
        </option>
    <?php endforeach ?>
</select>
