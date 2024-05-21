<div class="table-toolbar">
    <div class="button-modal-container" data-controller="columns">   
        <button id="table-column-toggle-button" class="table-column-toggle-button iawp-button" data-action="columns#toggleModal" data-columns-target="modalButton">
            <span class="dashicons dashicons-columns"></span><?php esc_html_e('Toggle Columns', 'independent-analytics'); ?>
        </button>
        <?php echo iawp_blade()->run('tables.column-picker-modal', [
            'columns' => $all_columns
        ]); ?>
    </div><?php
    if ($group_html) {
        echo $group_html;
    }
    ?>
</div>