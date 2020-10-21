<?php

return [
    [
        'key' => 'pos',
        'name' => 'pos::app.admin.system.pos.extension_name',
        'sort' => 2
    ],  [
        'key' => 'pos.configuration',
        'name' => 'pos::app.admin.system.pos.settings',
        'sort' => 1,
    ],  [
        'key' => 'pos.configuration.general',
        'name' => 'pos::app.admin.system.pos.general',
        'sort' => 1,
        'fields' => [
            [
                'name' => 'status',
                'title' => 'pos::app.admin.system.general.status',
                'type' => 'select',
                'validation' => 'required',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.general.active',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.general.inactive',
                        'value' => false
                    ]
                ]
            ],
            [
                'name' => 'heading_on_login',
                'title' => 'pos::app.admin.system.general.login-heading',
                'type' => 'text',
                'validation' => 'required|max:50',
                'channel_based' => true,
                'locale_based' => true
            ], [
                'name' => 'sub_heading_on_login',
                'title' => 'pos::app.admin.system.general.sub-login-heading',
                'type' => 'text',
                'validation' => 'required|max:50',
                'channel_based' => true,
                'locale_based' => true
            ], [
                'name' => 'footer_content',
                'title' => 'pos::app.admin.system.general.footer-content',
                'type' => 'text',
                'validation' => 'required|max:50',
                'channel_based' => true,
                'locale_based' => true
            ], [
                'name' => 'footer_note',
                'title' => 'pos::app.admin.system.general.footer-note',
                'type' => 'text',
                'validation' => 'required|max:50|alpha_spaces',
                'channel_based' => true,
                'locale_based' => true
            ], [
                'name' => 'footer_link_text',
                'title' => 'pos::app.admin.system.general.footer-link-text',
                'type' => 'text',
                'validation' => 'required|max:50|alpha_spaces',
                'channel_based' => true,
                'locale_based' => false
            ], [
                'name' => 'footer_link',
                'title' => 'pos::app.admin.system.general.footer-link',
                'type' => 'text',
                'validation' => 'max:150',
                'channel_based' => true,
                'locale_based' => false
            ], [
                'name' => 'pos_logo',
                'title' => 'pos::app.admin.system.general.pos-logo',
                'type' => 'image',
                'validation' => 'image|ext:jpeg,jpg,png,svg|size:20480',
                'channel_based' => false,
                'locale_based' => false
            ]
        ]
    ],  [
        'key' => 'pos.configuration.barcode',
        'name' => 'pos::app.admin.system.pos.barcode',
        'sort' => 1,
        'fields' => [
            [
                'name' => 'size',
                'title' => 'pos::app.admin.system.barcode.size',
                'type' => 'text',
                'channel_based' => true,
                'locale_based' => false
            ], [
                'name' => 'print_product_name',
                'title' => 'pos::app.admin.system.barcode.print-option',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.barcode.yes',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.barcode.no',
                        'value' => false
                    ]
                ]
            ], [
                'name' => 'image_type',
                'title' => 'pos::app.admin.system.barcode.image-style',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.barcode.horizontal',
                        'value' => 'horizontal'
                    ], [
                        'title' => 'pos::app.admin.system.barcode.vertical',
                        'value' => 'vertical'
                    ]
                ]
            ], [
                'name' => 'barcode_with',
                'title' => 'pos::app.admin.system.barcode.format',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.barcode.product-id',
                        'value' => 'product_id'
                    ], [
                        'title' => 'pos::app.admin.system.barcode.sku',
                        'value' => 'sku'
                    ]
                ]
            ], [
                'name' => 'barcode_prefix',
                'title' => 'pos::app.admin.system.barcode.barcode_prefix',
                'type' => 'text',
                'validate' => 'alpha|max:10',
                'channel_based' => false,
                'locale_based' => false
            ], [
                'name' => 'search_option',
                'title' => 'pos::app.admin.system.barcode.search_option',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.barcode.product_search',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.barcode.barcode_screener',
                        'value' => false
                    ]
                ]
            ], [
                'name' => 'hide_barcode',
                'title' => 'pos::app.admin.system.barcode.barcode_panel',
                'type' =>  'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.general.active',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.general.inactive',
                        'value' => false
                    ]
                ]
            ]
        ]
    ],  [
        'key' => 'pos.configuration.product',
        'name' => 'pos::app.admin.system.pos.product',
        'sort' => 1,
        'fields' => [
            [
                'name' => 'allow_sku',
                'title' => 'pos::app.admin.system.product.allow_sku',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.general.active',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.general.inactive',
                        'value' => false
                    ]
                ]
            ],
        ]
    ],  [
        'key' => 'pos.configuration.bill-receipt',
        'name' => 'pos::app.admin.system.pos.bill-receipt',
        'sort' => 1,
        'fields' => [
            [
                'name' => 'show_logo',
                'title' => 'pos::app.admin.system.bill-receipt.show-logo',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.general.active',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.general.inactive',
                        'value' => false
                    ]
                ]
            ],  [
                'name' => 'custom_address',
                'title' => 'pos::app.admin.system.bill-receipt.custom-address',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.general.active',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.general.inactive',
                        'value' => false
                    ]
                ]
            ],  [
                'name' => 'store_name',
                'title' => 'pos::app.admin.system.bill-receipt.store-name',
                'type' => 'text',
                'validation' => 'max:100',
                'channel_based' => true,
                'locale_based' => true
            ],  [
                'name' => 'store_address',
                'title' => 'pos::app.admin.system.bill-receipt.store-address',
                'type' => 'textarea',
                'validation' => 'max:200',
                'channel_based' => true,
                'locale_based' => true
            ],  [
                'name' => 'email_address',
                'title' => 'pos::app.admin.system.bill-receipt.email-address',
                'type' => 'text',
                'validation' => 'email',
                'channel_based' => true,
                'locale_based' => true
            ],  [
                'name' => 'website',
                'title' => 'pos::app.admin.system.bill-receipt.website',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => false
            ],  [
                'name' => 'phone_number',
                'title' => 'pos::app.admin.system.bill-receipt.contact-no',
                'type' => 'text',
                'validation' => 'numeric',
                'channel_based' => false,
                'locale_based' => false
            ],  [
                'name' => 'cc_number',
                'title' => 'pos::app.admin.system.bill-receipt.cc-no',
                'type' => 'text',
                'validation' => 'numeric',
                'channel_based' => false,
                'locale_based' => false
            ],  [
                'name' => 'gstin',
                'title' => 'pos::app.admin.system.bill-receipt.gstin-no',
                'type' => 'text',
                'validation' => 'numeric',
                'channel_based' => false,
                'locale_based' => false
            ],  [
                'name' => 'show_barcode',
                'title' => 'pos::app.admin.system.bill-receipt.show-barcode',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.general.active',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.general.inactive',
                        'value' => false
                    ]
                ]
            ],  [
                'name' => 'bill_footer_note',
                'title' => 'pos::app.admin.system.bill-receipt.footer-custom-note',
                'type' => 'textarea',
                'validation' => 'max:200',
                'channel_based' => true,
                'locale_based' => true
            ],
        ]
    ],  [
        'key' => 'pos.restaurant',
        'name' => 'pos::app.admin.system.pos.restaurant',
        'sort' => 2,
    ],  [
        'key' => 'pos.restaurant.general',
        'name' => 'pos::app.admin.system.pos.general',
        'sort' => 1,
        'fields'    => [
            [
                'name' => 'status',
                'title' => 'pos::app.admin.system.general.status',
                'type' => 'select',
                'validation' => 'required',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.general.active',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.general.inactive',
                        'value' => false
                    ]
                ]
            ],  [
                'name' => 'agent_table_create',
                'title' => 'pos::app.admin.system.restaurant.agent-table-create',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'pos::app.admin.system.general.active',
                        'value' => true
                    ], [
                        'title' => 'pos::app.admin.system.general.inactive',
                        'value' => false
                    ]
                ]
            ], [
                'name' => 'agent_table_max',
                'title' => 'pos::app.admin.system.restaurant.agent-table-max',
                'type' => 'text',
                'validation' => 'required|numeric|min_value:1|max_value:50',
                'channel_based' => true,
                'locale_based' => false
            ], [
                'name' => 'table_shape',
                'title' => 'pos::app.admin.system.restaurant.agent-table-shape',
                'type' => 'multiselect',
                'validation' => 'required',
                'channel_based' => true,
                'locale_based' => false,
                'options'   => [
                    [
                        'title' => 'Circle',
                        'value' => 'circle'
                    ], [
                        'title' => 'Square',
                        'value' => 'square'
                    ], [
                        'title' => 'Curved Square',
                        'value' => 'curved-square'
                    ]
                ]
            ],
        ]
    ],  [
        'key' => 'pos.restaurant.email',
        'name' => 'pos::app.admin.system.pos.email',
        'sort' => 2,
        'fields'    => [
            [
                'name' => 'generate_email',
                'title' => 'pos::app.admin.system.restaurant.generate-email',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => false
            ], [
                'name' => 'customer_booking_email',
                'title' => 'pos::app.admin.system.restaurant.customer-booking-email',
                'type' => 'boolean',
            ],  [
                'name' => 'agent_booking_email',
                'title' => 'pos::app.admin.system.restaurant.agent-booking-email',
                'type' => 'boolean'
            ], [
                'name' => 'custom_booking_email',
                'title' => 'pos::app.admin.system.restaurant.custom-booking-email',
                'type' => 'text',
                'validation' => 'email',
                'channel_based' => true,
                'locale_based' => false
            ], [
                'name' => 'booking_email_note',
                'title' => 'pos::app.admin.system.restaurant.booking-custom-note',
                'type' => 'textarea',
                'validation' => 'max:500',
                'channel_based' => true,
                'locale_based' => true
            ]
        ]
    ]
];