@extends('tablar::page')

@section('content')
<!-- Page header -->
<x-userheader>
    <x-slot:title>
        Settings
        </x-slot>
        <x-slot:subtitle>
            Set your success and error age thresholds
            </x-slot>
</x-userheader>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col">
                    <label>Success Min Age</label>
                    <input type="number" name="success_min_age" class="form-control"
                        value="{{ $age_setting->success_min_age }}">
                </div>
                <div class="col">
                    <label>Success Max Age</label>
                    <input type="number" name="success_max_age" class="form-control"
                        value="{{ $age_setting->success_max_age }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label>Error Min Age</label>
                    <input type="number" name="error_min_age" class="form-control"
                        value="{{ $age_setting->error_min_age }}">
                </div>
                <div class="col">
                    <label>Error Max Age</label>
                    <input type="number" name="error_max_age" class="form-control"
                        value="{{ $age_setting->error_max_age }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Settings</button>
        </form>
    </div>
</div>
@endsection