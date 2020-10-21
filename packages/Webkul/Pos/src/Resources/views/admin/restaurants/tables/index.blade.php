@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.restaurants.tables.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.restaurants.tables.title') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('admin.pos.restaurants.tables.create') }}" class="btn btn-lg btn-primary">
                    {{ __('pos::app.admin.restaurants.tables.btn-add-table') }}
                </a>
                <a href="{{ config('app.url') . '/pos/login' }}" target="_blank" class="btn btn-lg btn-dark">
                    {{ __('pos::app.admin.system.pos.btn-pos-front') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('pos_restaurant_tables', 'Webkul\Pos\DataGrids\Admin\PosTableDataGrid')
            {!! $pos_restaurant_tables->render() !!}
        </div>
    </div>
@stop