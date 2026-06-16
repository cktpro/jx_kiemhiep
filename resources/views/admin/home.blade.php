@extends('admin.layout')

{{-- port từ Admin6/AdminPageHome.aspx + AdminPageHome.aspx.cs (LoadQLNap) --}}

@section('title', 'Trang chủ')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Trang chủ</span></li>
@endsection

@section('content')
    <div class="container-lg">
        <div class="card mb-4">
            <div class="card-header">Danh mục quản lý</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-2 col-md-3 col-sm-4 col-6 mb-4">
                        <div class="bg-primary theme-color w-75 rounded mb-2" style="padding-top: 75%"></div>
                        <h6><a href="{{ route('admin.news.index') }}">Quản lý tin</a></h6>
                    </div>
                    @if($canNapThe)
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6 mb-4">
                            <div class="bg-success theme-color w-75 rounded mb-2" style="padding-top: 75%"></div>
                            <h6><a href="{{ route('admin.napthe') }}">Quản lý nạp thẻ</a></h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
