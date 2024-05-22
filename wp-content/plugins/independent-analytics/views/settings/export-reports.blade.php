@php /** @var \IAWP\Report_Finder $report_finder */ @endphp

<div class="settings-container export-reports" data-controller="export-reports">
    <div class="heading">
        <h2><?php esc_html_e('Export Custom Reports', 'independent-analytics'); ?></h2>
        <a class="tutorial-link" href="https://independentwp.com/knowledgebase/dashboard/export-import-custom-reports/" target="_blank">
            <?php esc_html_e('Read Tutorial', 'independent-analytics'); ?>
        </a>
    </div>
    <p class="setting-description">{{esc_html_e('Export your custom reports, so you can import them to another website running Independent Analytics.', 'independent-analytics')}}</p>
    <label>
        <input type="checkbox" data-export-reports-target="selectAllCheckbox" data-action="export-reports#handleToggleSelectAll">
        {{__('Select all reports', 'independent-analytics')}}
    </label>

    <div class="reports">
        @foreach($report_finder->fetch_reports_by_type() as $report_type)
            <div>
                <h3>{{$report_type['name']}}</h3>
                <ol>
                    @forelse($report_type['reports'] as $report)
                        <li>
                            <label>
                                <input type="checkbox" name="report_id" value="{{$report->id()}}" data-action="export-reports#handleToggleReport">
                                {{$report->name()}}
                            </label>
                        </li>
                    @empty
                        <li class="empty">
                            <p>No reports found</p>
                        </li>
                    @endforelse
                </ol>
            </div>
        @endforeach
    </div>

    <button class="iawp-button purple" data-export-reports-target="submitButton" data-action="export-reports#export" disabled>{{__('Export Reports', 'independent-analytics')}}</button>
</div>

<div class="settings-container import-reports" data-controller="import-reports" data-import-reports-database-version-value="{{'30'}}">
    <div class="heading">
        <h2><?php esc_html_e('Import Custom Reports', 'independent-analytics'); ?></h2>
        <a class="tutorial-link" href="https://independentwp.com/knowledgebase/dashboard/export-import-custom-reports/" target="_blank">
            <?php esc_html_e('Read Tutorial', 'independent-analytics'); ?>
        </a>
    </div>
    <button class="iawp-button purple" data-import-reports-target="submitButton" data-action="import-reports#import" disabled>{{__('Import Reports', 'independent-analytics')}}</button>
    <input type="file" accept="application/json" data-action="import-reports#handleFileSelected click->import-reports#clearFileInput" data-import-reports-target="fileInput">
    <p data-import-reports-target="warningMessage" style="display:none;"></p>
</div>