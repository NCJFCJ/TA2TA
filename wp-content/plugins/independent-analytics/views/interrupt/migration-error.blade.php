@php /** @var string $plugin_version */ @endphp
@php /** @var string $migration_db_version */ @endphp
@php /** @var ?int $migration_step */ @endphp
@php /** @var ?string $migration_error */ @endphp
@php /** @var ?string $migration_error_query */ @endphp

<h2><?php esc_html_e('Data Migration Failed', 'independent-analytics'); ?></h2>
<p class="get-help"><strong><?php printf(esc_html__('Please send the info below to %s and we will help to resolve this error ASAP. We\'re sorry for the inconvenience.', 'independent-analytics'), 'support@independentwp.com'); ?></strong></p>
<p><strong><?php esc_html_e('Plugin version:', 'independent-analytics'); ?></strong> {{$plugin_version}}</p>
<p><strong><?php esc_html_e('Migration:', 'independent-analytics'); ?></strong> {{$migration_db_version}}</p>
<p><strong><?php esc_html_e('Step:', 'independent-analytics'); ?></strong> {{$migration_step}}</p>
<p><strong><?php esc_html_e('Error message:', 'independent-analytics'); ?></strong></p>
<textarea rows="2" readonly>{{$migration_error}}</textarea>
<p><strong><?php esc_html_e('Failed query:', 'independent-analytics'); ?></strong></p>
<textarea rows="5" readonly>{{$migration_error_query}}</textarea>