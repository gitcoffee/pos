@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.banks.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.banks.title') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('admin.pos.banks.create') }}" class="btn btn-lg btn-primary">
                    {{ __('pos::app.admin.banks.btn-add-bank') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('pos_banks', 'Webkul\Pos\DataGrids\Admin\PosBankDataGrid')
            {!! $pos_banks->render() !!}
        </div>
    </div>
@stop