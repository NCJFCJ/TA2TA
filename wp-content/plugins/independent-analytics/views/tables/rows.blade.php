<div id="iawp-rows" class="iawp-rows" data-number-of-shown-rows="<?php echo esc_attr($number_of_shown_rows) ?>">
    <?php if ($number_of_shown_rows == 0): ?>
            <!-- No rows -->
        <?php if ($table_name == 'views'): ?>
    <p id="data-error"
       class="data-error"><?php esc_html_e('No views found', 'independent-analytics'); ?></p>
    <?php elseif ($table_name == 'referrers'): ?>
    <p id="data-error"
       class="data-error"><?php esc_html_e('No referrers found', 'independent-analytics'); ?></p>
    <?php elseif ($table_name == 'geo'): ?>
    <p id="data-error"
       class="data-error"><?php esc_html_e('No geographic data found', 'independent-analytics'); ?></p>
    <?php elseif ($table_name == 'devices'): ?>
    <p id="data-error"
       class="data-error"><?php esc_html_e('No device data found', 'independent-analytics'); ?></p>
    <?php elseif ($table_name == 'campaigns'): ?>
    <div class="data-error">

        <p>
                <?php esc_html_e('No campaign data found', 'independent-analytics'); ?>
        </p>
            <?php if(!$has_campaigns): ?>
        <p>
            <a href="?page=independent-analytics-campaign-builder"
               class="iawp-button purple">
                    <?php esc_html_e('Create your first campaign', 'independent-analytics'); ?>
            </a>
        </p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
            <!-- Some rows -->
        <?php foreach ($rows as $index => $row): ?>
        <?php $class = $table_name == 'views' && $row->is_deleted() ? 'iawp-row deleted' : 'iawp-row'; ?>
        <div class="<?php echo esc_attr($class); ?>" <?php echo $table->get_row_data_attributes($row) ?>>
            <?php foreach ($all_columns as $column): ?>
            <?php $class = $column->visible() ? 'cell' : 'cell hide'; ?>
        <div class="<?php echo esc_attr($class); ?>"
             data-column="<?php echo esc_attr($column->id()); ?>"
             data-test-visibility="<?php echo $column->visible() ? 'visible' : 'hidden'; ?>"
        >
            <div class="row-number"><?php echo $index + 1; ?></div>
            <span class="cell-content"><?php echo wp_kses_post($table->get_cell_content($row, $column)); ?></span>
            <span class="animator"></span>
        </div>
        <?php endforeach ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>