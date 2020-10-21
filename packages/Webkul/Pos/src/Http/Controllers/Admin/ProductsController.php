<?php

namespace Webkul\Pos\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Pos\Repositories\PosProductRepository as PosProduct;

/**
 * Products controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductsController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * PosProductGrid Repository object
     *
     * @var array
     */
    protected $posProduct;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PosProduct $posProduct
    )
    {
        $this->posProduct = $posProduct;

        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    public function generateBarcode($id)
    {
        $result = $this->posProduct->generateBarcode($id);

        if ( $result ) {
            session()->flash('success', trans('pos::app.admin.response.barcode-success'));    
        } else {
            session()->flash('warning', 'Warning: There are some error, please try again.');
        }

        return redirect()->back();
    }

    public function printBarcode($id)
    {
        $results = [];

        $barcode_name = core()->getConfigData('pos.configuration.barcode.print_product_name');
        
        $results[] = [
            'img_url'       => $this->posProduct->getBarcode($id),
            'product_name'  => $this->posProduct->getProductName($id)
        ];

        return view($this->_config['view'], [
            'barcode_name' => $barcode_name,
            'barcodes' => $results
        ]);
    }

    public function massupdate()
    {
        $data = request()->all();

        if (!isset($data['massaction-type']) || !isset($data['update-options'])) {
            return redirect()->back();
        }

        if (!$data['massaction-type'] == 'update') {
            return redirect()->back();
        }

        $productIds = explode(',', $data['indexes']);

        $results = [];

        if ($data['update-options'] == 'print') {

            $barcode_name = core()->getConfigData('pos.configuration.barcode.print_product_name');

            foreach ($productIds as $productId) {
                $results[] = [
                    'img_url'       => $this->posProduct->getBarcode($productId),
                    'product_name'  => $this->posProduct->getProductName($productId)
                ];
            }
            return view($this->_config['print_view'], [
                'barcode_name' => $barcode_name,
                'barcodes' => $results
            ]);
                
        } else {
            foreach ($productIds as $productId) {
                $this->posProduct->generateBarcode($productId);
            }

            session()->flash('success', trans('pos::app.admin.response.barcode-success'));

            return redirect()->route($this->_config['redirect']);
        }        
    }
}