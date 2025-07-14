@extends('layouts.master')

@section('title')
    {{ __('online_classes') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('online_classes') }}
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('online_classes') }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list_of_online_classes') }}
                            @can('zoom-create')
                                <a href="{{ route('zoom.create') }}" class="btn btn-primary btn-sm float-right">
                                    <i class="fa fa-plus"></i> {{ __('create_new_class') }}
                                </a>
                            @endcan
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                <table id="zoom_table" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('no') }}</th>
                                            <th>{{ __('title') }}</th>
                                            <th>{{ __('class_section') }}</th>
                                            <th>{{ __('subject') }}</th>
                                            <th>{{ __('teacher') }}</th>
                                            <th>{{ __('start_time') }}</th>
                                            <th>{{ __('duration') }}</th>
                                            <th>{{ __('status') }}</th>
                                            <th>{{ __('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#zoom_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('zoom.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'title', name: 'title'},
                    {data: 'class_section', name: 'class_section'},
                    {data: 'subject', name: 'subject'},
                    {data: 'teacher', name: 'teacher'},
                    {data: 'start_time', name: 'start_time'},
                    {data: 'duration', name: 'duration'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[5, 'desc']]
            });
        });

        function deleteClass(id) {
            if (confirm("{{ __('are_you_sure_want_to_delete') }}")) {
                $.ajax({
                    url: "{{ url('zoom') }}/" + id,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#zoom_table').DataTable().ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            }
        }

        function joinClass(url) {
            window.open(url, '_blank');
        }
    </script>
@endsection 