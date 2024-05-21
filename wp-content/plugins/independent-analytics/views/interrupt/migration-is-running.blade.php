@extends('interrupt.layout')

@section('content')
    <div class="settings-container interrupt-message"
         data-controller="migration-redirect"
    >
        <div id="iawp-update-running">
            <h2><?php esc_html_e('Update running', 'independent-analytics'); ?></h2>
            <p>
                <?php esc_html_e("We're running an update designed to speed up and improve Independent Analytics.
                This can take anywhere from 5 seconds to 5 minutes.", 'independent-analytics'); ?>
            </p>
            <p>
                <?php esc_html_e("Your site's performance is not impacted by this update. Analytics tracking will resume once the update is complete.", 'independent-analytics'); ?>
            </p>
            <p>
                <strong><?php esc_html_e("This page will automatically refresh when the update's finished.", 'independent-analytics'); ?></strong>
            </p>
            <p><span class="dashicons dashicons-update-alt spin"></span></p>
        </div>
        <div id="iawp-migration-error" class="iawp-migration-error"></div>
    </div>
@endsection
