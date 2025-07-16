@extends('layouts.master')
@section('title')
    {{ __('dashboard') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-theme text-white mr-2">
                    <i class="fa fa-home"></i>
                </span> {{ __('dashboard') }}
            </h3>
        </div>

        <div class="row">
            {{-- Upcoming Zoom Classes --}}
            @hasFeature('Zoom Online Classes')
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body custom-card-body">
                            <div class="clearfix">
                                <h4 class="card-title float-left">
                                    <i class="fa fa-video-camera text-primary mr-2"></i>{{ __('Upcoming Online Classes') }}
                                </h4>
                                <a href="{{ route('zoom.index') }}" class="btn btn-sm btn-primary float-right">
                                    {{ __('View All') }}
                                </a>
                            </div>
                            <div class="v-scroll dashboard-description">
                                @if (count($upcomingZoomClasses) > 0)
                                    @foreach ($upcomingZoomClasses as $class)
                                        <div class="wrapper mb-3 d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <div class="d-flex">
                                                <div class="wrapper ms-3">
                                                    <h6 class="mb-1">{{ $class->title }}</h6>
                                                    <span class="text-small text-muted">{{ $class->subject->name ?? 'N/A' }}</span>
                                                    <br>
                                                    <span class="text-small text-muted">
                                                        <i class="fa fa-user mr-1"></i>{{ $class->teacher->full_name ?? 'N/A' }}
                                                    </span>
                                                    <br>
                                                    <span class="text-small text-success">
                                                        <i class="fa fa-clock-o mr-1"></i>{{ date('M d, Y h:i A', strtotime($class->start_time)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                @if($class->start_time <= now()->addMinutes(15) && $class->end_time >= now())
                                                    <a href="{{ $class->join_url }}" target="_blank" class="btn btn-success btn-sm mb-1">
                                                        <i class="fa fa-video-camera mr-1"></i>{{ __('Join Now') }}
                                                    </a>
                                                @else
                                                    <span class="badge badge-info">{{ __('Scheduled') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-md-12 text-center bg-light p-3 mb-2">
                                        <i class="fa fa-video-camera fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">{{ __('No upcoming online classes found') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endHasFeature

            {{-- Announcements --}}
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body custom-card-body">
                        <h4 class="card-title">
                            <i class="fa fa-bullhorn text-warning mr-2"></i>{{ __('announcement') }}
                        </h4>
                        <div class="table-responsive v-scroll">
                            <table class="table custom-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('no.') }}</th>
                                        <th class="col-md-3">{{ __('title') }}</th>
                                        <th>{{ __('description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($announcement))
                                        @foreach ($announcement as $key => $row)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $row->title }}</td>
                                                <td>{{ Str::limit($row->description, 100) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">{{ __('No announcements found') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Holidays --}}
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body custom-card-body">
                        <h4 class="card-title">
                            <i class="fa fa-calendar text-info mr-2"></i>{{ __('holiday') }}
                        </h4>
                        <div class="v-scroll dashboard-description">
                            <table class="table custom-table">
                                @hasNotFeature('Holiday Management')
                                    <tbody class="leave-list">
                                        <tr>
                                            <td colspan="2" class="text-center text-small">
                                                {{ __('Purchase') . ' ' . __('Holiday Management') . ' ' . __('to Continue using this functionality') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                @endHasNotFeature

                                @hasFeature('Holiday Management')
                                    <tbody>
                                        @if(count($holiday) > 0)
                                            @foreach ($holiday as $holidayItem)
                                                <tr>
                                                    <td>{{ $holidayItem->title }}</td>
                                                    <td>
                                                        <span class="float-right text-muted">
                                                            {{ date('d - M', strtotime($holidayItem->date)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">{{ __('No holidays found') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                @endHasFeature
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body custom-card-body">
                        <h4 class="card-title">
                            <i class="fa fa-bolt text-success mr-2"></i>{{ __('Quick Actions') }}
                        </h4>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <a href="{{ route('assignment.index') }}" class="btn btn-outline-primary btn-block">
                                    <i class="fa fa-tasks mr-2"></i>{{ __('Assignments') }}
                                </a>
                            </div>
                            <div class="col-6 mb-3">
                                <a href="{{ route('attendance.view') }}" class="btn btn-outline-info btn-block">
                                    <i class="fa fa-check mr-2"></i>{{ __('Attendance') }}
                                </a>
                            </div>
                            <div class="col-6 mb-3">
                                <a href="{{ route('exams.index') }}" class="btn btn-outline-warning btn-block">
                                    <i class="fa fa-book mr-2"></i>{{ __('Exams') }}
                                </a>
                            </div>
                            <div class="col-6 mb-3">
                                @hasFeature('Zoom Online Classes')
                                    <a href="{{ route('zoom.index') }}" class="btn btn-outline-success btn-block">
                                        <i class="fa fa-video-camera mr-2"></i>{{ __('Online Classes') }}
                                    </a>
                                @else
                                    <a href="#" class="btn btn-outline-secondary btn-block disabled">
                                        <i class="fa fa-video-camera mr-2"></i>{{ __('Online Classes') }}
                                    </a>
                                @endHasFeature
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
    $(document).ready(function() {
        // Refresh upcoming classes every 5 minutes
        setInterval(function() {
            location.reload();
        }, 300000); // 5 minutes
    });
</script>
@endsection
