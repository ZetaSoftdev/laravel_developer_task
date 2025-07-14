@extends('layouts.master')

@section('title')
    {{ __('Zoom Online Classes') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Zoom Online Classes') }}
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Zoom Online Classes') }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('List of Zoom Online Classes') }}
                            @can('zoom-class-create')
                                <a href="{{ route('zoom.create') }}" class="btn btn-primary btn-sm float-right">
                                    <i class="fa fa-plus"></i> {{ __('Create New Class') }}
                                </a>
                            @endcan
                        </h4>
                        
                        <div id="toolbar">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="filter_class_section_id" id="filter_class_section_id" class="form-control">
                                        <option value="">{{ __('All Classes') }}</option>
                                        @foreach ($classSections as $classSection)
                                            <option value="{{ $classSection->id }}">{{ $classSection->class->name }} - {{ $classSection->section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="filter_subject_id" id="filter_subject_id" class="form-control">
                                        <option value="">{{ __('All Subjects') }}</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="filter_session_year_id" id="filter_session_year_id" class="form-control">
                                        <option value="">{{ __('All Session Years') }}</option>
                                        @foreach ($sessionYears as $sessionYear)
                                            <option value="{{ $sessionYear->id }}">{{ $sessionYear->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="filter_status" id="filter_status" class="form-control">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="upcoming">{{ __('Upcoming') }}</option>
                                        <option value="live">{{ __('Live') }}</option>
                                        <option value="completed">{{ __('Completed') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('zoom.list') }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                               data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true"
                               data-sort-name="id" data-sort-order="desc" data-maintain-selected="true"
                               data-export-data-type='all' data-query-params="zoomQueryParams"
                               data-toolbar="#toolbar" data-export-options='{ "fileName": "zoom-class-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}' 
                               data-show-export="true" data-escape="true">
                            <thead>
                                <tr>
                                    <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                    <th scope="col" data-field="no">{{ __('no.') }}</th>
                                    <th scope="col" data-field="title" data-sortable="true">{{ __('Title') }}</th>
                                    <th scope="col" data-field="class_section">{{ __('Class Section') }}</th>
                                    <th scope="col" data-field="subject">{{ __('Subject') }}</th>
                                    <th scope="col" data-field="teacher">{{ __('Teacher') }}</th>
                                    <th scope="col" data-field="start_time" data-sortable="true">{{ __('Start Time') }}</th>
                                    <th scope="col" data-field="end_time">{{ __('End Time') }}</th>
                                    <th scope="col" data-field="meeting_id">{{ __('Meeting ID') }}</th>
                                    <th scope="col" data-field="status">{{ __('Status') }}</th>
                                    <th scope="col" data-field="join_url" data-escape="false">{{ __('Join') }}</th>
                                    <th scope="col" data-field="start_url" data-escape="false" data-visible="false">{{ __('Start') }}</th>
                                    <th scope="col" data-field="operate" data-events="zoomEvents" data-escape="false">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function zoomQueryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search,
                class_section_id: $('#filter_class_section_id').val(),
                subject_id: $('#filter_subject_id').val(),
                session_year_id: $('#filter_session_year_id').val(),
                status: $('#filter_status').val()
            };
        }
        
        // Refresh table when filters change
        $('#filter_class_section_id, #filter_subject_id, #filter_session_year_id, #filter_status').on('change', function() {
            $('#table_list').bootstrapTable('refresh');
        });
        
        window.zoomEvents = {
            'click .delete-form': function (e, value, row, index) {
                e.preventDefault();
                swal({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: $(e.target).attr('href'),
                            type: "DELETE",
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.error == false) {
                                    show_success_toast(response.message);
                                    $('#table_list').bootstrapTable('refresh');
                                } else {
                                    show_error_toast(response.message);
                                }
                            }
                        });
                    }
                });
            }
        };
    </script>
@endsection
