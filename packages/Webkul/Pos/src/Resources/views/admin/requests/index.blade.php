@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.requests.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.requests.title') }}</h1>
            </div>
        </div>

        <div class="page-content">
            @inject('pos_requests', 'Webkul\Pos\DataGrids\Admin\PosProductRequestDataGrid')
            {!! $pos_requests->render() !!}
        </div>
    </div>
@stop