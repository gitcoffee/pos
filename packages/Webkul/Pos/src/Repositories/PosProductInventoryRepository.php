<?php

namespace Webkul\Pos\Repositories;

use Illuminate\Container\Container as App;
use Webkul\Core\Eloquent\Repository;
use DB;

/**
 * Product Repository
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductInventoryRepository extends Repository
{

    public function __construct(
        App $app)
    {
        parent::__construct($app);
    }

    /**->where('product_flat.visible_individually', 1)
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Product\Contracts\ProductInventory';
    }

    public function getSimpleProductQty($product)
    {
        $quantity = 0;
        $productInventory = $this->findWhere(['product_id' => $product['product_id'], 'inventory_source_id' => $product['pos_inventory_source_id']])->first();

        if ($productInventory->id) {
            $quantity = $productInventory->qty;
            return $quantity;
        } else {
            return $quantity;
        }
    }
}