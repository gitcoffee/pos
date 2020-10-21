@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.products.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.products.title') }}</h1>
            </div>
        </div>

        <div class="page-content">
            @inject('pos_products', 'Webkul\Pos\DataGrids\Admin\PosProductDataGrid')
            {!! $pos_products->render() !!}
        </div>
    </div>
@stop