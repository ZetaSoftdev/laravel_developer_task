@extends('layouts.master')

@section('title')
    {{ __('Online Class Attendance') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Online Class Attendance') }}
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('zoom.index') }}">{{ __('Online Classes') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Attendance') }}</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Class Details') }}
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">{{ __('Title') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $onlineClass->title }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">{{ __('Teacher') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $onlineClass->teacher->full_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">{{ __('Class Section') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $onlineClass->classSection->class->name }} {{ $onlineClass->classSection->section->name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">{{ __('Subject') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $onlineClass->subject->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">{{ __('Start Time') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ date('d-m-Y h:i A', strtotime($onlineClass->start_time)) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">{{ __('End Time') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ date('d-m-Y h:i A', strtotime($onlineClass->end_time)) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">{{ __('Status') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ ucfirst($onlineClass->status) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">{{ __('Duration') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $onlineClass->duration }} {{ __('minutes') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Attendance') }}
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Join Time') }}</th>
                                        <th>{{ __('Leave Time') }}</th>
                                        <th>{{ __('Duration') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($participants) && count($participants) > 0)
                                        @foreach($participants as $key => $participant)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $participant['name'] }}</td>
                                                <td>{{ $participant['email'] }}</td>
                                                <td>{{ date('d-m-Y h:i A', strtotime($participant['join_time'])) }}</td>
                                                <td>{{ isset($participant['leave_time']) ? date('d-m-Y h:i A', strtotime($participant['leave_time'])) : 'N/A' }}</td>
                                                <td>{{ isset($participant['duration']) ? $participant['duration'] . ' ' . __('minutes') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">{{ __('No attendance data available') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($onlineClass->attendances) && count($onlineClass->attendances) > 0)
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Student Attendance') }}
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Student Name') }}</th>
                                        <th>{{ __('Roll Number') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Join Time') }}</th>
                                        <th>{{ __('Leave Time') }}</th>
                                        <th>{{ __('Duration') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($onlineClass->attendances as $key => $attendance)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $attendance->student->user->full_name }}</td>
                                            <td>{{ $attendance->student->roll_number }}</td>
                                            <td>
                                                @if($attendance->status == 'present')
                                                    <span class="badge badge-success">{{ __('Present') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('Absent') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $attendance->join_time ? date('d-m-Y h:i A', strtotime($attendance->join_time)) : 'N/A' }}</td>
                                            <td>{{ $attendance->leave_time ? date('d-m-Y h:i A', strtotime($attendance->leave_time)) : 'N/A' }}</td>
                                            <td>{{ $attendance->duration ? $attendance->duration . ' ' . __('minutes') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection 