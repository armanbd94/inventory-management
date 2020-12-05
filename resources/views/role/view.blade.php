@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

@push('stylesheet')
    
@endpush

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

                <a type="button" class="btn btn-danger btn-sm" href="{{ route('role') }}">
                   <i class="fas fa-arrow-circle-left"></i> Back
                </a>

            </div>
            <!-- /entry header -->

            <!-- Card -->
            <div class="dt-card">

                <!-- Card Body -->
                <div class="dt-card__body">
                    <div class="col-md-12">
                        <h2 class="text-center text-primary">{{ $permission_data['role']->role_name }} - Details</h2>
                    </div>
                    <div class="col-md-12">
                        <ul id="permission" class="text-left"  style="list-style: none;">
                            @if (!$data->isEMpty())
                                @foreach ($data as $menu)
                                    @if ($menu->submenu->isEmpty())
                                        <li>
                                            @if(collect($permission_data['role_module'])->contains($menu->id)) 
                                            <i class="fas fa-check-circle text-success"></i>
                                            @else
                                            <i class="fas fa-times-circle text-danger"></i>
                                            @endif
                                            {!! $menu->type == 1 ? $menu->divider_title.' <small>(Divider)</small>' : $menu->module_name !!}
                                            @if (!$menu->permission->isEmpty())
                                                <ul style="list-style: none;">
                                                    @foreach ($menu->permission as $permission)
                                                        <li>
                                                            
                                                            @if(collect($permission_data['role_permission'])->contains($permission->id)) 
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            @else
                                                            <i class="fas fa-times-circle text-danger"></i>
                                                            @endif
                                                            {{ $permission->name }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @else 
                                    <li>
                                        
                                        @if(collect($permission_data['role_module'])->contains($menu->id)) 
                                        <i class="fas fa-check-circle text-success"></i>
                                        @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                        @endif
                                        {!! $menu->type == 1 ? $menu->divider_title.' <small>(Divider)</small>' : $menu->module_name !!}
                                            <ul style="list-style: none;">
                                                @foreach ($menu->submenu as $submenu)
                                                    <li>
                                                        @if(collect($permission_data['role_module'])->contains($submenu->id)) 
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        @else
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                        @endif
                                                        {{ $submenu->module_name }}
                                                        @if (!$submenu->permission->isEmpty())
                                                            <ul style="list-style: none;">
                                                                @foreach ($submenu->permission as $permission)
                                                                <li>
                                                                    @if(collect($permission_data['role_permission'])->contains($permission->id)) 
                                                                    <i class="fas fa-check-circle text-success"></i>
                                                                    @else
                                                                    <i class="fas fa-times-circle text-danger"></i>
                                                                    @endif
                                                                    {{ $permission->name }}
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                    </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div> 
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

