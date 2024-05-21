<div id="iawp-table-wrapper" class="iawp-table-wrapper">
    <div id="data-table-container" class="data-table-container">
        <div id='data-table'
             class='data-table'
             data-table-name='<?php echo esc_attr($table_name); ?>'
             data-columns='<?php echo \IAWP\Utils\Security::json_encode($all_columns) ?>'
             data-column-count='<?php echo count($all_columns); ?>'
             data-total-number-of-rows {{-- The value is added in JavaScript after page load --}}
             style="min-width: <?php echo absint($visible_column_count) * 170; ?>px; --columns: <?php echo absint($visible_column_count); ?>; --columns-mobile: <?php echo absint($visible_column_count - 1); ?>"
        >

            <!-- Header -->
            <div id="iawp-columns" class="iawp-columns">
                <div class="iawp-row"
                     data-controller="sort"
                >
                    <?php
                    foreach ($all_columns as $column): ?>
                        <?php
                        $cell_class = $column->visible() ? 'cell' : 'cell hide'; ?>
                    <div class="<?php echo esc_attr($cell_class); ?>"
                         data-column="<?php echo esc_attr($column->id()); ?>"
                         data-test-visibility="<?php echo $column->visible() ? 'visible' : 'hidden'; ?>"
                    >
                        <button class="sort-button"
                                data-sort-target="sortButton"
                                data-sort-direction="<?php echo $column->id() === esc_attr($sort_column) ? esc_attr($sort_direction) : '' ?>"
                                data-default-sort-direction="<?php echo esc_attr($column->sort_direction()); ?>"
                                data-sort-column="<?php echo esc_attr($column->id()); ?>"
                                data-action="sort#sortColumnColumn"
                        >
                            <div class="row-number"></div>
                            <span class="name"><?php echo esc_html($column->label()); ?></span>
                            <span class="dashicons dashicons-arrow-right"></span>
                            <span class="dashicons dashicons-arrow-up"></span>
                            <span class="dashicons dashicons-arrow-down"></span>
                            <div class="animator"></div>
                        </button>
                    </div>
                    <?php
                    endforeach ?>
                </div>
            </div>

            <!-- Rows -->
            <?php
            if ($render_skeleton) : ?>
            <div id="iawp-rows" class="iawp-rows rendering">
                    <?php
                foreach (range(1, $page_size) as $index): ?>
                <div class="iawp-row">
                        <?php
                    foreach ($all_columns as $column): ?>
                        <?php
                        $class = $column->visible() ? 'cell' : 'cell hide'; ?>
                    <div class="<?php echo esc_attr($class); ?>"
                         data-column="<?php echo esc_attr($column->id()); ?>"
                         data-test-visibility="<?php echo $column->visible() ? 'visible' : 'hidden'; ?>"
                    >
                        <div class="row-number"></div>
                        <span class="cell-content">
                            <span class="skeleton-loader"></span>
                        </span>
                        <span class="animator"></span>
                    </div>
                    <?php
                    endforeach ?>
                </div>
                <?php
                endforeach ?>
            </div>
            <?php
            else: ?>
            @include('tables.rows')
            <?php
            endif; ?>
        </div>
    </div>

    <div class="pagination">
        <button id="pagination-button" class="iawp-button purple"
                data-report-target="loadMore"
                data-action="report#loadMore"
        >
        <span class="disabled-button-text">
            <?php
            esc_html_e('Showing All Rows', 'independent-analytics'); ?>
        </span>
        <span class="enabled-button-text">
            {{sprintf(__('Load Next %d Rows', 'independent-analytics'), $page_size)}}
        </span>
        </button>
    </div>
</div>
