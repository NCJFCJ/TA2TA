<div class="group-select-container">
    <select id="group-select" class="group-select" data-controller="group" data-action="group#changeGroup">
        <?php foreach($options as $option) : ?>
            <option id="<?php echo esc_attr($option->id()) ?>" 
                value="<?php echo esc_attr($option->id()) ?>"
                <?php selected($option->id(), $group->id(), true); ?>
                data-testid="group-by-<?php echo esc_attr($option->id()) ?>"><?php echo esc_html($option->singular()); ?></option>
        <?php endforeach; ?>
    </select>
    <label><span class="dashicons dashicons-open-folder"></span></label>
</div>