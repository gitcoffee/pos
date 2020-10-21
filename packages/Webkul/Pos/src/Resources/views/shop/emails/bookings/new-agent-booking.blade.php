@component('shop::emails.layouts.master')
    <div style="text-align: center;">
        <a href="{{ config('app.url') }}">
            @if (core()->getConfigData('pos.configuration.general.pos_logo'))
                <img src="{{ \Illuminate\Support\Facades\Storage::url(core()->getConfigData('pos.configuration.general.pos_logo')) }}" alt="{{ core()->getConfigData('pos.configuration.general.heading_on_login') }}" style="height: 40px; width: 110px;"/>
            @else
                <img src="{{ bagisto_asset('themes/default/assets/images/logo.svg') }}">
            @endif
        </a>
    </div>

    <?php 
        $agent = $booking->agent;
        $table = $booking->table;
    ?>

    <div style="padding: 30px;">
        <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
            <span style="font-weight: bold;">
                {{ __('pos::app.admin.mail.booking.heading', ['booking_id' => $booking->booking_id]) }}
            </span> <br>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('pos::app.admin.mail.booking.dear', ['customer_name' => $agent->firstname . ' ' . $agent->lastname]) }},
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {!! __('pos::app.admin.mail.booking.booking-placed', [
                    'booking_id' => $booking->booking_id,
                    'created_at' => $booking->created_at
                    ])
                !!}
            </p>
        </div>

        <div style="margin: 30px 0px !important;">
            <div style="font-weight: bold;font-size: 20px;color: #242424;line-height: 15px;">{{ __('pos::app.admin.mail.booking.booking-for') }}</div>
            <div style="font-weight: normal;font-size: 16px;color: #242424;padding-top: 20px;line-height: 30px;">
                {{ __('pos::app.admin.mail.booking.customer-name') }} - {{ $booking->customer_name }}
                <br> 
                {{ __('pos::app.admin.mail.booking.customer-email') }} - {{ $booking->customer_email }}
            </div>
        </div>

        <div class="section-content">
            <div class="table mb-20">
                <table style="overflow-x: auto; border-collapse: collapse;
                border-spacing: 0;width: 100%">
                    <thead>
                        <tr style="background-color: #f2f2f2">
                            <th style="text-align: left;padding: 8px">{{ __('pos::app.admin.mail.booking.table-name') }}</th>
                            <th style="text-align: left;padding: 8px">{{ __('pos::app.admin.mail.booking.table-type') }}</th>
                            <th style="text-align: left;padding: 8px">{{ __('pos::app.admin.mail.booking.booked-seat') }}</th>
                            <th style="text-align: left;padding: 8px">{{ __('pos::app.admin.mail.booking.booked-date') }}</th>
                            <th style="text-align: left;padding: 8px">{{ __('pos::app.admin.mail.booking.booked-slot') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td data-value="{{ __('pos::app.admin.mail.booking.table-name') }}" style="text-align: left;padding: 8px">{{ $table->name }}</td>
                            <td data-value="{{ __('pos::app.admin.mail.booking.table-type') }}" style="text-align: left;padding: 8px">{{ $table->type }}</td>
                            <td data-value="{{ __('pos::app.admin.mail.booking.booked-seat') }}" style="text-align: left;padding: 8px">{{ $booking->booked_seat }}</td>
                            <td data-value="{{ __('pos::app.admin.mail.booking.booked-date') }}" style="text-align: left;padding: 8px">{{ $booking->booked_date }}</td>
                            <td data-value="{{ __('pos::app.admin.mail.booking.booked-slot') }}" style="text-align: left;padding: 8px">{{ $booking->booked_time_from . ' - ' . $booking->booked_time_to }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        @if (core()->getConfigData('pos.restaurant.email.booking_email_note'))
            <div style="margin-top: 30px !important;">
                <div style="font-weight: bold;font-size: 20px;color: #242424;line-height: 15px;">{{ __('pos::app.admin.mail.booking.message-note') }}</div>
                <div style="font-weight: normal;font-size: 16px;color: #242424;padding-top: 20px;line-height: 30px;">
                    {{ core()->getConfigData('pos.restaurant.email.booking_email_note') }}
                </div>
            </div>
        @endif

        <div style="margin-top: 20px;font-size: 16px;color: #5E5E5E;line-height: 24px;display: inline-block;width: 100%">
            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {!! __('shop::app.mail.order.help', [
                        'support_email' => '<a style="color:#0041FF" href="mailto:' . config('mail.from.address') . '">' . config('mail.from.address') . '</a>'
                        ]) !!}
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('shop::app.mail.order.thanks') }}
            </p>
        </div>
    </div>
@endcomponent
