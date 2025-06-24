@extends('tablar::page') 

@section('content')
    <!-- Page header -->
     <x-userheader>
        <x-slot:title>
            Welcome back, {{Auth::user()->name}}!
        </x-slot>
        <x-slot:subtitle>
           Manage your face detection credits and view your activity.
        </x-slot>
    </x-userheader>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Current Balance</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <p class="text-secondary">Available credit for face detection</p>
                            <div class="d-flex align-items-end">
                                <h1 class="display-5 fw-bold">{{Auth()->user()->credits}}</h1>
                                <p class="text-secondary px-2">credits</p>
                                <h5><span class="badge bg-dark text-white">Healthy</span></h5>
                            </div>
                            <div class="row">
                                <div class="col-sm col-lg-6 mb-2"><a href="{{route('purchaseCredit')}}" class="btn btn-dark col-12"><i class="ti ti-credit-card pe-1"></i>Buy More Credits</a></div>
                                <div class="col-sm col-lg-6"><a href="{{route('uploadImage')}}" class="btn btn-ligh col-12"><i class="ti ti-upload pe-1"></i>Upload Image</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Total Uploads</h3>
                        </div>
                        <div class="card-body">
                            <h2 class="">{{$total_uploads}}</h2>
                            <p>Image Processed</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm col-lg-4">
                     <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Member Since</h3>
                        </div>
                        <div class="card-body">
                            <h2 class="">{{Auth()->user()->created_at->format('M, Y') }}</h2>
                            <p>Account Created</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Credits Used</h3>
                        </div>
                        <div class="card-body">
                            <h2 class="">{{$total_consumed}}</h2>
                            <p>Total credit consumed</p>
                        </div>
                    </div>
                </div>
            </div>
             <div class="row row-deck row-cards mb-4">
                <div class="col-sm">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Action</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-secondary">Available credit for face detection</p>
                            <div class="row">
                                <div class="col-sm col-lg-3 mb-2">
                                    <a href="{{route('uploadImage')}}">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <h2 class=""><i class="ti ti-upload"></i></h2>
                                                <p>Uploads Image</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                 <div class="col-sm col-lg-3 mb-2">
                                    <a href="{{route('purchaseCredit')}}">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <h2 class=""><i class="ti ti-credit-card"></i></h2>
                                                <p>Buy Credits</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                 <div class="col-sm col-lg-3 mb-2">
                                    <a href="{{route('transactionHistory')}}">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <h2 class=""><i class="ti ti-history"></i></h2>
                                                <p>View History</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                 <div class="col-sm col-lg-3">
                                    <a href="{{route('transactionHistory')}}">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <h2 class=""><i class="ti ti-camera"></i></h2>
                                                <p>Recent Results</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
