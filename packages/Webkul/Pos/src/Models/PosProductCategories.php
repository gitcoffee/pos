<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosProductCategories as PosProductCategoriesContract;

/**
 * PosProductCategories Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductCategories extends Model implements PosProductCategoriesContract
{
    protected $table = 'product_categories';

    protected $fillable = ['product_id', 'category_id'];
}