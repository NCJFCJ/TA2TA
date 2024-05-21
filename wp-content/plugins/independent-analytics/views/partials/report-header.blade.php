@php /** @var \IAWP\Report $report */ @endphp
@php /** @var bool $can_edit */ @endphp

<div id="report-title-bar" class="report-title-bar">
    @if($report->is_saved_report())
        <div data-controller="{{ $can_edit ? "rename-report" : "" }}"
             data-rename-report-id-value="{{$report->id()}}"
             data-rename-report-name-value="{{$report->name()}}"
             class="modal-parent small rename-report">
            <a id="rename-link" class="rename-link {{ !$can_edit ? 'no-edit' : '' }}" href="#"
               data-action="click->rename-report#toggleModal"
               data-rename-report-target="modalButton"
               title="{{$report->name()}}">
                <h1 data-name-for-report-id="{{$report->id()}}"
                    class="report-title">{{$report->name()}}</h1>
                @if($can_edit)
                    <span class="dashicons dashicons-edit"></span>
                @endif
            </a>
            @if($can_edit)
                <div class="modal small" data-rename-report-target="modal">
                    <div class="modal-inner">
                        <div class="title-small">
                            {{__('Rename report', 'independent-analytics')}}
                        </div>
                        <p>{{__('Give this report a new name', 'independent-analytics')}}</p>
                        <form data-action="rename-report#rename">
                            <input type="text" data-rename-report-target="input"
                                   placeholder="Report name" required>
                            <button data-rename-report-target="renameButton"
                                    class="iawp-button purple">{{__('Update title', 'independent-analytics')}}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="primary-report-title-container">
            <h1 class="report-title">{{$report->name()}}</h1>
        </div>
    @endif
    @if($can_edit)
        <div class="buttons">
            @if($report->is_saved_report())
                <div data-controller="save-report" data-save-report-id-value="{{$report->id()}}"
                     class="save-report">
                    <p data-save-report-target="warning" style="display: none;"
                       class="unsaved-warning"><span class="dashicons dashicons-warning"></span>
                        <span class="text">You have unsaved changes</span></p>
                    <button id="save-report-button" 
                            data-save-report-target="button"
                            data-action="save-report#save"
                            class="save-report-button iawp-button">{{__('Save', 'independent-analytics')}}</button>
                </div>
            @endif

            <div data-controller="copy-report"
                 @if($report->is_saved_report())
                     data-copy-report-id-value="{{$report->id()}}"
                 @else
                     data-copy-report-type-value="{{$report->type()}}"
                 @endif
                 class="modal-parent small copy-report"
            >
                <button id="save-as-report-button"
                        data-action="click->copy-report#toggleModal"
                        data-copy-report-target="modalButton"
                        class="save-as-report-button iawp-button">{{__('Save As', 'independent-analytics')}}</button>
                <div class="modal small" data-copy-report-target="modal">
                    <div class="modal-inner">
                        <div class="title-small">
                            {{__('Create new report', 'independent-analytics')}}
                        </div>
                        <p>{{__('Enter a name for the new report.', 'independent-analytics')}}</p>
                        <form data-action="copy-report#copy">
                            <input type="text" data-copy-report-target="input"
                                   placeholder="Report name" required>
                            <button data-copy-report-target="copyButton" class="iawp-button purple">
                                {{__('Save as', 'independent-analytics')}}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div>
                <button id="favorite-report-button"
                        data-controller="set-favorite-report"
                        data-set-favorite-report-id-value="{{$report->is_saved_report() ? $report->id() : ''}}"
                        data-set-favorite-report-type-value="{{$report->is_saved_report() ? '' : $report->type()}}"
                        data-action="set-favorite-report#setFavoriteReport"
                        class="iawp-button favorite {{$report->is_favorite() ? 'active' : '' }}"
                >
                    <span class="dashicons dashicons-star-filled"></span>
                    {{__('Make default', 'independent-analytics')}}
                </button>
            </div>  
            @if($report->is_saved_report())
                <div data-controller="delete-report" data-delete-report-id-value="{{$report->id()}}"
                     class="modal-parent small delete-report">
                    <button id="delete-report-button"
                            data-action="delete-report#toggleModal"
                            data-delete-report-target="modalButton" class="iawp-button">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                    <div class="modal small" data-delete-report-target="modal">
                        <div class="modal-inner">
                            <div class="title-small">
                                {{__('Confirm', 'independent-analytics')}}
                            </div>
                            <p>{{__('Are you sure you want to delete this report?', 'independent-analytics')}}</p>
                            <button data-action="delete-report#delete"
                                    data-delete-report-target="deleteButton"
                                    class="iawp-button red">{{__('Delete report', 'independent-analytics')}}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>