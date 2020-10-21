<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosProductBarcode as PosProductBarcodeContract;

/**
 * PosProductBarcode Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductBarcode extends Model implements PosProductBarcodeContract
{
    protected $table = 'pos_product_barcode';

    protected $fillable = ['id', 'product_id', 'barcode'];

    public $timestamps = false;

    public function product_barcode() {
        return $this->belongsTo(PosProductBarcodeProxy::modelClass());
    }
}