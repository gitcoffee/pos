export const messages = {
    en: {
        pos_common_component: {
            modal: {
                header_title: 'Default header',
                body: 'Default body'
            }
        },
        pos_login: {
            title: 'Pos Login',
            username: 'User Name',
            password: 'Password',
            remember_password: 'Remember Password',
            button_title: 'Log In',
            forgot_passward: 'Forgot Password?',
            footer_note: 'A Product Of <a href="{url}" target="_blank">WEBKUL</a>',
            invalid_creds: 'Warning: Please check your credentials and try again!',
            unauth_user: 'Warning: You are not pos authorized user, please contact to admin!'
        },
        pos_view: {
            title: 'Pos System',
            error_offline_mode: 'Warning: Currently you are in offline mode!',
            error_offline_action: 'Warning: You can not perform this action in offline mode!',
            online_mode: 'Success: You have successfully entered in online mode!',
            offline_mode: 'Warning: You have successfully entered in offline mode!',
        },
        pos_home: {
            title: 'Pos System',
            navtop: {
                heading: 'Point Of Sale',
                search_placeholder: 'Search product by Name, SKU',
                btn_search: 'Search',
                entry_product_search: 'Search Product',
                search_barcode_placeholder: 'Search Product Through Barcode',
                cashier: 'Cashier',
                add_custom_product: 'Add Custom Product',
                entry_product_name: 'Product Name',
                entry_sku: 'Sku',
                entry_price: 'Price',
                entry_quantity: 'Quantity',
                button_cart: 'Add To Cart',
                success_add_to_cart: 'Success: Custom product added to current cart successfully!',
                add_custom_product_tooltip: 'Add Custom Product',
                reset_data_tooltip: 'Reset Pos Data',
                offline_mode_tooltip: 'Switch To Offline/Online Mode',
                logout_tooltip: 'Logout User',
            },
            pos_categories: {
                all: 'All',
                selected: 'Select Category',
                all_category_listing: 'All Categories Listing',
            },
            pos_products: {
                product_variation: 'Product Variations',
                product_discount_tooltip: 'Info: This product is on discount.',
                product_variation_tooltip: 'Info: This product contains variations.',
                total_product_info: 'Info: Total {total_product} product(s) found in this Outlet.',
                total_product_loaded: 'Success: Total {total_loaded} product(s) loaded to outlet out of {outof_total} product(s).',
                warning_select_product_variation: 'Please select above options first.',
                success_add_to_cart: 'Success: Product added to cart successfully!',
                btn_done: 'Done',
                btn_add_to_cart: 'Add To Cart',
                grouped_product: 'Grouped Product',
                name: 'Name',
                quantity: 'Quantity',
                customize_options: 'Customize Options',
                choose_selection: 'Choose An Option',
                none: 'None',
                total_amount: 'Total Amount',
                downloadable_options: 'Downloadable Options',
                download_samples: 'Samples Contents',
                download_links: 'Links Contents',
                sample: 'Sample',
                booking_details: 'Booking Details',
                location: 'Location',
                view_on_map: 'View On Map',
                book_appointment: 'Book An Appointment',
                book_date: 'Booking Date',
                book_slot: 'Slot',
                slot_duration: 'Slot Duration : ',
                slot_minutes: '{minutes} Minutes',
                today_availability: 'Today Availability',
                booking_closed: 'Closed',
                show_all_days: 'Show For All Days',
                event_on: 'Event On ',
                book_your_ticket: 'Book Your Ticket',
                select_from_date: 'Select From Date',
                select_to_date: 'Select To Date',
                rent_an_item: 'Rent An Item',
                placeholder_from: 'From Date',
                placeholder_to: 'To Date',
                select_slot: 'Select Date Slot',
                placeholder_select_date: 'Choose Date Slot',
                placeholder_select_slot: 'Choose Time Slot',
                select_time_slot: 'Select Time Slot',
                rent_time_from: 'Rent Time From',
                rent_time_to: 'Rent Time To',
                select_rent_time: 'Select Rent Time',
                chosse_rent_options: 'Choose Rent Option',
                daily_basis: 'Daily Basis',
                hourly_basis: 'Hourly Basis',
                book_restaurant_table: 'Book Restaurant Table',
                special_note: 'Special Request/Notes',
                book_your_slot: 'Book Your Slot',
                booking_from: 'Book From',
                booking_till: 'Book Till',
                event_ticket: 'Event Ticket',
                event_from: 'Event From',
                event_till: 'Event Till',
                rent_type: 'Rent Type',
                rent_from: 'Rent From',
                rent_till: 'Rent Till',
                error: {
                    no_product: 'Warning: No product found under this category.',
                    no_barcode_provided: 'Warning: provide barcode to search the product!',
                    no_barcode_product: 'Warning: No product found in this outlet related to provided barcode.',
                    no_quantity: 'Warning: The quantity for this product is not available.',
                    no_available_qty: 'Warning: You can not add the quantity more than available quantity {available_qty}.',
                    select_atleast_one: 'Warning: You have to select atleast one ticket slot to book an event.',
                }
            },
            pos_custom_product: {
                choose_product: 'Warning: Choose cart first!',
                correct_qty_price: 'Warning: Please provide the correct quantity and price for the custom product!',
            },
            button_container: {
                btn_quantity: ' Quantity',
                btn_discount: ' Discount',
                btn_empty_cart: 'Empty Cart'
            },
            pos_cart: {
                cart_details: 'Cart Details',
                text_with: 'with',
                text_unit: 'Unit(s)',
                text_per_unit: 'per unit',
                delete_current_cart: 'Delete Current Cart',
                button_customer: 'Customer',
                button_pay: 'Pay',
                button_hold: ' Hold',
                text_cart_empty: 'Current cart is empty!',
                apply_discount: 'Apply Discount To Cart',
                entry_available_discount: 'Choose Discount',
                entry_select_discount: '-- Select Discount --',
                button_apply_discount: 'Apply Discount',
                hold_order_note: 'Order Note',
                entry_order_note: 'Provide Order Note',
                placeholder_note: 'Enter note for order..',
                button_hold_order: 'Hold Order',
                button_cancel: 'Cancel',
                success_hold_cart: 'Success: Current cart has been added to the hold list successfully!',
                text_error: 'Error',
                text_confirm: 'Confirm',
                text_confirm_msg: 'Confirm: This process will generate an order depending upon the status Online or Offline. Do you still wanna do it.?',
                text_cancel: 'Cancel',
                text_sub_total: 'Sub Total',
                text_discount: 'Cash Discount',
                text_tax_total: 'Tax Total',
                text_home_delivery: 'Home Delivery',
                text_grand_total: 'Grand Total',
                error: {
                    error_plus_cart: 'Warning: You can not add cart more than {cart_count} !',
                    error_cart_empty: 'Warning: Current cart is empty!',
                    error_customer_empty: 'Warning: You have to select customer first for this order!',
                    text_amount_error: 'Warning: Entered amount cannot be paid!',
                    error_no_cart: 'Warning: There is no cart available, try again!',
                    error_no_product: 'Warning: There is no product added to the current cart, try again!',
                    error_no_customer: 'Warning: No customer selected for the current cart, try again!',
                    error_select_all_attributes: 'Warning: Please selcted all product attributes!',
                }
            }
        },
        pos_payment: {
            title: 'Pos Payment',
            heading_title: 'Payment',
            heading_order_print: 'Print Invoice',
            vertical_menu: {
                text_cash: ' Cash Payment',
                text_credit: ' Credit Payment',
            },
            pos_cash: {
                text_total: 'Total',
                text_tendered: 'Tendered',
                text_change: 'Change',
                entry_bank_list: 'Select Bank',
                entry_card_details: 'Card Details',
                placeholder_order_comment: 'Add order note here...',
                button_confirm_pay: '  Confirm Payment',
            },
            btn_print: 'Print',
            btn_skip: 'Skip',
            success_offline_order: 'Success: Order has been placed in Offline Mode successfully!',
            text_order_success: 'Success: Your Order has been placed successfully!',
        },
        pos_payment_cash: {
            title: 'Cash Payment',
        },
        pos_payment_credit: {
            title: 'Credit Payment',
        },
        pos_sales: {
            title: 'Pos Sales',
            top_menu: {
                sales_history: 'Sale History',
                hold_sale: 'Hold Sale',
                offline_sale: 'Offline Sale',
            },
            menu_content: {
                sales_history: {
                    placeholder_search: 'Search by order id',
                    text_order_id: 'Order ID',
                    text_booking_id: 'Booking ID',
                    text_order_date: 'Order Date',
                    text_customer_info: 'Customer Detail',
                    text_order_note: 'Order Note',
                    text_bank_name: 'Bank Information',
                    button_print_invoice: 'Print Invoice',
                    text_order_summary: 'Order Summary',
                    text_units: ' Unit(s)',
                    text_per_unit: ' per unit',
                    text_sub_total: 'Sub Total',
                    text_discount: 'Discount (-)',
                    text_taxes: 'Taxes (+)',
                    text_grand_total: 'Total',
                    text_mode_cash: 'Cash Payment',
                    text_mode_card: 'Card Payment',
                    text_balance: 'Balance',
                },
                hold_sales: {
                    no_hold_order: 'Warning: There is no hold order!',
                    text_note: ' Note',
                    text_hold_product_details: 'Hold Products Details:',
                    button_cart: ' Add To Cart',
                    button_delete: ' Remove',
                    success_remove_hold: 'Success: The current cart has been deleted successfully!',
                    success_move_cart: 'Success: The selected hold cart has been moved to the current cart successfully!',
                    no_hold_order: 'Warning: There is no hold order available!',
                },
                offline_sales: {
                    order_id: 'Order Id',
                    order_ref_id: 'Order Ref. Id',
                    order_date: 'Order Date',
                    item_detail: 'Item Detail',
                    btn_view: ' View',
                    btn_sync: ' Sync',
                    customer_detail: 'Customer Detail',
                    order_detail: 'Order Detail',
                    no_offline_order: 'Warning: Currently no order selected!',
                    dont_refresh: 'Note: Please don\'t refresh the page/redirect till the sync process have been compeleted!',
                    total_offline_order: 'Info: Total {total_order_found} offline orders found (processing...)',
                    total_sync_order: 'Success: {total_sync} order synced successfully!',
                    total_sync_success: 'Success: Offline order(s) synced successfully!',
                    no_offline_order: 'Warning: There is no offline order found!',
                    btn_all_sync: 'Sync All Offline Order(s)',
                    sync_all_order: 'Synchronize All Offline Order(s)',
                },
                print_invoice: {
                    email: 'Email: ',
                    website: 'Website: ',
                    phone: 'Phone: ',
                    customer_care: 'Customer Care: ',
                    gstin: 'GSTIN: ',
                    date: 'Date: ',
                    order_id: 'Order ID: ',
                    order_ref_id: 'Order Ref. ID: ',
                    cashier: 'Cashier: ',
                    customer: 'Customer: ',
                    mode_of_shipping: 'Mode Of Shipping: ',
                    mode_of_payment: 'Mode Of Payment: ',
                    pickup: 'Pick Up',
                    cash_payment: 'Cash Payment',
                    card_payment: 'Card Payment',
                    product: 'Product',
                    quantity: 'Qty',
                    price: 'Price',
                    amount: 'Amount',
                    total_qty: 'Total Quantity: ',
                    sub_total: 'Sub Total: ',
                    discount: 'Discount: ',
                    tax: 'Tax: ',
                    total: 'Total: ',
                    balance: 'Balance: ',
                    order_note: 'Order Note: ',
                    footer_note_1: 'Have a nice day',
                    footer_note_2: 'Thank You. Visit Again',
                }
            },
            error: {
                no_order_record: 'Warning: No Order Found!',
            }
        },
        pos_sales_history: {
            title: 'Sales History'
        },
        pos_sales_hold: {
            title: 'Hold Order'
        },
        pos_sales_offline: {
            title: 'Offline Sale'
        },
        pos_customer: {
            title: 'Pos Customer',
            top_menu: {
                title: 'Customers',
            },
            menu_content: {
                search_area: {
                    placeholder_search: 'Search Customer By Name',
                },
                add_customer: ' Add Customer',
                edit_customer: ' Edit Customer',
                success_customer_cart: 'Success: Customer has been selected for checkout!',
                success_customer_already_cart: 'Success: This Customer already selected for checkout!',
                success_customer_remove: 'Success: Cart customer removed successfully!',
                success_customer_added: 'Success: Customer added into offline mode successfully!',
                button_select_customer: 'Select Customer',
                text_edit: ' Edit',
                text_remove: ' Remove',
                entry_firstname: 'First Name',
                entry_lastname: 'Last Name',
                entry_email: 'Email',
                entry_phone: 'Phone',
                entry_gender: 'Gender',
                entry_male: 'Male',
                entry_female: 'Female',
                entry_dob: 'Date Of Birth',
                entry_customer_group: 'Customer Group',
                button_save: 'Save',
            },
            error: {
                no_customer_record: 'Warning: No Customer Found!',
                no_customer_selected: 'Warning: Currently no customer is selected!',
            }
        },
        pos_cashier: {
            title: 'Pos Cashier',
            top_menu: {
                close_counter: 'Close Counter',
                today_cash: 'Today Cash',
                sales_history: 'Sales History'
            },
            menu_content: {
                text_opening_amount: 'Drawer Opening Amount',
                entry_amount: 'Opening Amount',
                entry_remark: 'Remarks',
                button_open_store: 'Open Store',
                close_counter: {
                    text_drawer_amount: 'Drawer Amount Details',
                    text_counted_amount: 'Counted Drawer Amount',
                    text_closing_details: 'Closing Drawer Details',
                    text_open_amount: 'Opening Amount',
                    text_total_cash: 'Today\'s Total Cash Amount',
                    text_current_drawer_sale: 'Current Drawer Sale',
                    text_expected_amount: 'Expected Amount In Drawer',
                    text_amount: 'Amount ({currency})',
                    text_remark: 'Remark',
                    text_remark_placeholder: 'Comment regarding cash balance..',
                    text_difference: 'Difference between Opening and Current Drawer Sale Amount',
                    text_total_drawer_amount: 'Total Amount In Drawer',
                    button_close_drawer: 'Close Drawer',
                },
                sales_history: {
                    order_id: 'Order ID',
                    order_date: 'Order Date',
                    order_total: 'Order Total',
                    payment_mode: 'Payment Mode'
                },
                today_cash: {
                    total_cash_sale: 'Today Cash Sale',
                    total_card_sale: 'Today Card Sale'
                }
            }
        },
        pos_cashier_closecounter: {
            title: 'Close Counter'
        },
        pos_cashier_todaycash: {
            title: 'Today Cash'
        },
        pos_cashier_saleshistory: {
            title: 'Sales History'
        },
        pos_reports: {
            title: 'Pos Reports'
        },
        pos_product: {
            title: 'Pos Product',
            top_menu: {
                low_stock: 'Low Stock Product',
                request_list: 'Requested Products',
                setting: 'Setting'
            },
            menu_content: {
                low_stock: {
                    text_left: ' Left',
                    text_request_product: 'Request Product',
                    request_product: 'Add Product for Stock Request',
                    entry_required_qty: 'Required Quantity',
                    entry_request_to_supplier: 'Request To Supplier',
                    entry_select: 'Select Supplier',
                    entry_comment: 'Comment',
                    text_qty: ' Qty',
                    entry_supplier_name: 'Supplier Name',
                    entry_comment: 'Comment',
                    button_add: 'Add To List',
                    button_send: 'Send Request'
                },
                request_list: {
                    column_id: 'Id',
                    column_name: 'Product Name',
                    column_qty: 'Qty',
                    column_supplier: 'Supplier',
                    column_comment: 'Comment',
                    column_date: 'Date',
                    column_status: 'Status',
                    no_record_found: 'No Request Found',
                    action_pending: 'Pending',
                    action_complete: 'Complete',
                    action_decline: 'Decline',
                },
                setting: {
                    entry_product_setting: 'Pos Product Setting',
                    entry_low_stock: 'Minimum Unit of Quantity for Low Stock Product',
                    button_done: 'Done'
                }
            },
            error: {
                no_lowstock_product: 'Warning: There is no low stock product!'
            }
        },
        pos_product_low_stock: {
            title: 'Low Stock Products'
        },
        pos_product_requested: {
            title: 'Requested Products',
            placeholder_search: 'Search Requested-Product By Name'
        },
        pos_product_setting: {
            title: 'Product Setting'
        },
        pos_setting: {
            title: 'Pos Setting', 
            top_menu: {
                discount: 'Loyalty Discount',
                taxes: 'Taxes',
                basic_setting: 'Basic Setting',
                profile_setting: 'Profile Setting'
            },
            menu_content: {
                discount: {
                    text_discount: 'Discount Form',
                    add_discount: 'Add Discount',
                    edit_discount: 'Edit Discount',
                    entry_offer_name: 'Offer Name',
                    entry_from_price: 'From Price',
                    entry_to_price: 'To Price',
                    entry_discount_type: 'Discount Type',
                    entry_discount_value: 'Value',
                    entry_fix: 'Fixed',
                    entry_percentage: 'Percentage',
                    button_done: 'Done',
                    text_range: 'Range',
                    text_to: 'To',
                    success_discount_apply: 'Success: Discount applied to the current cart successfully!',
                },
                tax: {
                    text_tax: 'Tax Form',
                    add_tax: 'Add Tax',
                    edit_tax: 'Edit Tax',
                    button_done: 'Done'
                },
                basic: {
                    entry_select_locale: 'Select Locale',
                    entry_select_currency: 'Select Currency'
                },
                profile: {
                    entry_user_profile: 'User Profile Setting',
                    entry_firstname: 'First Name',
                    entry_lastname: 'Last Name',
                    entry_username: 'Username',
                    entry_email: 'Account E-mail',
                    entry_pre_password: 'Previous Password',
                    entry_new_password: 'New Password',
                    entry_confirm_password: 'Confirm Password',
                    button_done: 'Done'
                }
            },
            error: {
                required_fromprice: 'Warning: Fill from-price field!',
                required_toprice: 'Warning: Fill to-price field!',
                grather_fromprice: 'Warning: from-price can not greater than to-price',
                no_discount: 'Warning: No discount available!',
                no_taxes: 'Warning: No taxes available!'
            }
        },
        pos_setting_discount: {
            title: 'Pos Discount'
        },
        pos_setting_taxes: {
            title: 'Pos Taxes'
        },
        pos_setting_basic: {
            title: 'Pos Basic Setting'
        },
        pos_setting_profile: {
            title: 'Pos Profile'
        },
        pos_restaurant: {
            title: 'Pos Restaurant',
            top_menu: {
                table_list: 'Tables List',
                table_create: 'Create Table',
                table_booked: 'Table Booking History',
            },
            menu_content: {
                table_list: {
                    heading: 'Restaurant Table List',
                    available_seat: 'Total Seats: ',
                    book_table: ' Book Table',
                    full_booked: ' Fully Booked',
                    search_table_placeholder: 'Search Restaurant Table',
                    add_booking: 'Book Your Table: #{position}-{table_name}',
                    select_customer: 'Select Customer',
                    placeholder_customer: 'Search customer by name, email, phone.',
                    name: 'Full Name',
                    email: 'Email Address',
                    phone: 'Mobile',
                    placeholder_booking_name: 'Full name for booking..',
                    placeholder_booking_email: 'Email address for booking.',
                    placeholder_phone: 'Mobile number for booking.',
                    total_available_seat: 'Available Seats: {available_seat}',
                    seat: 'Book Total Seat(s)',
                    booking_date: 'Booking Date',
                    booking_time_from: 'Booking Time From',
                    booking_time_to: 'Booking Time To',
                    button_confirm_booking: 'Confirm Booking',
                    warning_select_date: 'Warning: Please choose booking date first.',
                    warning_select_from_time: 'Warning: Please choose booking time from or must be greater than current time.',
                    warning_time_range: 'Warning: Booking Time To must be grather than Booking Time From.',
                    warning_booked_seat: 'Warning: You can not book the seat more than available seats.',
                },

                table_booked: {
                    heading: 'Table Booking History List',
                    search_booking_placeholder: 'Search by booking-id, table-name, customer-name, phone, etc.',
                    booking_id: 'Booking Id: ',
                    booking_date: 'Booking Date: ',
                    time_slot: 'Time Slot: ',
                    table_information: 'Table Information :-',
                    table_name: 'Name: ',
                    booked_table_name: 'Table Name: ',
                    booked_table_type: 'Table Type: ',
                    table_type: 'Type: ',
                    booked_seat: 'Booked Seat: ',
                    customer_information: 'Customer Information :-',
                    customer_name: 'Name: ',
                    customer_email: 'Email: ',
                    booked_details: 'Booked Id: #{booking_id}',
                    booked_info: 'Booking Information',
                    no_booking_selected: 'Warning: No booking selected.',
                    booking_order_details: '-: Order Details :-',
                    add_product: 'Add Product(s)',
                    table_release: 'Table Release',
                    checkout: 'Checkout',
                    no_product_added: 'Warning: Add Product into table before checkout.',
                },

                table_create: {
                    heading: 'Create Restaurant Table',
                    table_name: 'Table Name',
                    table_type: 'Table Type',
                    table_status: 'Status',
                    active: 'Active',
                    inactive: 'Inactive',
                    table_position: 'Position',
                    no_of_seat: 'Total No. of Seat',
                    button_save: 'Create Table',
                    error_table_create_access: 'Warning: you can not create table more than {table_count} tables.',
                }
            },
            error: {
                no_access: 'Warning: You don\'t have access to use this panel.',
                no_table: 'Warning: No table available.',
                no_booking: 'Warning: No table booking available.',
            }
        },
        pos_restaurant_table_list: {
            title: 'Table List'
        },
        pos_restaurant_table_create: {
            title: 'Create Table'
        },
        pos_restaurant_table_booked: {
            title: 'Booking History'
        },
    },
    ar: {
        pos_login: {
            title: 'موقف تسجيل الدخول',
            username: 'اسم المستخدم',
            password: 'كلمه السر',
            remember_password: 'تذكر كلمة المرور',
            button_title: 'تسجيل الدخول',
            forgot_passward: 'هل نسيت كلمة المرور؟',
            footer_note: 'منتج من <a href="{url}" target="_blank"> WEBKUL </a>',
            invalid_creds: 'تحذير: يرجى التحقق من بيانات الاعتماد والمحاولة مرة أخرى!',
            unauth_user: 'تحذير: أنت لست مستخدمًا معتمدًا ، يرجى الاتصال بالمشرف!'
        },
        pos_view: {
            title: 'نظام نقاط البيع',
            error_offline_mode: 'تحذير: أنت حاليًا في وضع عدم الاتصال!',
            error_offline_action: 'تحذير: لا يمكنك تنفيذ هذا الإجراء في وضع عدم الاتصال!',
        },
        pos_home: {
            title: 'نظام نقاط البيع',
            navtop: {
                heading: 'نقطة البيع',
                search_placeholder: 'بحث المنتج حسب الاسم ، سكو',
                btn_search: 'بحث',
                entry_product_search: 'بحث المنتج',
                search_barcode_placeholder: 'البحث عن المنتج من خلال الباركود ..',
                cashier: 'أمين الصندوق',
                add_custom_product: 'إضافة منتج مخصص',
                entry_product_name: 'اسم المنتج',
                entry_price: 'السعر',
                entry_quantity: 'كمية',
                button_cart: 'أضف إلى السلة',
                success_add_to_cart: 'نجاح: تم إضافة منتج مخصص إلى العربة الحالية بنجاح!',
                add_custom_product_tooltip: 'إضافة منتج مخصص',
                reset_data_tooltip: 'إعادة تعيين بيانات نقاط البيع',
                offline_mode_tooltip: 'التبديل إلى وضع عدم الاتصال / عبر الإنترنت',
                logout_tooltip: 'تسجيل خروج المستخدم',
            },
            pos_categories: {
                all: 'الكل',
                selected: 'اختر الفئة',
                all_category_listing: 'جميع الفئات قائمة',
            },
            pos_products: {
                product_variation: 'الاختلافات المنتج',
                product_discount_tooltip: 'معلومات: هذا المنتج هو على الخصم.',
                product_variation_tooltip: 'معلومات: هذا المنتج يحتوي على الاختلافات.',
                total_product_info: 'معلومات: إجمالي المنتجات {total_product} الموجودة في هذا المخرج.',
                total_product_loaded: 'النجاح: تم تحميل إجمالي المنتجات {total_loaded} ليتم إخراجها من المنتج (المنتجات) {outof_total}.',
                warning_select_product_variation: 'يرجى اختيار الخيارات أعلاه أولا.',
                btn_done: 'فعله',
                btn_add_to_cart: 'أضف إلى السلة',
                grouped_product: 'منتج مجمع',
                name: 'اسم',
                quantity: 'كمية',
                customize_options: 'تخصيص الخيارات',
                choose_selection: 'إختر خيار',
                none: 'لا يوجد',
                total_amount: 'المبلغ الإجمالي',
                downloadable_options: 'خيارات قابلة للتنزيل',
                download_samples: 'محتويات العينة',
                download_links: 'محتويات الارتباط',
                sample: 'عينة',
                booking_details: 'تفاصيل الحجز',
                location: 'موقعك',
                view_on_map: 'عرض على الخريطة',
                book_appointment: 'حجز موعد:',
                book_date: 'تاريخ الحجز',
                book_slot: 'فتحة',
                slot_duration: 'مدة الفتحة:',
                slot_minutes: '{minutes} دقيقة',
                today_availability: 'توفر اليوم',
                booking_closed: 'مغلق',
                show_all_days: 'عرض لجميع الأيام',
                event_on: 'الحدث قيد التشغيل',
                book_your_ticket: 'احجز تذكرتك',
                select_from_date: 'حدد من التاريخ',
                select_to_date: 'حدد حتى الآن',
                rent_an_item: 'استئجار عنصر',
                placeholder_from: 'من التاريخ',
                placeholder_to: 'حتى الآن',
                select_slot: 'حدد خانة التاريخ',
                placeholder_select_date: 'اختر خانة التاريخ',
                placeholder_select_slot: 'اختر فتحة الوقت',
                select_time_slot: 'حدد فتحة الوقت',
                rent_time_from: 'وقت الإيجار من',
                rent_time_to: 'وقت الإيجار',
                select_rent_time: 'حدد وقت الإيجار',
                chosse_rent_options: 'اختر خيار الإيجار',
                daily_basis: 'الأساس اليومي',
                hourly_basis: 'بالساعة',
                book_restaurant_table: 'طاولة مطعم الكتاب',
                special_note: 'طلب / ملاحظات خاصة',
                book_your_slot: 'احجز سلوتك',
                booking_from: 'احجز من',
                booking_till: 'كتاب حتى',
                event_ticket: 'تذكرة الحدث',
                event_from: 'حدث من',
                event_till: 'الحدث حتى',
                rent_type: 'نوع الإيجار',
                rent_from: 'الإيجار من',
                rent_till: 'نظيفة حتى',
                error: {
                    no_product: 'تحذير: لا يوجد منتج موجود تحت هذا التصنيف.',
                    no_barcode_provided: 'تحذير: قم بتوفير الباركود للبحث في المنتج!',
                    no_barcode_product: 'تحذير: لا يوجد منتج موجود في هذا المنفذ يتعلق بالباركود المقدم.',
                    no_quantity: 'تحذير: كمية هذا المنتج غير متاحة.',
                    no_available_qty: 'تحذير: لا يمكنك إضافة الكمية أكثر من الكمية المتاحة {available_qty}.',
                    select_atleast_one: 'تحذير: يجب عليك تحديد فتحة تذكرة واحدة على الأقل لحجز حدث.',
                }
            },
            pos_cart: {
                cart_details: 'تفاصيل العربة',
                text_with: 'مع',
                text_unit: 'وحدة (ق)',
                text_per_unit: 'لكل وحدة',
                delete_current_cart: 'حذف العربة الحالية',
                button_customer: 'زبون',
                button_pay: 'دفع',
                button_hold: ' معلق',
                text_cart_empty: 'العربة الحالية فارغة!',
                apply_discount: 'تطبيق الخصم على العربة',
                entry_available_discount: 'اختيار الخصم',
                entry_select_discount: '- اختر الخصم -',
                button_apply_discount: 'تطبيق الخصم',
                hold_order_note: 'طلب ملاحظة',
                entry_order_note: 'تقديم مذكرة النظام',
                placeholder_note: 'أدخل ملاحظة للطلب ..',
                button_hold_order: 'عقد النظام',
                button_cancel: 'إلغاء',
                success_hold_cart: 'النجاح: تمت إضافة السلة الحالية إلى قائمة الانتظار بنجاح!',
                text_error: 'خطأ',
                text_confirm: 'تؤكد',
                text_confirm_msg: 'تأكيد: ستنشئ هذه العملية طلبًا اعتمادًا على الحالة عبر الإنترنت أو دون اتصال هل ما زلت تريد أن تفعل ذلك؟',
                text_cancel: 'إلغاء',
                text_sub_total: 'المجموع الفرعي',
                text_discount: 'خصم',
                text_tax_total: 'مجموع الضرائب',
                text_home_delivery: 'توصيل منزلي',
                text_grand_total: 'المجموع الكلي',
                error: {
                    error_plus_cart: 'تحذير: لا يمكنك إضافة سلة التسوق أكثر من {cart_count}!',
                    error_cart_empty: 'تحذير: العربة الحالية فارغة!',
                    error_customer_empty: 'تحذير: يجب عليك تحديد العميل أولاً لهذا الطلب!',
                    text_amount_error: 'تحذير: لا يمكن دفع المبلغ المدخل!',
                    error_no_cart: 'تحذير: لا يوجد عربة متوفرة ، حاول مرة أخرى!',
                    error_no_product: 'تحذير: لا يوجد منتج مضاف إلى السلة الحالية ، أعد المحاولة!',
                    error_no_customer: 'تحذير: لم يتم اختيار عميل للسلة الحالية ، حاول مرة أخرى!',
                    
                }
            }
        },
        pos_payment: {
            title: 'الدفع بوس',
            heading_title: 'دفع',
            heading_order_print: 'فاتورة طباعة',
            vertical_menu: {
                text_cash: ' دفع نقدا',
                text_credit: ' دفعة ائتمانية',
            },
            pos_cash: {
                text_total: 'مجموع',
                text_tendered: 'مناقصة',
                text_change: 'يتغيرون',
                placeholder_order_comment: 'إضافة ملاحظة الطلب هنا ...',
                button_confirm_pay: '  تأكيد الدفع',
            },
            btn_print: 'طباعة',
            btn_skip: 'تخطى',
            success_offline_order: 'النجاح: تم وضع الطلب في وضع عدم الاتصال بنجاح!',
            text_order_success: 'النجاح: تم وضع طلبك بنجاح!',
        },
        pos_payment_cash: {
            title: 'دفع نقدا',
        },
        pos_payment_credit: {
            title: 'دفعة ائتمانية',
        },
        pos_sales: {
            title: 'نقاط البيع المبيعات',
            top_menu: {
                sales_history: 'بيع التاريخ',
                hold_sale: 'عقد بيع',
                offline_sale: 'بيع حاليا',
            },
            menu_content: {
                sales_history: {
                    placeholder_search: 'البحث عن طريق معرف الطلب',
                    text_order_id: 'رقم التعريف الخاص بالطلب',
                    text_order_date: 'تاريخ الطلب',
                    text_customer_info: 'تفاصيل العملاء',
                    text_order_note: 'طلب ملاحظة',
                    button_print_invoice: 'فاتورة طباعة',
                    text_order_summary: 'ملخص الطلب',
                    text_units: 'وحدة (ق)',
                    text_per_unit: ' لكل وحدة',
                    text_sub_total: 'المجموع الفرعي',
                    text_discount: 'خصم (-)',
                    text_taxes: 'الضرائب (+)',
                    text_grand_total: 'مجموع',
                    text_mode_cash: 'دفع نقدا',
                    text_mode_card: 'بطاقه ائتمان',
                    text_balance: 'توازن',
                },
                hold_sales: {
                    no_hold_order: 'تحذير: لا يوجد أمر تعليق!',
                    text_note: 'ملحوظة',
                    text_hold_product_details: 'عقد تفاصيل المنتجات:',
                    button_cart: 'أضف إلى السلة',
                    button_delete: 'إزالة',
                    success_remove_hold: 'النجاح: تم حذف العربة الحالية بنجاح!',
                    success_move_cart: 'النجاح: تم نقل عربة الانتظار المحددة إلى العربة الحالية بنجاح!',
                    no_hold_order: 'تحذير: لا يوجد أمر تعليق متاح!',
                },
                offline_sales: {
                    order_id: 'رقم التعريف الخاص بالطلب',
                    order_ref_id: 'ترتيب المرجع. هوية شخصية',
                    order_date: 'تاريخ الطلب',
                    item_detail: 'البند التفاصيل',
                    btn_view: 'رأي',
                    btn_sync: 'مزامنة',
                    customer_detail: 'تفاصيل العملاء',
                    order_detail: 'تفاصيل الطلب',
                    no_offline_order: 'تحذير: حاليًا لم يتم تحديد أي طلب!',
                    dont_refresh: 'ملاحظة: يرجى عدم تحديث الصفحة / إعادة التوجيه حتى يتم إلزام عملية المزامنة!',
                    total_offline_order: 'معلومات: تم العثور على إجمالي {total_order_found} الطلبات غير المتصلة (معالجة ...)',
                    total_sync_order: 'نجاح: تمت مزامنة الطلب {total_sync} بنجاح!',
                    total_sync_success: 'نجاح: تمت مزامنة الطلبات (الطلبات) غير المتصلة بنجاح!',
                    no_offline_order: 'تحذير: لا يوجد طلب غير متصل!',
                    btn_all_sync: 'مزامنة جميع الطلبات غير المتصلة',
                    sync_all_order: 'مزامنة جميع الطلبات غير المتصلة',
                },
                print_invoice: {
                    email: 'البريد الإلكتروني:',
                    website: 'موقع الكتروني:',
                    phone: 'هاتف:',
                    customer_care: 'رعاية العميل:',
                    gstin: 'GSTIN: ',
                    date: 'تاريخ:',
                    order_id: 'رقم التعريف الخاص بالطلب:',
                    order_ref_id: 'ترتيب المرجع. هوية شخصية:',
                    cashier: 'أمين الصندوق:',
                    customer: 'العملاء:',
                    mode_of_shipping: 'طريقة الشحن:',
                    mode_of_payment: 'طريقة الدفع:',
                    pickup: 'امسك',
                    cash_payment: 'دفع نقدا',
                    card_payment: 'بطاقه ائتمان',
                    product: 'المنتج',
                    quantity: 'الكمية',
                    price: 'السعر',
                    amount: 'كمية',
                    total_qty: 'الكمية الإجمالية:',
                    sub_total: 'المجموع الفرعي:',
                    discount: 'خصم:',
                    tax: 'ضريبة:',
                    total: 'مجموع:',
                    balance: 'توازن:',
                    order_note: 'ملاحظة الطلب:',
                    footer_note_1: 'أتمنى لك نهارا سعيد',
                    footer_note_2: 'شكرا لكم. زورني مره اخرى',
                }
            },
            error: {
                no_order_record: 'تحذير: لم يتم العثور على أي طلب!',
            }
        },
        pos_sales_history: {
            title: 'تاريخ المبيعات'
        },
        pos_sales_hold: {
            title: 'عقد النظام'
        },
        pos_sales_offline: {
            title: 'بيع حاليا'
        },
        pos_customer: {
            title: 'نقاط البيع العملاء',
            top_menu: {
                title: 'الزبائن',
            },
            menu_content: {
                search_area: {
                    placeholder_search: 'بحث العملاء حسب الاسم',
                },
                add_customer: 'إضافة العملاء',
                edit_customer: 'تحرير العملاء',
                success_customer_cart: 'النجاح: تم اختيار الزبون للخروج!',
                success_customer_already_cart: 'النجاح: تم تحديد هذا العميل بالفعل لإجراء عملية السحب!',
                success_customer_remove: 'النجاح: تمت إزالة عميل عربة التسوق بنجاح!',
                success_customer_added: 'النجاح: تمت إضافة العميل إلى وضع عدم الاتصال بنجاح!',
                button_select_customer: 'اختيار العملاء',
                text_edit: ' تصحيح',
                text_remove: ' إزالة',
                entry_firstname: 'الاسم الاول',
                entry_lastname: 'الكنية',
                entry_email: 'البريد الإلكتروني',
                entry_gender: 'جنس',
                entry_male: 'الذكر',
                entry_female: 'إناثا',
                entry_dob: 'تاريخ الولادة',
                entry_customer_group: 'مجموعة العملاء',
                button_save: 'حفظ',
            },
            error: {
                no_customer_record: 'تحذير: لا يوجد عميل!',
                no_customer_selected: 'تحذير: حاليًا لم يتم اختيار أي عميل!',
            }
        },
        pos_cashier: {
            title: 'نقاط البيع الصراف',
            top_menu: {
                close_counter: 'إغلاق العداد',
                today_cash: 'اليوم النقدية',
                sales_history: 'تاريخ المبيعات'
            },
            menu_content: {
                text_opening_amount: 'فتح درج المبلغ',
                entry_amount: 'فتح المبلغ',
                entry_remark: 'ملاحظات',
                button_open_store: 'افتح المتجر',
                close_counter: {
                    text_drawer_amount: 'تفاصيل مقدار الدرج',
                    text_counted_amount: 'عد درج المبلغ',
                    text_closing_details: 'إغلاق تفاصيل الدرج',
                    text_open_amount: 'فتح المبلغ',
                    text_total_cash: 'إجمالي النقد اليوم',
                    text_current_drawer_sale: 'بيع الدرج الحالي',
                    text_expected_amount: 'المبلغ المتوقع في الدرج',
                    text_amount: 'كمية ({currency})',
                    text_remark: 'تعليق',
                    text_remark_placeholder: 'تعليق بخصوص الرصيد النقدي ..',
                    text_difference: 'الفرق بين مبلغ الفتح ومبلغ بيع الدرج الحالي',
                    text_total_drawer_amount: 'المبلغ الإجمالي في الدرج',
                    button_close_drawer: 'إغلاق الدرج',
                },
                sales_history: {
                    order_id: 'رقم التعريف الخاص بالطلب',
                    order_date: 'تاريخ الطلب',
                    order_total: 'الطلب الكلي',
                    payment_mode: 'طريقة الدفع'
                },
                today_cash: {
                    total_cash_sale: 'اليوم بيع النقدية',
                    total_card_sale: 'بيع بطاقة اليوم'
                }
            }
        },
        pos_cashier_closecounter: {
            title: 'إغلاق العداد'
        },
        pos_cashier_todaycash: {
            title: 'اليوم النقدية'
        },
        pos_cashier_saleshistory: {
            title: 'تاريخ المبيعات'
        },
        pos_reports: {
            title: 'تقارير نقاط البيع'
        },
        pos_product: {
            title: 'نقاط البيع المنتج',
            top_menu: {
                low_stock: 'انخفاض المخزون المنتج',
                request_list: 'المنتجات المطلوبة',
                setting: 'ضبط'
            },
            menu_content: {
                low_stock: {
                    text_left: ' اليسار',
                    text_request_product: 'طلب المنتج',
                    request_product: 'إضافة منتج لطلب الأسهم',
                    entry_required_qty: 'الكمية المطلوبة',
                    entry_request_to_supplier: 'طلب المورد',
                    entry_select: 'حدد المورد',
                    entry_comment: 'تعليق',
                    text_qty: ' الكمية',
                    entry_supplier_name: 'اسم المورد',
                    entry_comment: 'تعليق',
                    button_add: 'أضف إلى القائمة',
                    button_send: 'ارسل طلب'
                },
                request_list: {
                    column_id: 'هوية شخصية',
                    column_name: 'اسم المنتج',
                    column_qty: 'الكمية',
                    column_supplier: 'المورد',
                    column_comment: 'تعليق',
                    column_date: 'تاريخ',
                    column_status: 'الحالة',
                    no_record_found: 'لم يتم العثور على طلب',
                    action_pending: 'قيد الانتظار',
                    action_complete: 'اكتمال',
                    action_decline: 'انخفاض',
                },
                setting: {
                    entry_product_setting: 'وضع المنتج نقاط البيع',
                    entry_low_stock: 'وحدة الحد الأدنى من الكمية للمنتجات منخفضة المخزون',
                    button_done: 'فعله'
                }
            },
            error: {
                no_lowstock_product: 'تحذير: لا يوجد منتج ذو مخزون منخفض!'
            }
        },
        pos_product_low_stock: {
            title: 'منتجات الأسهم منخفضة'
        },
        pos_product_requested: {
            title: 'المنتجات المطلوبة',
            placeholder_search: 'بحث المنتج المطلوب حسب الاسم'
        },
        pos_product_setting: {
            title: 'إعداد المنتج'
        },
        pos_setting: {
            title: 'وضع نقاط البيع', 
            top_menu: {
                discount: 'خصم الولاء',
                taxes: 'الضرائب',
                basic_setting: 'الإعداد الأساسي',
                profile_setting: 'إعداد الملف الشخصي'
            },
            menu_content: {
                discount: {
                    text_discount: 'نموذج الخصم',
                    add_discount: 'إضافة الخصم',
                    edit_discount: 'تحرير الخصم',
                    entry_offer_name: 'اسم العرض',
                    entry_from_price: 'من السعر',
                    entry_to_price: 'إلى السعر',
                    entry_discount_type: 'نوع الخصم',
                    entry_discount_value: 'القيمة',
                    entry_fix: 'ثابت',
                    entry_percentage: 'النسبة المئوية',
                    button_done: 'فعله',
                    text_range: 'نطاق',
                    text_to: 'إلى',
                    success_discount_apply: 'النجاح: تم تطبيق الخصم على العربة الحالية بنجاح!',
                },
                tax: {
                    text_tax: 'نموذج الضرائب',
                    add_tax: 'إضافة الضريبة',
                    edit_tax: 'تحرير الضريبة',
                    button_done: 'فعله'
                },
                basic: {
                    entry_select_locale: 'اختر لغة',
                    entry_select_currency: 'اختر العملة'
                },
                profile: {
                    entry_user_profile: 'إعداد ملف تعريف المستخدم',
                    entry_firstname: 'الاسم الاول',
                    entry_lastname: 'الكنية',
                    entry_username: 'اسم المستخدم',
                    entry_email: 'حساب البريد الإلكتروني',
                    entry_pre_password: 'كلمة المرور السابقة',
                    entry_new_password: 'كلمة السر الجديدة',
                    entry_confirm_password: 'تأكيد كلمة المرور',
                    button_done: 'فعله'
                }
            },
            error: {
                required_fromprice: 'تحذير: ملء حقل السعر!',
                required_toprice: 'تحذير: ملء حقل السعر!',
                grather_fromprice: 'تحذير: من السعر لا يمكن أن يكون أكبر من السعر',
                no_discount: 'تحذير: لا توجد قواعد متاحة!',
                no_taxes: 'تحذير: لا توجد ضرائب متاحة!'
            }
        },
        pos_setting_discount: {
            title: 'نقاط البيع'
        },
        pos_setting_taxes: {
            title: 'ضرائب البيع'
        },
        pos_setting_basic: {
            title: 'وضع الإعداد الأساسي'
        },
        pos_setting_profile: {
            title: 'موقف الملف الشخصي'
        }
    },
    el: {
        pos_common_component: {
            modal: {
                header_title: 'Προεπιλεγμένη κεφαλίδα',
                body: 'Προκαθορισμένο σώμα'
            }
        },
        pos_login: {
            title: 'Pos Σύνδεση',
            username: 'Ονομα χρήστη',
            password: 'Κωδικός πρόσβασης',
            remember_password: 'Να θυμάσαι τον κωδικό',
            button_title: 'Σύνδεση',
            forgot_passward: 'Ξεχάσατε τον κωδικό;',
            footer_note: 'Ένα προϊόν του <a href="{url}" target="_blank"> WEBKUL </a>',
            invalid_creds: 'Προειδοποίηση: Ελέγξτε τα διαπιστευτήριά σας και δοκιμάστε ξανά!',
            unauth_user: 'Προειδοποίηση: Δεν είστε εγκεκριμένος χρήστης, παρακαλώ επικοινωνήστε με το admin!'
        },
        pos_view: {
            title: 'Pos Σύστημα',
            error_offline_mode: 'Προειδοποίηση: Αυτή τη στιγμή βρίσκεστε σε λειτουργία χωρίς σύνδεση!',
            error_offline_action: 'Προειδοποίηση: Δεν μπορείτε να εκτελέσετε αυτήν την ενέργεια σε λειτουργία εκτός σύνδεσης!',
            online_mode: 'Επιτυχία: Έχετε εισάγει επιτυχώς σε λειτουργία online!',
            offline_mode: 'Προειδοποίηση: Έχετε εισάγει επιτυχώς σε λειτουργία χωρίς σύνδεση!',
        },
        pos_home: {
            title: 'Pos Σύστημα',
            navtop: {
                heading: 'Σημείο πώλησης',
                search_placeholder: 'Αναζήτηση προϊόντος με όνομα, SKU',
                btn_search: 'Ψάξιμο',
                entry_product_search: 'Αναζήτηση προϊόντος',
                search_barcode_placeholder: 'Αναζήτηση προϊόντος μέσω γραμμικού κώδικα',
                cashier: 'Ταμίας',
                add_custom_product: 'Προσθήκη προσαρμοσμένου προϊόντος',
                entry_product_name: 'όνομα προϊόντος',
                entry_sku: 'Γκόα',
                entry_price: 'Τιμή',
                entry_quantity: 'Ποσότητα',
                button_cart: 'Προσθήκη στο καλάθι',
                success_add_to_cart: 'Επιτυχία: Το προσαρμοσμένο προϊόν προστέθηκε στο τρέχον καλάθι με επιτυχία!',
                add_custom_product_tooltip: 'Προσθήκη προσαρμοσμένου προϊόντος',
                reset_data_tooltip: 'Επαναφορά των δεδομένων Pos',
                offline_mode_tooltip: 'Μεταβείτε στη λειτουργία εκτός σύνδεσης / σε σύνδεση',
                logout_tooltip: 'Αποσυνδεδεμένος χρήστης',
            },
            pos_categories: {
                all: 'Ολα',
                selected: 'Επιλέξτε Κατηγορία',
                all_category_listing: 'Όλες οι κατηγορίες κατηγοριών',
            },
            pos_products: {
                product_variation: 'Παραλλαγές προϊόντων',
                product_discount_tooltip: 'Πληροφορίες: Αυτό το προϊόν είναι σε έκπτωση.',
                product_variation_tooltip: 'Πληροφορίες: Το προϊόν περιέχει παραλλαγές.',
                total_product_info: 'Πληροφορίες: Συνολικά {total_product} προϊόντα που βρέθηκαν σε αυτό το Outlet.',
                total_product_loaded: 'Επιτυχία: Συνολικά {total_loaded} προϊόντα φορτώθηκαν στην έξοδο από {outof_total} προϊόντα.',
                warning_select_product_variation: 'Παρακαλώ επιλέξτε πρώτα τις παραπάνω επιλογές.',
                success_add_to_cart: 'Επιτυχία: Προϊόν που προστέθηκε στο καλάθι με επιτυχία!',
                btn_done: 'Ολοκληρώθηκε',
                btn_add_to_cart: 'Προσθήκη στο καλάθι',
                grouped_product: 'Ομαδοποιημένο προϊόν',
                name: 'Ονομα',
                quantity: 'Ποσότητα',
                customize_options: 'Προσαρμογή επιλογών',
                choose_selection: 'Διάλεξε μια επιλογή',
                none: 'Κανένας',
                total_amount: 'Συνολικό ποσό',
                downloadable_options: 'Επιλογές με δυνατότητα λήψης',
                download_samples: 'Περιεχόμενα δείγματος',
                download_links: 'Περιεχόμενα συνδέσμου',
                sample: 'Δείγμα',
                booking_details: 'Λεπτομέρειες κράτησης',
                location: 'Τοποθεσία',
                view_on_map: 'Προβολή στο χάρτη',
                book_appointment: 'Κλείσε ένα ραντεβού :',
                book_date: 'Ημερομηνία κλεισίματος',
                book_slot: 'Θυρίδα',
                slot_duration: 'Διάρκεια αυλακώσεων:',
                slot_minutes: '{λεπτά} Λεπτά',
                today_availability: 'Σήμερα Διαθεσιμότητα',
                booking_closed: 'Κλειστό',
                show_all_days: 'Εμφάνιση για όλες τις ημέρες',
                event_on: 'Εκδήλωση ενεργοποιημένη',
                book_your_ticket: 'Κάντε κράτηση του εισιτηρίου σας',
                select_from_date: 'Επιλέξτε Από ημερομηνία',
                select_to_date: 'Επιλέξτε Προς ημερομηνία',
                rent_an_item: 'Νοικιάστε ένα είδος',
                placeholder_from: 'Από την ημερομηνία',
                placeholder_to: 'Μέχρι σήμερα',
                select_slot: 'Επιλέξτε Υποδοχή ημερομηνίας',
                placeholder_select_date: 'Επιλέξτε Υποδοχή ημερομηνίας',
                placeholder_select_slot: 'Επιλέξτε Χρόνος χρόνου',
                select_time_slot: 'Επιλέξτε Χρόνος χρόνου',
                rent_time_from: 'Χρόνος ενοικίασης από',
                rent_time_to: 'Χρόνος ενοικίασης',
                select_rent_time: 'Επιλέξτε Ώρα ενοικίασης',
                chosse_rent_options: 'Επιλέξτε Επιλογή ενοικίασης',
                daily_basis: 'Σε καθημερινή βάση',
                hourly_basis: 'Ωριαία βάση',
                book_restaurant_table: 'Βιβλίο τραπέζι εστιατορίου',
                special_note: 'Ειδικό αίτημα / Σημειώσεις',
                book_your_slot: 'Κάντε κράτηση για τον κουλοχέρη σας',
                booking_from: 'Κράτηση από',
                booking_till: 'Κράτηση μέχρι',
                event_ticket: 'Εισιτήριο εκδήλωσης',
                event_from: 'Εκδήλωση από',
                event_till: 'Εκδήλωση μέχρι',
                rent_type: 'Τύπος ενοικίασης',
                rent_from: 'Ενοικίαση από',
                rent_till: 'Καθαρίστε μέχρι',
                error: {
                    no_product: 'Προσοχή: Δεν βρέθηκε προϊόν σε αυτήν την κατηγορία.',
                    no_barcode_provided: 'Προειδοποίηση: παρέχετε barcode για αναζήτηση στο προϊόν!',
                    no_barcode_product: 'Προσοχή: Δεν βρέθηκε προϊόν σε αυτήν την έξοδο που να σχετίζεται με τον παρεχόμενο κωδικό barcode.',
                    no_quantity: 'Προειδοποίηση: Η ποσότητα για αυτό το προϊόν δεν είναι διαθέσιμη.',
                    no_available_qty: 'Προειδοποίηση: Δεν μπορείτε να προσθέσετε ποσότητα μεγαλύτερη από την διαθέσιμη ποσότητα {available_qty}.',
                    select_atleast_one: 'Προειδοποίηση: Πρέπει να επιλέξετε τουλάχιστον μία υποδοχή εισιτηρίων για να κλείσετε μια εκδήλωση.',
                }
            },
            pos_custom_product: {
                choose_product: 'Προειδοποίηση: Επιλέξτε πρώτα το καλάθι σας!',
                correct_qty_price: 'Προειδοποίηση: Παρακαλείστε να δώσετε τη σωστή ποσότητα και τιμή για το έθιμο προϊόν!',
            },
            pos_cart: {
                cart_details: 'Λεπτομέρειες καλαθιού',
                text_with: 'με',
                text_unit: 'Μονάδα (ες)',
                text_per_unit: 'ανά μονάδα',
                delete_current_cart: 'Διαγραφή τρέχουσας κάρτας',
                button_customer: 'Πελάτης',
                button_pay: 'Πληρωμή',
                button_hold: ' Κρατήστε',
                text_cart_empty: 'Το τρέχον καλάθι είναι άδειο!',
                apply_discount: 'Εφαρμογή έκπτωσης στο καλάθι',
                entry_available_discount: 'Επιλέξτε Έκπτωση',
                entry_select_discount: '- Επιλέξτε Έκπτωση -',
                button_apply_discount: 'Εφαρμογή έκπτωσης',
                hold_order_note: 'Σημείωση παραγγελίας',
                entry_order_note: 'Παρέχετε σημείωση παραγγελίας',
                placeholder_note: 'Εισαγάγετε σημείωση για παραγγελία ..',
                button_hold_order: 'Κράτηση παραγγελίας',
                button_cancel: 'Ματαίωση',
                success_hold_cart: 'Επιτυχία: Το τρέχον καλάθι έχει προστεθεί στη λίστα αναμονής με επιτυχία!',
                text_error: 'Λάθος',
                text_confirm: 'Επιβεβαιώνω',
                text_confirm_msg: 'Επιβεβαίωση: Αυτή η διαδικασία θα δημιουργήσει μια εντολή ανάλογα με την κατάσταση Συνδεδεμένοι ή Εκτός σύνδεσης. Θέλετε ακόμα να το κάνετε;',
                text_cancel: 'Ματαίωση',
                text_sub_total: 'ΜΕΡΙΚΟ ΣΥΝΟΛΟ',
                text_discount: 'Εκπτωση σε πληρωμή τοις μετρητοίς',
                text_tax_total: 'Φόρος Σύνολο',
                text_home_delivery: 'Παράδοση στο σπίτι',
                text_grand_total: 'Σύνολο',
                error: {
                    error_plus_cart: 'Προειδοποίηση: Δεν μπορείτε να προσθέσετε το καλάθι περισσότερο από {cart_count}!',
                    error_cart_empty: 'Προειδοποίηση: Το τρέχον καλάθι είναι άδειο!',
                    error_customer_empty: 'Προειδοποίηση: Πρέπει πρώτα να επιλέξετε τον πελάτη για αυτήν την παραγγελία!',
                    text_amount_error: 'Προειδοποίηση: Δεν μπορείτε να πληρώσετε το ποσό που έχετε εισάγει!',
                    error_no_cart: 'Προειδοποίηση: Δεν υπάρχει διαθέσιμο καλάθι, δοκιμάστε ξανά!',
                    error_no_product: 'Προσοχή: Δεν υπάρχει προϊόν που να προστίθεται στο τρέχον καλάθι, δοκιμάστε ξανά!',
                    error_no_customer: 'Προειδοποίηση: Δεν επιλέχθηκε πελάτης για το τρέχον καλάθι, δοκιμάστε ξανά!',
                    error_select_all_attributes: 'Προσοχή: Παρακαλούμε επιλέξτε όλα τα χαρακτηριστικά του προϊόντος!',
                }
            }
        },
        pos_payment: {
            title: 'Pos Πληρωμή',
            heading_title: 'Πληρωμή',
            heading_order_print: 'Εκτύπωση τιμολογίου',
            vertical_menu: {
                text_cash: ' Πληρωμή με μετρητά',
                text_credit: ' Πιστωτική Πληρωμή',
            },
            pos_cash: {
                text_total: 'Σύνολο',
                text_tendered: 'Προσφέρονται',
                text_change: 'Αλλαγή',
                placeholder_order_comment: 'Προσθέστε την παραγγελία εδώ ...',
                button_confirm_pay: '  Επιβεβαίωση πληρωμής',
            },
            btn_print: 'Τυπώνω',
            btn_skip: 'Παραλείπω',
            success_offline_order: 'Επιτυχία: Η παραγγελία έχει τεθεί σε λειτουργία χωρίς σύνδεση με επιτυχία!',
            text_order_success: 'Επιτυχία: Η παραγγελία σας έχει τοποθετηθεί με επιτυχία!',
        },
        pos_payment_cash: {
            title: 'Πληρωμή με μετρητά',
        },
        pos_payment_credit: {
            title: 'Πιστωτική Πληρωμή',
        },
        pos_sales: {
            title: 'Pos Πωλήσεις',
            top_menu: {
                sales_history: 'Ιστορικό πώλησης',
                hold_sale: 'Κρατήστε Πώληση',
                offline_sale: 'Offline πώληση',
            },
            menu_content: {
                sales_history: {
                    placeholder_search: 'Αναζήτηση βάσει αναγνωριστικού παραγγελίας',
                    text_order_id: 'Αριθμός Παραγγελίας',
                    text_order_date: 'Ημερομηνία παραγγελίας',
                    text_customer_info: 'Λεπτομέρειες Πελατών',
                    text_order_note: 'Σημείωση παραγγελίας',
                    button_print_invoice: 'Εκτύπωση τιμολογίου',
                    text_order_summary: 'Σύνοψη Παραγγελίας',
                    text_units: ' Μονάδα (ες)',
                    text_per_unit: ' ανά μονάδα',
                    text_sub_total: 'ΜΕΡΙΚΟ ΣΥΝΟΛΟ',
                    text_discount: 'Έκπτωση (-)',
                    text_taxes: 'Φόροι (+)',
                    text_grand_total: 'Σύνολο',
                    text_mode_cash: 'Πληρωμή με μετρητά',
                    text_mode_card: 'Πληρωμή με κάρτα',
                    text_balance: 'Ισορροπία',
                },
                hold_sales: {
                    no_hold_order: 'Προειδοποίηση: Δεν υπάρχει εντολή κράτησης!',
                    text_note: ' Σημείωση',
                    text_hold_product_details: 'Κρατήστε Προϊόντα Λεπτομέρειες:',
                    button_cart: ' Προσθήκη στο καλάθι',
                    button_delete: ' Αφαιρώ',
                    success_remove_hold: 'Επιτυχία: Το τρέχον καλάθι έχει διαγραφεί με επιτυχία!',
                    success_move_cart: 'Επιτυχία: Το επιλεγμένο καλάθι κατόχου έχει μετακινηθεί με επιτυχία στο τρέχον καλάθι!',
                    no_hold_order: 'Προειδοποίηση: Δεν υπάρχει διαθέσιμη παραγγελία!',
                },
                offline_sales: {
                    order_id: 'Αριθμός Παραγγελίας',
                    order_ref_id: 'Παραγγελία Αναφ. Ταυτότητα',
                    order_date: 'Ημερομηνία παραγγελίας',
                    item_detail: 'Λεπτομέρειες στοιχείου',
                    btn_view: 'Θέα',
                    btn_sync: 'Συγχρονισμός',
                    customer_detail: 'Λεπτομέρειες Πελατών',
                    order_detail: 'Λεπτομέρειες παραγγελίας',
                    no_offline_order: 'Προσοχή: Επί του παρόντος δεν έχει επιλεγεί καμία παραγγελία!',
                    dont_refresh: 'Σημείωση: Μην ανανεώνετε τη σελίδα / ανακατευθύνετε μέχρι να ολοκληρωθεί η διαδικασία συγχρονισμού!',
                    total_offline_order: 'Πληροφορίες: Σύνολο {total_order_found} εντολών εκτός σύνδεσης βρέθηκαν (επεξεργασία ...)',
                    total_sync_order: 'Επιτυχία: η σειρά {total_sync} συγχρονίστηκε με επιτυχία!',
                    total_sync_success: 'Επιτυχία: Οι παραγγελίες εκτός σύνδεσης συγχρονίστηκαν με επιτυχία!',
                    no_offline_order: 'Προειδοποίηση: Δεν υπάρχει εντολή εκτός σύνδεσης!',
                    btn_all_sync: 'Συγχρονισμός όλων των εντολών εκτός σύνδεσης',
                    sync_all_order: 'Συγχρονισμός όλων των εντολών εκτός σύνδεσης',
                },
                print_invoice: {
                    email: 'ΗΛΕΚΤΡΟΝΙΚΗ ΔΙΕΥΘΥΝΣΗ: ',
                    website: 'Δικτυακός τόπος: ',
                    phone: 'Τηλέφωνο: ',
                    customer_care: 'Εξυπηρέτηση πελατών: ',
                    gstin: 'GSTIN: ',
                    date: 'Ημερομηνία: ',
                    order_id: 'Αριθμός Παραγγελίας: ',
                    order_ref_id: 'Παραγγελία Αναφ. ταυτότητα: ',
                    cashier: 'Ταμίας: ',
                    customer: 'Πελάτης: ',
                    mode_of_shipping: 'Τρόπος ναυτιλίας: ',
                    mode_of_payment: 'Τρόπος Πληρωμής: ',
                    pickup: 'Μαζεύω',
                    cash_payment: 'Πληρωμή με μετρητά',
                    card_payment: 'Πληρωμή με κάρτα',
                    product: 'Προϊόν',
                    quantity: 'Ποσότητα',
                    price: 'Τιμή',
                    amount: 'Ποσό',
                    total_qty: 'Συνολική ποσότητα: ',
                    sub_total: 'ΜΕΡΙΚΟ ΣΥΝΟΛΟ: ',
                    discount: 'Εκπτωση: ',
                    tax: 'Φόρος: ',
                    total: 'Σύνολο: ',
                    balance: 'Ισορροπία: ',
                    order_note: 'Σημείωση παραγγελίας: ',
                    footer_note_1: 'Να εχετε μια καλη μερα',
                    footer_note_2: 'Σας ευχαριστώ. Επίσκεψη ξανά',
                }
            },
            error: {
                no_order_record: 'Προειδοποίηση: Δεν βρέθηκε παραγγελία!',
            }
        },
        pos_sales_history: {
            title: 'Ιστορικό πωλήσεων'
        },
        pos_sales_hold: {
            title: 'Κράτηση παραγγελίας'
        },
        pos_sales_offline: {
            title: 'Offline πώληση'
        },
        pos_customer: {
            title: 'Pos Πελάτης',
            top_menu: {
                title: 'Οι πελάτες',
            },
            menu_content: {
                search_area: {
                    placeholder_search: 'Αναζήτηση Πελατών βάσει ονόματος',
                },
                add_customer: ' Προσθήκη πελάτη',
                edit_customer: ' Επεξεργασία πελάτη',
                success_customer_cart: 'Επιτυχία: Ο πελάτης έχει επιλεγεί για checkout!',
                success_customer_already_cart: 'Επιτυχία: Αυτός ο πελάτης έχει ήδη επιλεγεί για ολοκλήρωση παραγγελίας!',
                success_customer_remove: 'Επιτυχία: Ο πελάτης καλαθιού αφαιρέθηκε με επιτυχία!',
                success_customer_added: 'Επιτυχία: Ο πελάτης προστέθηκε στη λειτουργία εκτός σύνδεσης με επιτυχία!',
                button_select_customer: 'Επιλέξτε Πελάτη',
                text_edit: ' Επεξεργασία',
                text_remove: ' Αφαιρώ',
                entry_firstname: 'Ονομα',
                entry_lastname: 'Επίθετο',
                entry_email: 'ΗΛΕΚΤΡΟΝΙΚΗ ΔΙΕΥΘΥΝΣΗ',
                entry_gender: 'Γένος',
                entry_male: 'Αρσενικός',
                entry_female: 'Θηλυκός',
                entry_dob: 'Ημερομηνια γεννησης',
                entry_customer_group: 'Ομάδα πελατών',
                button_save: 'Αποθηκεύσετε',
            },
            error: {
                no_customer_record: 'Προειδοποίηση: Δεν βρέθηκε πελάτης!',
                no_customer_selected: 'Προσοχή: Επί του παρόντος δεν έχει επιλεγεί πελάτης!',
            }
        },
        pos_cashier: {
            title: 'Pos Ταμίας',
            top_menu: {
                close_counter: 'Κλείσιμο μετρητή',
                today_cash: 'Σήμερα μετρητά',
                sales_history: 'Ιστορικό πωλήσεων'
            },
            menu_content: {
                text_opening_amount: 'Ποσό ανοίγματος συρταριού',
                entry_amount: 'Ποσό ανοίγματος',
                entry_remark: 'Παρατηρήσεις',
                button_open_store: 'Άνοιγμα καταστήματος',
                close_counter: {
                    text_drawer_amount: 'Λεπτομέρειες σχετικά με το μέγεθος του συρταριού',
                    text_counted_amount: 'Μετρημένο Ποσό Συρταριού',
                    text_closing_details: 'Κλείσιμο Στοιχείων Συρταριού',
                    text_open_amount: 'Ποσό ανοίγματος',
                    text_total_cash: 'Το σημερινό συνολικό ποσό μετρητών',
                    text_current_drawer_sale: 'Τρέχουσα πώληση συρταριού',
                    text_expected_amount: 'Αναμενόμενο ποσό στο συρτάρι',
                    text_amount: 'Ποσό ({currency})',
                    text_remark: 'Παρατήρηση',
                    text_remark_placeholder: 'Σχόλιο σχετικά με ταμειακά διαθέσιμα ..',
                    text_difference: 'Διαφορά μεταξύ του ποσού πώλησης του ανοίγματος και του τρέχοντος συρταριού',
                    text_total_drawer_amount: 'Συνολικό ποσό στο συρτάρι',
                    button_close_drawer: 'Κλείστε το συρτάρι',
                },
                sales_history: {
                    order_id: 'Αριθμός Παραγγελίας',
                    order_date: 'Ημερομηνία παραγγελίας',
                    order_total: 'Παραγγελία Σύνολο',
                    payment_mode: 'Τρόπος πληρωμής'
                },
                today_cash: {
                    total_cash_sale: 'Σήμερα πώληση μετρητών',
                    total_card_sale: 'Σήμερα Πώληση καρτών'
                }
            }
        },
        pos_cashier_closecounter: {
            title: 'Κλείσιμο μετρητή'
        },
        pos_cashier_todaycash: {
            title: 'Σήμερα μετρητά'
        },
        pos_cashier_saleshistory: {
            title: 'Ιστορικό πωλήσεων'
        },
        pos_reports: {
            title: 'Pos Αναφορές'
        },
        pos_product: {
            title: 'Pos προϊόν',
            top_menu: {
                low_stock: 'Χαμηλό απόθεμα προϊόντος',
                request_list: 'Ζητούμενα προϊόντα',
                setting: 'Σύνθεση'
            },
            menu_content: {
                low_stock: {
                    text_left: ' Αριστερά',
                    text_request_product: 'Αίτηση προϊόντος',
                    request_product: 'Προσθήκη προϊόντος για ζήτηση',
                    entry_required_qty: 'Απαιτούμενη ποσότητα',
                    entry_request_to_supplier: 'Αίτηση προς τον προμηθευτή',
                    entry_select: 'Επιλέξτε προμηθευτή',
                    entry_comment: 'Σχόλιο',
                    text_qty: ' Ποσότητα',
                    entry_supplier_name: 'Όνομα προμηθευτή',
                    entry_comment: 'Σχόλιο',
                    button_add: 'Πρόσθεσε στη λίστα',
                    button_send: 'Στείλε αίτημα'
                },
                request_list: {
                    column_id: 'Ταυτότητα',
                    column_name: 'Ονομασία προϊόντος',
                    column_qty: 'Ποσότητα',
                    column_supplier: 'Προμηθευτής',
                    column_comment: 'Σχόλιο',
                    column_date: 'Ημερομηνία',
                    column_status: 'Κατάσταση',
                    no_record_found: 'Δεν βρέθηκε αίτημα',
                    action_pending: 'εκκρεμής',
                    action_complete: 'Πλήρης',
                    action_decline: 'Πτώση',
                },
                setting: {
                    entry_product_setting: 'Ρύθμιση προϊόντος Pos',
                    entry_low_stock: 'Ελάχιστη Μονάδα Ποσότητας για Προϊόν Χαμηλού Αποθέματος',
                    button_done: 'Ολοκληρώθηκε'
                }
            },
            error: {
                no_lowstock_product: 'Προειδοποίηση: Δεν υπάρχει χαμηλό προϊόν αποθεμάτων!'
            }
        },
        pos_product_low_stock: {
            title: 'Προϊόντα χαμηλού αποθέματος'
        },
        pos_product_requested: {
            title: 'Ζητούμενα προϊόντα',
            placeholder_search: 'Αναζήτηση ζητούμενου προϊόντος με όνομα'
        },
        pos_product_setting: {
            title: 'Ρύθμιση προϊόντος'
        },
        pos_setting: {
            title: 'Pos Σύνθεση', 
            top_menu: {
                discount: 'Έκπτωση αφοσίωσης',
                taxes: 'Φόροι',
                basic_setting: 'Βασική ρύθμιση',
                profile_setting: 'Ρύθμιση προφίλ'
            },
            menu_content: {
                discount: {
                    text_discount: 'Φόρμα Έκπτωσης',
                    add_discount: 'Προσθέστε έκπτωση',
                    edit_discount: 'Επεξεργασία έκπτωσης',
                    entry_offer_name: 'Όνομα προσφοράς',
                    entry_from_price: 'Από την τιμή',
                    entry_to_price: 'Προς τιμή',
                    entry_discount_type: 'Τύπος έκπτωσης',
                    entry_discount_value: 'αξία',
                    entry_fix: 'Σταθερός',
                    entry_percentage: 'Ποσοστό',
                    button_done: 'Ολοκληρώθηκε',
                    text_range: 'Εύρος',
                    text_to: 'Προς το',
                    success_discount_apply: 'Επιτυχία: Η έκπτωση που εφαρμόστηκε στο τρέχον καλάθι με επιτυχία!',
                },
                tax: {
                    text_tax: 'Φορολογική μορφή',
                    add_tax: 'Προσθήκη Φόρου',
                    edit_tax: 'Επεξεργασία φόρου',
                    button_done: 'Ολοκληρώθηκε'
                },
                basic: {
                    entry_select_locale: 'Επιλέξτε τοπικό',
                    entry_select_currency: 'Επιλέξτε Νόμισμα'
                },
                profile: {
                    entry_user_profile: 'Ρύθμιση προφίλ χρήστη',
                    entry_firstname: 'Ονομα',
                    entry_lastname: 'Επίθετο',
                    entry_username: 'Όνομα χρήστη',
                    entry_email: 'Λογαριασμός ηλεκτρονικού ταχυδρομείου',
                    entry_pre_password: 'Προηγούμενος κωδικός πρόσβασης',
                    entry_new_password: 'Νέος Κωδικός',
                    entry_confirm_password: 'Επιβεβαίωση Κωδικού',
                    button_done: 'Ολοκληρώθηκε'
                }
            },
            error: {
                required_fromprice: 'Προειδοποίηση: Συμπληρώστε από το πεδίο τιμής!',
                required_toprice: 'Προειδοποίηση: Συμπληρώστε το πεδίο τιμών!',
                grather_fromprice: 'Προειδοποίηση: Από-τιμή δεν μπορεί να είναι μεγαλύτερη από την τιμή',
                no_discount: 'Προειδοποίηση: Δεν υπάρχουν διαθέσιμοι κανόνες!',
                no_taxes: 'Προειδοποίηση: Δεν υπάρχουν διαθέσιμοι φόροι!'
            }
        },
        pos_setting_discount: {
            title: 'Pos Έκπτωση'
        },
        pos_setting_taxes: {
            title: 'Pos Φόροι'
        },
        pos_setting_basic: {
            title: 'Βασική ρύθμιση Pos'
        },
        pos_setting_profile: {
            title: 'Pos Προφίλ'
        }
    }
}