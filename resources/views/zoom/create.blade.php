@extends('layouts.master')

@section('title')
    {{ __('Create Online Class') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <i class="mdi mdi-video-plus"></i>
                {{ __('Create Online Class') }}
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('zoom.index') }}">{{ __('Online Classes') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
                </ol>
            </nav>
            <div class="page-header-actions">
                <a href="{{ route('zoom.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-arrow-left"></i>
                    {{ __('Back to Online Classes') }}
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Create New Online Class') }}
                        </h4>
                        <form class="pt-3 create-online-class-form" id="create-form" action="{{ route('zoom.store') }}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" name="title" class="form-control" placeholder="{{ __('Enter Class Title') }}" required />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="class_section_id">{{ __('Class Section') }} <span class="text-danger">*</span></label>
                                    <select id="class_section_id" name="class_section_id" class="form-control select2" required>
                                        <option value="">{{ __('Select Class Section') }}</option>
                                        @foreach($classSections as $section)
                                            <option value="{{ $section->id }}">{{ $section->class->name }} {{ $section->section->name }} {{ $section->medium->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="subject_id">{{ __('Subject') }} <span class="text-danger">*</span></label>
                                    <select id="subject_id" name="subject_id" class="form-control select2" required>
                                        <option value="">{{ __('Select Subject') }}</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="session_year_id">{{ __('Session Year') }} <span class="text-danger">*</span></label>
                                    <select id="session_year_id" name="session_year_id" class="form-control select2" required>
                                        <option value="">{{ __('Select Session Year') }}</option>
                                        @foreach($sessionYears as $year)
                                            <option value="{{ $year->id }}" {{ $defaultSessionYear && $defaultSessionYear->id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="start_time">{{ __('Start Time') }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="start_time" name="start_time" class="form-control" required />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="end_time">{{ __('End Time') }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="end_time" name="end_time" class="form-control" required />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="description">{{ __('Description') }}</label>
                                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="{{ __('Enter Class Description') }}"></textarea>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-gradient-primary me-2">{{ __('Create') }}</button>
                                    <a href="{{ route('zoom.index') }}" class="btn btn-secondary me-2">{{ __('Back') }}</a>
                                    <a href="{{ route('zoom.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2();
        
        // Store original subjects for reset functionality
        const originalSubjects = @json($subjects);
        
        // Handle class section change
        $('#class_section_id').on('change', function() {
            const classSectionId = $(this).val();
            const subjectSelect = $('#subject_id');
            
            if (classSectionId) {
                // Show loading state
                subjectSelect.html('<option value="">{{ __("Loading...") }}</option>');
                subjectSelect.prop('disabled', true);
                
                // Fetch subjects for selected class section
                $.ajax({
                    url: "{{ route('zoom.get-subjects', ':id') }}".replace(':id', classSectionId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        subjectSelect.html('<option value="">{{ __("Select Subject") }}</option>');
                        
                        if (response.success && response.subjects.length > 0) {
                            $.each(response.subjects, function(index, subject) {
                                subjectSelect.append(
                                    '<option value="' + subject.id + '">' + 
                                    subject.name + 
                                    (subject.code ? ' (' + subject.code + ')' : '') + 
                                    '</option>'
                                );
                            });
                        } else {
                            subjectSelect.append('<option value="" disabled>{{ __("No subjects found for this class") }}</option>');
                        }
                        
                        subjectSelect.prop('disabled', false);
                        subjectSelect.select2('destroy').select2(); // Reinitialize Select2
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching subjects:', error);
                        subjectSelect.html('<option value="">{{ __("Error loading subjects") }}</option>');
                        subjectSelect.prop('disabled', false);
                        
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: "{{ __('Error') }}",
                            text: "{{ __('Failed to load subjects. Please try again.') }}"
                        });
                    }
                });
            } else {
                // Reset to original subjects if no class section selected
                subjectSelect.html('<option value="">{{ __("Select Subject") }}</option>');
                $.each(originalSubjects, function(index, subject) {
                    subjectSelect.append(
                        '<option value="' + subject.id + '">' + 
                        subject.name + 
                        (subject.code ? ' (' + subject.code + ')' : '') + 
                        '</option>'
                    );
                });
                subjectSelect.prop('disabled', false);
                subjectSelect.select2('destroy').select2(); // Reinitialize Select2
            }
        });
        
        // Handle subject change (for reverse compatibility)
        $('#subject_id').on('change', function() {
            const subjectId = $(this).val();
            const classSectionSelect = $('#class_section_id');
            
            // If subject is selected but no class section, don't filter class sections
            // This maintains the current behavior where users can select subject first
            if (subjectId && !classSectionSelect.val()) {
                // Allow both selection orders - no additional filtering needed
                console.log('Subject selected first - maintaining flexibility');
            }
        });

        // Form validation
        $('#create-form').validate({
            rules: {
                title: {
                    required: true,
                },
                class_section_id: {
                    required: true,
                },
                subject_id: {
                    required: true,
                },
                start_time: {
                    required: true,
                },
                end_time: {
                    required: true,
                },
                session_year_id: {
                    required: true,
                }
            },
            messages: {
                title: {
                    required: "{{ __('Please enter class title') }}",
                },
                class_section_id: {
                    required: "{{ __('Please select class section') }}",
                },
                subject_id: {
                    required: "{{ __('Please select subject') }}",
                },
                start_time: {
                    required: "{{ __('Please select start time') }}",
                },
                end_time: {
                    required: "{{ __('Please select end time') }}",
                },
                session_year_id: {
                    required: "{{ __('Please select session year') }}",
                }
            },
            submitHandler: function (form) {
                // Validate start time is before end time
                const startTime = new Date($('#start_time').val());
                const endTime = new Date($('#end_time').val());
                
                if (startTime >= endTime) {
                    Swal.fire({
                        icon: 'error',
                        title: "{{ __('Error') }}",
                        text: "{{ __('End time must be after start time') }}"
                    });
                    return false;
                }
                
                form.submit();
            }
        });
    });
</script>
@endsection 