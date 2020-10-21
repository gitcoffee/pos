<?php

namespace Webkul\Pos\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Repositories\ProductRepository as Product;
use Webkul\Product\Repositories\ProductFlatRepository as ProductFlat;
use Image;

/**
 * PosProduct Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductRepository extends Repository
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    protected $product;

    protected $productFlat;

    public function __construct(
        App $app,
        Product $product,
        ProductFlat $productFlat
    )
    {
        $this->_config = request('_config');

        $this->product = $product;

        $this->productFlat = $productFlat;

        parent::__construct($app);
    }

    public function model() {
        return 'Webkul\Pos\Contracts\PosProductBarcode';
    }

    public function generateBarcode($id)
    {
        $image_name = '';

        $product_obj = $this->product->find($id, ['id','sku']);

        $config_barcode_generate_with = core()->getConfigData('pos.configuration.barcode.barcode_with');

		if (isset($product_obj[$config_barcode_generate_with]) && $product_obj[$config_barcode_generate_with]) {
            $barcode_dir_path = storage_path('app/public/product/' . $id . '/');

            if (!file_exists($barcode_dir_path)) {
                mkdir(storage_path('app/public/product/' . $id . '/'), 0777, true);
            }
            
            $barcode_file_path  = __DIR__ . '/../barcode.php';
            require_once($barcode_file_path);

            $image_name .= core()->getConfigData('pos.configuration.barcode.barcode_prefix');

			$image_name .= $product_obj[$config_barcode_generate_with];

            $filepath = $barcode_dir_path . $image_name . '.png';

            $config_barcode_size = core()->getConfigData('pos.configuration.barcode.size');

            if ($config_barcode_size) {
                $size = $config_barcode_size;
            } else {
                $size = 20;
            }
            
            $config_barcode_type = core()->getConfigData('pos.configuration.barcode.image_type');

            $barcode = $this->findWhere(['product_id' => $id]);

            if ( isset($barcode[0]->product_id) && $barcode[0]->product_id == $id ) {
                Storage::delete('/product/' . $id . '/' . $barcode[0]->barcode . '.png');
            }

            barcode($filepath, $image_name, $size, $config_barcode_type);

            Image::cache(function($image) use($filepath){
                return $image->make($filepath)->resize(300, 200)->greyscale();
            });
            
            if (isset($barcode[0]->product_id) && $barcode[0]->product_id == $id) {
                $this->update([
                    'product_id' => $product_obj->id,
                    'barcode' => $image_name
                ], $barcode[0]->id);
            } else {
                $this->create([
                    'product_id' => $product_obj->id,
                    'barcode'   => $image_name
                ]);
            }
        }

        return $image_name;
    }

    public function getBarcode($product_id)
    {
        $barcode = $this->findWhere(['product_id' => $product_id]);

        if (isset($barcode[0]->barcode) && $barcode[0]->barcode) {
            
            $path = public_path('/storage/product/' . $product_id . '/' . $barcode[0]->barcode . '.png');

            if (file_exists($path)) {
                return url('/storage/product/' . $product_id . '/' . $barcode[0]->barcode . '.png');
            } else {
                $image_name = $this->generateBarcode($product_id);
                if (isset($image_name) && $image_name) {
                    $path = public_path('/storage/product/' . $product_id . '/' . $image_name . '.png');
    
                    if (file_exists($path)) {
                        return url('/storage/product/' . $product_id . '/' . $image_name . '.png');
                    }
                }
            }
        } else {
            $image_name = $this->generateBarcode($product_id);
            if (isset($image_name) && $image_name) {
                $path = public_path('/storage/product/' . $product_id . '/' . $image_name . '.png');

                if (file_exists($path)) {
                    return url('/storage/product/' . $product_id . '/' . $image_name . '.png');
                }
            }
        }
    }

    public function getProductName($id)
    {
        $product_name = '';
        $product_obj = $this->productFlat->findWhere(['product_id' => $id, 'locale' => config('app.locale')], ['id', 'name', 'product_id']);

        if ( isset($product_obj[0]->product_id) && $product_obj[0]->product_id == $id ) {
            $product_name = $product_obj[0]->name;
        }
        return $product_name;
    }
}