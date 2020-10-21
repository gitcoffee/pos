@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.restaurants.bookings.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.restaurants.bookings.title') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ config('app.url') . '/pos/login' }}" target="_blank" class="btn btn-lg btn-dark">
                    {{ __('pos::app.admin.system.pos.btn-pos-front') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('pos_restaurant_table_bookings', 'Webkul\Pos\DataGrids\Admin\PosTableBookingDataGrid')
            {!! $pos_restaurant_table_bookings->render() !!}
        </div>
    </div>
@stop