<div id="column-picker" class="column-picker" data-columns-target="modal">
    <div class="title-small">
        <?php
        esc_html_e('Toggle Table Columns', 'independent-analytics'); ?>
    </div>
    <?php
    foreach ($columns as $column): 
        $is_wc_disabled = $column->requires_woocommerce() && (iawp_is_free() || !iawp_using_woocommerce()) ? true : false;
        if ($column->id() === 'wc_orders'): ?>
            <p class="title-small wc-title">
                <?php
                esc_html_e('WooCommerce', 'independent-analytics') ?>
                <?php
                if (iawp_is_free()) : ?>
                    <span class="pro-label"><?php
                        esc_html_e('PRO', 'independent-analytics') ?></span>
                <?php endif; ?>
            </p>
        <?php endif; ?>
        <label class="column-label <?php echo esc_attr($is_wc_disabled ? 'disabled' : ''); ?>"
                for="iawp-column-<?php echo esc_attr($column->id()); ?>">
            <input id="iawp-column-<?php
            echo esc_attr($column->id()); ?>"
                <?php
                if ($is_wc_disabled) { ?>
                    <?php
                    checked(true, false, true); ?>
                    disabled="disabled"
                    data-locked-behind-pro="true"
                <?php
                } else { ?>
                    <?php
                    checked(true, $column->visible(), true); ?>
                    data-columns-target="checkbox"
                    data-action="columns#check"
                <?php } ?>
                    type="checkbox"
                    name="<?php
                    echo esc_attr($column->id()) ?>"
                    data-test-visibility="<?php
                    echo $column->visible() ? 'visible' : 'hidden'; ?>"
            >
            <span><?php
                echo esc_html($column->label()); ?></span>
        </label>
    <?php
    endforeach ?>
</div>