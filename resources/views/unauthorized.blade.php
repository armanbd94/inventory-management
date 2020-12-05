@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

@section('content')
<div class="dt-content">

    <!-- Grid -->
    <div class="row">
        <div class="col-xl-12 pb-3">
            <ol class="breadcrumb bg-white">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                <li class="active breadcrumb-item">{{ $sub_title }}</li>
              </ol>
        </div>
        <!-- Grid Item -->
        <div class="col-xl-12">

            <!-- Entry Header -->
            <div class="dt-entry__header">

                <!-- Entry Heading -->
                <div class="dt-entry__heading">
                    <h2 class="dt-page__title mb-0 text-primary"><i class="{{ $page_icon }}"></i> {{ $sub_title }}</h2>
                </div>
                <!-- /entry heading -->

            </div>
            <!-- /entry header -->

            <!-- Card -->
            <div class="dt-card">

                <!-- Card Body -->
                <div class="dt-card__body">

                    <!-- 404 Page -->
                    <div class="error-page text-center">

                        <!-- Title -->
                        <h1 class="dt-error-code">401</h1>
                        <!-- /title -->

                        <h2 class="mb-10">Sorry, You are unauthorized to access this page</h2>

                        <p class="text-center mb-6"><a href="{{ url('/') }}" class="btn btn-primary">Go to Home</a></p>


                    </div>
                    <!-- /404 page -->
                </div>
                <!-- /card body -->

            </div>
            <!-- /card -->

        </div>
        <!-- /grid item -->

    </div>
    <!-- /grid -->

</div>

@endsection

