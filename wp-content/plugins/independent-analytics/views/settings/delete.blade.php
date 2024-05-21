<div data-controller="reset-analytics delete-data" class="export-settings settings-container">
    <div class="heading">
        <h2><?php esc_html_e('Danger zone', 'independent-analytics'); ?></h2>
        <a class="tutorial-link" href="https://independentwp.com/knowledgebase/data/delete-all-data/" target="_blank">
            <?php esc_html_e('Read Tutorial', 'independent-analytics'); ?>
        </a>
    </div>
    <div class="button-group">
        <button id="reset-analytics-button" data-action="click->reset-analytics#open"
                class="iawp-button ghost-red">
            <?php esc_html_e('Reset analytics', 'independent-analytics'); ?>
        </button>
        <button id="delete-everything-button" data-action="click->delete-data#open"
                class="iawp-button ghost-red">
            <?php esc_html_e('Delete all data & deactivate plugin', 'independent-analytics'); ?>
        </button>
    </div>
    <div id="reset-analytics-modal" aria-hidden="true" class="mm micromodal-slide">
        <div tabindex="-1" class="mm__overlay" data-action="click->reset-analytics#close">
            <div role="dialog" aria-modal="true" aria-labelledby="reset-analytics-modal-title"
                 class="mm__container">
                <h1><?php esc_html_e('Reset analytics', 'independent-analytics'); ?></h1>
                <p>
                    <?php esc_html_e('You are about to reset your analytics. This will delete all of the data and return all stats to zero.', 'independent-analytics'); ?>
                </p>
                <p><?php printf(esc_html__('Type "%s" in the input below to confirm.', 'independent-analytics'), 'Reset analytics'); ?></p>
                <form data-action="submit->reset-analytics#submit">
                    <input type="text" autofocus data-reset-analytics-target="input"
                           data-action="input->reset-analytics#updateConfirmation" class="block-input">
                    <button type="submit" class="iawp-button purple"
                            data-reset-analytics-target="submit"><?php esc_html_e('Reset analytics', 'independent-analytics'); ?></button>
                    <button type="button" class="iawp-button ghost-purple"
                            data-micromodal-close><?php esc_html_e('Cancel', 'independent-analytics'); ?></button>
                </form>
            </div>
        </div>
    </div>
    <div id="delete-data-modal" aria-hidden="true" class="mm micromodal-slide">
        <div tabindex="-1" class="mm__overlay" data-action="click->delete-data#close">
            <div role="dialog" aria-modal="true" aria-labelledby="delete-data-modal-title"
                 class="mm__container">
                <h1><?php esc_html_e('Delete all data', 'independent-analytics'); ?></h1>
                <p>
                    <?php esc_html_e('You are about to delete all data associated with Independent Analytics. This includes all analytics data and settings.', 'independent-analytics'); ?>
                </p>
                <p>
                    <?php esc_html_e('The plugin will be deactivated immediately after all data is deleted.', 'independent-analytics'); ?>
                </p>
                <p><?php printf(esc_html__('Type "%s" in the input below to confirm.', 'independent-analytics'), 'Delete all data'); ?></p>
                <form data-action="submit->delete-data#submit">
                    <input type="text" autofocus data-delete-data-target="input"
                           data-action="input->delete-data#updateConfirmation" class="block-input">
                    <button type="submit" class="iawp-button purple"
                            data-delete-data-target="submit"><?php esc_html_e('Delete all data', 'independent-analytics'); ?></button>
                    <button type="button" class="iawp-button ghost-purple"
                            data-micromodal-close><?php esc_html_e('Cancel', 'independent-analytics'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
