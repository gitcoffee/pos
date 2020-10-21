@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.reports.title') }}
@stop

@section('css')
    <style>
        .control-group.date:after {
            left: unset !important;
            top: 5px !important;
            right: 1% !important;
        }
    </style>
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.reports.title') }}</h1>
            </div>
            <div class="page-action">
                <div class="export-import" @click="showModal('reportDataGrid')">
                    <i class="export-icon"></i>
                    <span >
                        {{ __('admin::app.export.export') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="page-header">
            <date-filter></date-filter>
        </div>

        <div class="page-content">
            @inject('pos_reports', 'Webkul\Pos\DataGrids\Admin\PosReportDataGrid')
            {!! $pos_reports->render() !!}
        </div>
    </div>

    <modal id="reportDataGrid" :is-open="modalIds.reportDataGrid">
        <h3 slot="header">{{ __('pos::app.admin.reports.download') }}</h3>
        <div slot="body">
            <export-form></export-form>
        </div>
    </modal>
@stop

@push('scripts')

    @include('pos::admin.export.export', ['gridName' => $pos_reports])

    <script type="text/x-template" id="date-filter-template">
        <div style="text-align:center;">
            <div>
                <div class="control-group" style="width:250px;margin:0px;">
                    <select class="control" v-model="outlet_id">
                        <option value="">-- Select Outlet --</option>
                        <option :value="outlet.id" v-for="(outlet, index) in pos_outlets" :key="index"> @{{ outlet.name }} </option>
                        <option value="all">All</option>
                    </select>
                </div>

                <div class="control-group" style="width: 250px;margin: 0px 0px 0px 20px;">
                    <select class="control" v-model="sale_type">
                        <option value="">-- Sale Type --</option>
                        <option value="pos">Pos</option>
                        <option value="web">Website</option>
                        <option value="all">All</option>
                    </select>
                </div>
                
                <div class="control-group" style="width: 250px;margin: 0px 0px 0px 20px;">
                    <select class="control" v-model="bank_name">
                        <option value="">-- Select Bank --</option>
                        <option :value="bank.name" v-for="(bank, index) in pos_banks" :key="index"> @{{ bank.name }} </option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:10px;">
                <div class="control-group" style="width:250px;margin:0px;position:relative;">
                    <input type="text" class="control" autocomplete="off" v-model="search_term" placeholder="{{ __('admin::app.catalog.products.product-search-hint') }}" v-on:keyup="search">
        
                    <div class="linked-product-search-result" style="position:absolute;">
                        <ul>
                            <li v-for='(product, index) in products' v-if='products.length' @click="addProduct(product)">
                                @{{ product.name }}
                            </li>
        
                            <li v-if="is_searching && search_term.length">
                                {{ __('admin::app.catalog.products.searching') }}
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="control-group date" style="width: 250px;margin: 0px 0px 0px 20px;">
                    <datetime><input type="text" class="control" id="start_date" value="{{ $startDate->format('Y-m-d H:i:s') }}" placeholder="{{ __('admin::app.dashboard.from') }}" v-model="start" /></datetime>
                </div>

                <div class="control-group date" style="width: 250px;margin: 0px 0px 0px 20px;">
                    <datetime><input type="text" class="control" id="end_date" value="{{ $endDate->format('Y-m-d H:i:s') }}" placeholder="{{ __('admin::app.dashboard.to') }}" v-model="end"/></datetime>
                </div>
            </div>

            <div style="width: 100%;margin-top: 10px;overflow: hidden;text-align: right;">
                <button type="button" class="btn btn-lg btn-dark" @click="applyFilter">Filter</button>
                <button type="button" class="btn btn-lg btn-primary" @click="clearURL">Clear Filter</button>
            </div>
        </div>
    </script>

    <script>
        Vue.component('date-filter', {

            template: '#date-filter-template',

            data: function() {
                return {
                    start: "{{ $startDate->format('Y-m-d H:i:s') }}",
                    end: "{{ $endDate->format('Y-m-d H:i:s') }}",
                    outlet_id: "{{ request('outlet') ? request('outlet') : '' }}",
                    pos_outlets: @json($posOutlets),
                    sale_type: "{{ request('sale') ? request('sale') : '' }}",
                    pos_banks: @json($posBanks),
                    bank_name: "{{ request('bank') ? request('bank') : '' }}",

                    products: [],
                    search_term: '',
                    is_searching: false,
                }
            },

            methods: {
                applyFilter: function() {
                    var url = '';
                    
                    if ( this.outlet_id !== '') {
                        url += "&outlet=" + encodeURIComponent(this.outlet_id);
                    }

                    if ( this.sale_type !== '') {
                        url += "&sale=" + encodeURIComponent(this.sale_type);
                    }

                    if ( this.bank_name !== '') {
                        url += "&bank=" + encodeURIComponent(this.bank_name);
                    }

                    if ( this.search_term !== '') {
                        url += "&product=" + encodeURIComponent(this.search_term);
                    }

                    if ( this.start !== '') {
                        url += "&start=" + encodeURIComponent(this.start);
                    }
                    if ( this.end !== '') {
                        url += "&end=" + encodeURIComponent(this.end);
                    }

                    if ( url !== '') {
                        window.location.href = '?' + url.substring(1, url.length);
                    }
                },

                clearURL: function() {
                    window.location.href = '{{ route('admin.pos.reports.index') }}';
                },

                addProduct: function (product) {
                    this.search_term = product.name;
                    this.products = [];
                },

                search: function () {
                    var self = this;

                    self.is_searching = true;

                    if (self.search_term.length >= 1) {
                        this.$http.get ("{{ route('admin.pos.reports.orderedproductsearch') }}", {params: {query: self.search_term}})
                            .then (function(response) {
                                self.products = response.data;

                                self.is_searching = false;
                            })
                            .catch (function (error) {
                                self.is_searching = false;
                            })
                    } else {
                        self.products = [];
                        self.is_searching = false;
                    }
                }
            }
        });
    </script>

@endpush