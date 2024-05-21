@php /** @var string[] $missing_privileges */ @endphp
<div class="settings-container interrupt-message"
     data-controller="migration-redirect"
>
    <h2><?php esc_html_e('Missing Database Permissions', 'independent-analytics'); ?></h2>
    <p>
        <?php esc_html_e("Your site is missing the following database privileges required to run Independent Analytics:", 'independent-analytics'); ?> 
        {{implode(', ', $missing_privileges)}}
    </p>
    <p>
        <?php esc_html_e('Please follow our tutorial to enable these permissions on your site and analytics tracking will begin right away:', 'independent-analytics'); ?>
        
    </p>
    <p>
    <a href="https://independentwp.com/knowledgebase/common-questions/missing-database-permissions/"
            class="link-purple"
            target="_blank"
            style="text-decoration:underline">
            <span>{{ __('Missing Database Permissions: How to Fix this Error Message', 'independent-analytics') }}</span>
        </a>
</p>
</div>