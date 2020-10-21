<?php

namespace Webkul\Pos\Repositories;

use Illuminate\Container\Container as App;
use DB;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\Repository;
use Webkul\Sales\Repositories\OrderRepository;
use Image;

/**
 * PosOrder Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOrderRepository extends Repository
{
    public function model() {
        return 'Webkul\Pos\Contracts\PosOrder';
    }

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;
    
    protected $order;

    public function __construct(
        OrderRepository $order,
        App $app
    )
    {
        $this->_config = request('_config');

        $this->order = $order;

        parent::__construct($app);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        $params = request()->input();

        if (isset($params['filter_cart_total'])) {
            $params['filter_cart_total'] = core()->convertToBasePrice($params['filter_cart_total']);
        }
        
        $results = app('Webkul\Sales\Repositories\OrderRepository')->scopeQuery(function($query) use($params) {

                $qb = $query->distinct()
                        ->addSelect('orders.*')
                        ->addSelect('pos_order.id AS pos_entry_id')
                        ->addSelect('pos_order.order_id AS pos_order_id')
                        ->addSelect('pos_order.user_id AS user_id')
                        ->addSelect('pos_order.order_ref_id AS order_ref_id')
                        ->addSelect('pos_order.order_barcode_path AS order_barcode_path')
                        ->addSelect('pos_customer_credit.payment_mode AS payment_mode')
                        ->addSelect('pos_order.bank_name AS bank_name')
                        ->addSelect('pos_order.order_note AS pos_order_note')
                        ->addSelect('pos_users.status AS user_status')
                        ->addSelect('customers.phone AS customer_phone')
                        ->addSelect('pos_restaurant_table_bookings.booking_id')
                        ->addSelect('pos_restaurant_table_bookings.booked_date')
                        ->addSelect('pos_restaurant_table_bookings.booked_time_from')
                        ->addSelect('pos_restaurant_table_bookings.booked_time_to')
                        ->addSelect('pos_restaurant_table_bookings.booked_seat')
                        ->addSelect('pos_restaurant_tables.name AS table_name')
                        ->addSelect('pos_restaurant_tables.type AS table_type')
                        ->leftJoin('pos_order', 'pos_order.order_id', '=', 'orders.id')
                        ->leftJoin('pos_restaurant_table_bookings', 'orders.id', '=', 'pos_restaurant_table_bookings.order_id')
                        ->leftJoin('pos_restaurant_tables', 'pos_restaurant_table_bookings.table_id', '=', 'pos_restaurant_tables.id')
                        ->leftJoin('customers', 'orders.customer_email', '=', 'customers.email')
                        ->leftJoin('pos_customer_credit', 'pos_order.order_id', '=', 'pos_customer_credit.order_id')
                        ->leftJoin('pos_users', 'pos_users.id', '=', 'pos_order.user_id')
                        ->where('pos_users.status', 1)
                        ->orderBy('pos_order.order_id', 'DESC');

                if (isset($params['filter_order']) && $params['filter_order']) {
                    $order_filter = $params['filter_order'];
                    $order_array = explode("#", $params['filter_order']);

                    if ( count($order_array) > 1 && isset($order_array[1]) ) {
                        $order_filter = $order_array[1];
                    } else if (isset($order_array[0])) {
                        $order_filter = $order_array[0];
                    }
                    
                    $qb->where('pos_order.order_id', 'LIKE', '%' . $order_filter . '%')
                    ->orWhere('pos_order.order_ref_id', 'LIKE', '%' . $order_filter . '%')
                    ->orWhere('orders.customer_email', 'LIKE', '%' . $order_filter . '%')
                    ->orWhere('pos_restaurant_tables.name', 'LIKE', '%' . $order_filter . '%')
                    ->orWhere('pos_restaurant_table_bookings.booking_id', 'LIKE', '%' . $order_filter . '%')
                    ->orWhere(DB::raw("CONCAT(pos_restaurant_table_bookings.booked_date, ' ', pos_restaurant_table_bookings.booked_time_from)"), 'LIKE', '%' . $order_filter .'%')
                    ->orWhere(DB::raw("CONCAT(pos_restaurant_table_bookings.booked_date, ' ', pos_restaurant_table_bookings.booked_time_to)"), 'LIKE', '%' . $order_filter .'%')
                    ->orWhere(DB::raw("CONCAT(pos_restaurant_table_bookings.booked_time_from, ' - ', pos_restaurant_table_bookings.booked_time_to)"), 'LIKE', '%' . $order_filter .'%');
                }

                if (isset($params['user_id']) && $params['user_id']) {
                    $qb->where('pos_order.user_id', $params['user_id']);
                }

                if (isset($params['outlet_id']) && $params['outlet_id']) {
                    $qb->where('pos_order.outlet_id', $params['outlet_id']);
                }

                if (isset($params['filter_date']) && $params['filter_date']) {
                    $qb->where('pos_order.created_at', 'LIKE', '%'.date("Y-m-d").'%');
                }

                if (isset($params['filter_drawer_date']) && $params['filter_drawer_date']) {
                    $qb->where('pos_order.created_at', '>', $params['filter_drawer_date']);
                }
                
                if ( isset($params['filter_amount']) ) {
                    if ($params['filter_amount'] == 'cash') {
                        $qb->where('pos_customer_credit.payment_mode', 'cash');
                    }
                    if ($params['filter_amount'] == 'card') {
                        $qb->where('pos_customer_credit.payment_mode', 'card');
                    }

                    $qb->addSelect(DB::raw('SUM(orders.grand_total) as total_amount'));
                    $qb->addSelect(DB::raw('SUM(orders.base_grand_total) as base_total_amount'));
                    
                    return $qb->groupBy('pos_order.user_id');
                }

                return $qb->groupBy('pos_order.id');
            })->paginate(isset($params['limit']) ? $params['limit'] : 10);

        return $results;
    }

    public function generateOrderBarcode($id)
    {
        $barcode_dir_path = storage_path('app/public/order_barcode/');

        if (!file_exists($barcode_dir_path)) {
            mkdir(storage_path('app/public/order_barcode/'), 0777, true);
        }
        
        $barcode_file_path  = __DIR__ . '/../barcode.php';
        require_once($barcode_file_path);

        $filepath = $barcode_dir_path . $id . '.png';

        $save_db_path = '/order_barcode/' . $id . '.png';

        $config_barcode_size = core()->getConfigData('pos.configuration.barcode.size');

		if ($config_barcode_size) {
			$size = $config_barcode_size;
		} else {
			$size = 20;
        }
        
        $config_barcode_type = core()->getConfigData('pos.configuration.barcode.image_type');

        $barcode = $this->findWhere(['order_id' => $id])->first();

        if ( isset($barcode->order_id) && $barcode->order_id == $id ) {
            Storage::delete('/order_barcode/' . $id . '.png');
        }

        barcode($filepath, $id, $size, $config_barcode_type);

        Image::cache(function($image) use($filepath) {
            return $image->make($filepath)->resize(300, 200)->greyscale();
        });

		return $save_db_path;
    }

    /**
     * Search Product by Attribute
     *
     * @return Collection
     */
    public function searchProductByAttribute($term)
    {
        $results = app('Webkul\Sales\Repositories\OrderItemRepository')->scopeQuery(function($query) use($term) {

                return $query->distinct()
                        ->addSelect('order_items.id', 'order_items.name', 'order_items.type', 'order_items.sku', 'order_items.product_id')
                        ->addSelect('orders.id as main_order_id')
                        ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('order_items.name', 'like', '%' . urldecode($term) . '%')
                        ->orderBy('order_items.product_id', 'desc')
                        ->groupBy('order_items.name');
            })->paginate(16);

        return $results;
    }
}