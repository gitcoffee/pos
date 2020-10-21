@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.users.users.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.users.users.title') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('admin.pos.users.create') }}" class="btn btn-lg btn-primary">
                    {{ __('pos::app.admin.users.users.btn-add-user') }}
                </a>
                <a href="{{ config('app.url') . '/pos/login' }}" target="_blank" class="btn btn-lg btn-dark">
                    {{ __('pos::app.admin.system.pos.btn-pos-front') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('pos_users', 'Webkul\Pos\DataGrids\Admin\PosUserDataGrid')
            {!! $pos_users->render() !!}
        </div>
    </div>
@stop