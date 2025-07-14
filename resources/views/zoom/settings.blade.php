@extends('layouts.master')

@section('title')
    {{ __('zoom_settings') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('zoom_settings') }}
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('zoom_settings') }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('zoom_api_configuration') }}</h4>
                        <form class="forms-sample" action="{{ route('zoom-settings.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="client_id">{{ __('client_id') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="client_id" name="client_id" 
                                            class="form-control @error('client_id') is-invalid @enderror"
                                            value="{{ old('client_id', $settings->client_id ?? '') }}" 
                                            required>
                                        @error('client_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="client_secret">{{ __('client_secret') }} <span class="text-danger">*</span></label>
                                        <input type="password" id="client_secret" name="client_secret" 
                                            class="form-control @error('client_secret') is-invalid @enderror"
                                            value="{{ old('client_secret', $settings->client_secret ?? '') }}" 
                                            required>
                                        @error('client_secret')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="account_id">{{ __('account_id') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="account_id" name="account_id" 
                                            class="form-control @error('account_id') is-invalid @enderror"
                                            value="{{ old('account_id', $settings->account_id ?? '') }}" 
                                            required>
                                        @error('account_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="is_active">{{ __('status') }}</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                                {{ old('is_active', $settings->is_active ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_active">{{ __('active') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h5><i class="icon fas fa-info"></i> {{ __('note') }}:</h5>
                                        <p>{{ __('zoom_settings_note') }}</p>
                                        <ul>
                                            <li>{{ __('zoom_settings_note_1') }}</li>
                                            <li>{{ __('zoom_settings_note_2') }}</li>
                                            <li>{{ __('zoom_settings_note_3') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-save"></i> {{ __('save_changes') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 