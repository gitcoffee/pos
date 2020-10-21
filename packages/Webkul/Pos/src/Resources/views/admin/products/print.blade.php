@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.products.print_title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.products.print_title') }}</h1>
            </div>

            <div class="page-action">
                <button type="button" class="btn btn-lg btn-primary" id="print-barcode" >
                    {{ __('pos::app.admin.products.print-btn-title') }}
                </button>
                <a href="{{ route('admin.pos.products.index') }}" class="btn btn-lg btn-black">
                    {{ __('pos::app.admin.products.back-btn-title') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            
            <printbarcode
                :barcodename='@json($barcode_name)'
                :barcodes='@json($barcodes)'
            ></printbarcode>
            
        </div>
    </div>
@stop


@push('scripts')

    <script type="text/x-template" id="printbarcode-template">
        
        <div v-if="isQuantity">
            <div class="form-field-section">
                <div class="control-group" :class="[errors.has('quantity') ? 'has-error' : '']">
                    <label for="quantity" class="required">{{ 'Print Quantity' }}</label>
                    <input type="text" v-validate="'required|numeric|max:3'" class="control" id="quantity" name="quantity" v-model="quantity" data-vv-as="&quot;{{ 'Print Quantity' }}&quot;"  @keyup="generateBarcodes" />
                    <span class="control-error" v-if="errors.has('quantity')">@{{ errors.first('quantity') }}</span>
                </div>
            </div>
            <div class="barcode-container">
                <div v-for="(barcode, index) in barcodes" :key="index">
                    
                    <div class="barcode-print" v-for="n in parseInt(quantity)">
                        <img :src="barcode.img_url" /><br>
                        <p v-if="barcodename == 1" v-text="barcode.product_name"></p>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script>
        
        Vue.component('printbarcode', {

        template: '#printbarcode-template',

            inject: ['$validator'],
            
            props: {
                barcodename: {
                    type: Boolean,
                    required: true
                },
    
                barcodes: {
                    type: Array,
                    required: true
                },
            },
            data() {
                return {
                    quantity: 1,
                    showOption: 0,
                }
            },
            computed: {
                isQuantity () {
                    this.checkBarcodes();

                    return this.showOption;
                }
            },
            methods: {
                checkBarcodes() {
                    if (this.barcodes.length > 0) {
                        this.showOption = 1;
                    }
                },
                generateBarcodes(event) {
                    if ((event.keyCode > 47 && event.keyCode < 58) || (event.keyCode > 95 && event.keyCode < 106)  || (event.keyCode > 36 && event.keyCode < 41) || event.keyCode == 8) {
                        if (parseInt(this.quantity) > 99) {
                            this.quantity = 1;
                        }
                    } else {
                        this.quantity = 1;
                    }
                }
            }
        });

        $(document).ready(function () {
            function addClasses() {
                $('.navbar-top').addClass('hide');
                $('.navbar-left').addClass('hide');
                $('.content-container .aside-nav').addClass('hide');
                $('.content-container').addClass('content-container-hide');
                $('.content-wrapper').addClass('content-wrapper-hide');
                $('.page-action').addClass('hide');
                $('.page-header').addClass('hide');
                $('.form-field-section').addClass('hide');
            }
            function removeClasses() {
                $('.navbar-top').removeClass('hide');
                $('.navbar-left').removeClass('hide');
                $('.content-container .aside-nav').removeClass('hide');
                $('.content-container').removeClass('content-container-hide');
                $('.content-wrapper').removeClass('content-wrapper-hide');
                $('.page-action').removeClass('hide');
                $('.page-header').removeClass('hide');
                $('.form-field-section').removeClass('hide');
            }
            $('#print-barcode').on('click', function(e) {
                addClasses();
                window.print();
                removeClasses();
            });

        });
    </script>
@endpush