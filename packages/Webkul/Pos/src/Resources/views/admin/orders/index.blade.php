@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.orders.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.orders.title') }}</h1>
            </div>
        </div>

        <div class="page-content">
            @inject('pos_orders', 'Webkul\Pos\DataGrids\Admin\PosOrderDataGrid')
            {!! $pos_orders->render() !!}
        </div>
    </div>
@stop