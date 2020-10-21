@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.users.outlets.assign.title') }}
@stop

@section('content-wrapper')

    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/dashboard') }}';"></i>
                    
                    {{ __('pos::app.admin.users.outlets.assign.title') }}
                </h1>
            </div>
        </div>

        <div class="page-content">
            @inject('pos_assign_products', 'Webkul\Pos\DataGrids\Admin\PosOutletProductDataGrid')
            {!! $pos_assign_products->render() !!}
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="status-container-template">
        <div class="control-group text">
            <select class="control" :name="'status['+product_id+']'" :id="product_id" @change="assignProduct">
                <option value="0" :selected="posStatus == 0">{{ __('pos::app.admin.users.outlets.assign.disabled') }}</option>
                <option value="1" :selected="posStatus == 1">{{ __('pos::app.admin.users.outlets.assign.enabled') }}</option>
            </select>
        </div>
    </script>

    <script>
        Vue.component('status-container', {
            template: '#status-container-template',
            props: ['product_id', 'pos_status', 'outlet_id'],
            inject: ['$validator'],
            data: () => ({
                posStatus: 0,
            }),
            created() {
                if ( parseInt(this.pos_status) != 'NaN' ) {
                    this.posStatus = parseInt(this.pos_status);
                }
            },
            methods: {
                assignProduct(event) {
                    var this_this = this;
                    var _clickedElement = event.target;
                    var selectedValue = _clickedElement.value;
                    
                    this_this.$http.post("{{ url('admin/pos/outlets/assignupdate') }}", {
                        _token: '{{ csrf_token() }}',
                        indexes: this_this.product_id,
                        status: selectedValue
                    })
                    .then(response => {
                        if (response.data.status) {
                            $('.alert-wrapper').html('<div class="alert alert-success"><span class="icon white-cross-sm-icon" onclick="$(this).parent().remove();"></span><p>'+ response.data.message +'</p></div>');
                        } else {
                            $('.alert-wrapper').html('<div class="alert alert-danger"><span class="icon white-cross-sm-icon" onclick="$(this).parent().remove();"></span><p>'+ response.data.message +'</p></div>');
                        }
                    })
                    .catch(function (error) {
                        console.log('Error in assigning Product: ' + error);
                    });
                    
                }
            }
        });
    </script>
@endpush
