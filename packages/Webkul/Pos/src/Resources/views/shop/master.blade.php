<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

    <title>@yield('page_title')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ $csrfToken }}">
    <link rel="stylesheet" href="{{ asset('vendor/webkul/pos/assets/css/pos-shop.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/webkul/ui/assets/css/ui.css') }}">
    
    <link rel="icon" sizes="16x16" href="{{ asset('vendor/webkul/pos/assets/images/favicon.ico') }}" />

    @yield('head')

    @section('seo')
        <meta name="description" content="{{ core()->getCurrentChannel()->description }}"/>
    @show

    @stack('css')

</head>

<body @if (app()->getLocale() == 'ar')class="rtl"@endif>

    <div id="app">

        <div class="pos-container-wrapper">
            
            <div class="content-container">
                
                @yield('content-wrapper')

            </div>

        </div>

        <footer-link
            :show-link='false'
            :admin-link='@json(config('app.url') . '/admin/login')'
            :store-link='@json("https://store.webkul.com/bagisto-laravel-ecommerce-pos.html")'
        ></footer-link>

        <ajax-loader></ajax-loader>
    </div>
    <script type="text/javascript">

        // general logo config section
        window.pos_logo_default     = @json(asset('vendor/webkul/pos/assets/images/Logo.svg'));
        window.pos_logo_image       = @json(Storage::url(core()->getConfigData('pos.configuration.general.pos_logo')));
        window.pos_logo_result      = @json(core()->getConfigData('pos.configuration.general.pos_logo'));

        // barcode config section
        window.search_option        = @json(core()->getConfigData('pos.configuration.barcode.search_option'));
        window.hide_barcode        = @json(core()->getConfigData('pos.configuration.barcode.hide_barcode'));

        // product config section
        window.allow_sku        = @json(core()->getConfigData('pos.configuration.product.allow_sku'));

        // Bill-Receipt config section
        window.show_logo        = @json(core()->getConfigData('pos.configuration.bill-receipt.show_logo'));
        window.custom_address_status   = @json(core()->getConfigData('pos.configuration.bill-receipt.custom_address'));

        window.custom_address   = {
            store_name: @json(core()->getConfigData('pos.configuration.bill-receipt.store_name')),
            store_address: @json(core()->getConfigData('pos.configuration.bill-receipt.store_address')),
            email_address: @json(core()->getConfigData('pos.configuration.bill-receipt.email_address')),
            website: @json(core()->getConfigData('pos.configuration.bill-receipt.website')),
            phone_number: @json(core()->getConfigData('pos.configuration.bill-receipt.phone_number')),
            cc_number: @json(core()->getConfigData('pos.configuration.bill-receipt.cc_number')),
            gstin: @json(core()->getConfigData('pos.configuration.bill-receipt.gstin')),
        };
        window.show_order_barcode  = @json(core()->getConfigData('pos.configuration.bill-receipt.show_barcode'));
        window.bill_footer_note  = @json(core()->getConfigData('pos.configuration.bill-receipt.bill_footer_note'));

        
        window.default_channel      = @json(core()->getDefaultChannel());
        window.pos_currencies       = @json(core()->getAllCurrencies());
        window.pos_default_currency = @json(core()->getCurrentCurrency());
        window.base_currency        = @json(core()->getBaseCurrency());
        window.base_currency_code   = @json(core()->getBaseCurrencyCode());
        window.base_currency_symbol = @json(core()->currencySymbol(core()->getBaseCurrencyCode()));
        window.pos_currency_code    = @json(core()->getCurrentCurrencyCode());
        window.pos_currency_symbol  = @json(core()->currencySymbol(core()->getCurrentCurrencyCode()));        
        window.pos_locales          = @json(core()->getAllLocales());
        window.pos_default_locale   = @json(app()->getLocale());
        window.channel_logo         = @json(core()->getCurrentChannel()->logo_url);
        window.app_base_url         = @json(config('app.url'));
        window.pos_saas             = 0;
        window.base_dir_url         = @json($base_dir_url);
        window.csrfToken            = @json($csrfToken);

        //Pos Restaurant Config
        window.config_restaurant    = {
            status:             @json(core()->getConfigData('pos.restaurant.general.status')),
            custom_email:       @json(core()->getConfigData('pos.restaurant.email.generate_email')),
            agent_table_status: @json(core()->getConfigData('pos.restaurant.general.agent_table_create')),
            agent_table_max:    @json(core()->getConfigData('pos.restaurant.general.agent_table_max')),
            table_shape:        @json(core()->getConfigData('pos.restaurant.general.table_shape')),
        };

        window.flashMessages = [];
        @if ($success = session('success'))
            window.flashMessages = [{'type': 'alert-success', 'message': "{{ $success }}" }];
        @elseif ($warning = session('warning'))
            window.flashMessages = [{'type': 'alert-warning', 'message': "{{ $warning }}" }];
        @elseif ($error = session('error'))
            window.flashMessages = [{'type': 'alert-error', 'message': "{{ $error }}" }];
        @elseif ($info = session('info'))
            window.flashMessages = [{'type': 'alert-error', 'message': "{{ $info }}" }];
        @endif

        window.serverErrors = [];
        @if (isset($errors))
            @if (count($errors))
                window.serverErrors = @json($errors->getMessages());
            @endif
        @endif
        
    </script>

    <script type="text/javascript" src="{{ asset('vendor/webkul/pos/assets/js/pos.js') }}"></script>

    @stack('scripts')
</body>

</html>