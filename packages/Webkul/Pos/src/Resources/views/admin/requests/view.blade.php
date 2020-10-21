@extends('admin::layouts.master')

@section('page_title')
    {{ __('pos::app.admin.requests.view-title', ['request_id' => $posProductRequest->id]) }}
@stop

@section('content-wrapper')
<div class="content full-page">
    <form method="POST" action="" @submit.prevent="onSubmit" enctype="multipart/form-data">

        <div class="page-header">
            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/pos/requests') }}';"></i>

                    {{ __('pos::app.admin.requests.view-title', ['request_id' => $posProductRequest->id]) }}
                </h1>
            </div>

            <div class="page-action">
                <?php if ($posProductRequest->request_status != '1') {  ?>
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('pos::app.admin.requests.save-btn-title') }}
                    </button>
                <?php } ?>
            </div>
        </div>

        <div class="page-content">
            @csrf()

            <input name="_method" type="hidden" value="PUT">

            <div class="sale-container">

                <accordian :title="'{{ __('pos::app.admin.requests.user-and-outlet') }}'" :active="true">
                    <div slot="body">

                        <div class="sale-section">
                            <div class="secton-title">
                                <span>{{ __('pos::app.admin.requests.user-info') }}</span>
                            </div>

                            <div class="section-content">
                                <div class="row">
                                    <span class="title"> 
                                        {{ __('pos::app.admin.requests.user-name') }}
                                    </span>

                                    <span class="value">
                                        <a target="_blank" href="{{ route('admin.pos.users.edit', $posUser->id) }}">{{ $posUser->firstname }} {{ $posUser->lastname }}</a>
                                    </span>
                                </div>

                                <div class="row">
                                    <span class="title"> 
                                        {{ __('pos::app.admin.requests.user-email') }}
                                    </span>

                                    <span class="value"> 
                                        {{ $posUser->email }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="sale-section">
                            <div class="secton-title">
                                <span>{{ __('pos::app.admin.requests.outlet-info') }}</span>
                            </div>

                            <div class="section-content">
                                <div class="row">
                                    <span class="title"> 
                                        {{ __('pos::app.admin.requests.outlet-name') }}
                                    </span>

                                    <span class="value">
                                        <a target="_blank" href="{{ route('admin.pos.outlets.edit', $posOutlet->id) }}">{{ $posOutlet->name }}</a>
                                    </span>
                                </div>

                                <div class="row">
                                    <span class="title" style="vertical-align: top;"> 
                                        <p>{{ __('pos::app.admin.requests.outlet-address') }}</p>
                                    </span>

                                    <span class="value"> 
                                        <p>{{ $posOutlet->address }}</p>
                                        <p>{{ $posOutlet->state }} , {{ $posOutlet->country }}</p>
                                        <p>{{ $posOutlet->postcode }}</p>
                                    </span>
                                </div>

                                <div class="row">
                                    <span class="title"> 
                                        {{ __('pos::app.admin.requests.outlet-inventory-source') }}
                                    </span>

                                    <span class="value">
                                        <a target="_blank" href="{{ route('admin.inventory_sources.edit', $posOutlet->inventory_source_id) }}">{{ $posOutlet->inventory_source_name }}</a>
                                    </span>
                                </div>

                            </div>
                        </div>

                    </div>
                </accordian>

                <accordian :title="'{{ __('pos::app.admin.requests.pos-product-request') }}'" :active="true">
                        <div slot="body">

                            <div class="sale-section">
                                <div class="secton-title">
                                    <span>{{ __('pos::app.admin.requests.request-info') }}</span>
                                </div>
    
                                <div class="section-content">
                                    <div class="row">
                                        <span class="title"> 
                                            {{ __('pos::app.admin.requests.request-id') }}
                                        </span>
    
                                        <span class="value">
                                             # {{ $posProductRequest->id }}
                                        </span>
                                    </div>
    
                                    <div class="row">
                                        <span class="title"> 
                                            {{ __('pos::app.admin.requests.requested-product') }}
                                        </span>
    
                                        <span class="value"> 
                                            <a target="_blank" href="{{ route('admin.catalog.products.edit', $posProductRequest->product_id) }}"> # {{ $posProductRequest->product_id }} {{ $productFlat->name }}</a>
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title"> 
                                            {{ __('pos::app.admin.requests.request-date') }}
                                        </span>
    
                                        <span class="value">
                                            {{ $posProductRequest->created_at }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title"> 
                                            {{ __('pos::app.admin.requests.requested-quantity') }}
                                        </span>
    
                                        <span class="value">
                                            <b>{{ $posProductRequest->requested_quantity }} {{ __('pos::app.admin.requests.units') }}</b>
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title"> 
                                            {{ __('pos::app.admin.requests.request-comment') }}
                                        </span>
    
                                        <span class="value">
                                            {{ $posProductRequest->comment }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title"> 
                                            {{ __('pos::app.admin.requests.request-status') }}
                                        </span>
    
                                        <span class="value">
                                            <div class="control-group">
                                                <select class="control" {{ $posProductRequest->request_status == '1' ? 'disabled' : ''}} name="update-options" id="request_status" >
                                                    <option value="0" {{ $posProductRequest->request_status == '0' ? 'selected' : ''}}>{{ __('pos::app.admin.requests.pending') }}</option>
                                                    <option value="1" {{ $posProductRequest->request_status == '1' ? 'selected' : ''}}>{{ __('pos::app.admin.requests.complete') }}</option>
                                                    <option value="2" {{ $posProductRequest->request_status == '2' ? 'selected' : ''}}>{{ __('pos::app.admin.requests.decline') }}</option>
                                                </select>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            </div>

                    </div>
                </accordian>
            </div>
        </div>
    <form
</div>
@stop