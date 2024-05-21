<?php
$class = $is_filtered ? 'quick-stats filtered' : 'quick-stats' ?>
<?php $class .= ' total-of-' . count($stats) ?>
<div id="quick-stats" class="<?php echo esc_attr($class); ?>">
    <?php foreach ($stats as $stat): ?>
        <div class="stat <?php echo esc_attr($stat['class']); ?>">
            <div class="metric">
                <span class="metric-name"><?php echo esc_html($stat['title']); ?></span>
                <?php if ($stat['class'] == 'orders' || $stat['class'] == 'net-sales') : ?>
                    <span class="plugin-label"><?php echo iawp_blade()->run('icons.plugins.woo'); ?></span>
                <?php endif; ?>
            </div>
            <div class="values">
                <span class="count" test-value="<?php echo esc_attr(strip_tags($stat['count'])); ?>">
                    <?php echo wp_kses($stat['count'], ['span' => []]); ?>
                    <?php if ($is_filtered) : ?>
                        <span class="unfiltered"> / <?php echo wp_kses($stat['unfiltered'], ['span' => []]); ?></span>
                    <?php endif; ?>
                </span>
            </div>
            <span class="growth">
                <?php
                $normalized = $stat['class'] == 'bounce-rate' ? $stat['growth'] * -1 : $stat['growth'];
        $class              = $stat['growth'] < 0 ? 'down' : '';
        $class .= $normalized < 0 ? ' bad' : '';
        ?>
                <span class="percentage <?php echo esc_attr($class) ?>"
                        test-value="<?php echo esc_attr($stat['growth']); ?>">
                        <span class="dashicons dashicons-arrow-up-alt growth-arrow"></span><?php echo $stat['formatted_growth'] ?>
                </span>
                <span class="period-label"><?php esc_html_e('vs. previous period', 'independent-analytics') ?></span>
            </span>
        </div>
    <?php endforeach; ?>
</div>
