<?php

namespace Webkul\Pos\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\API\Http\Resources\Catalog\ProductImage;

/**
 * ProductRequest JsonResource
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductRequest extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->productImageHelper = app('Webkul\Product\Helpers\ProductImage');

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        $product = $this->product ? $this->product : $this;
        
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'images' => new ProductImage($product),
            'base_image' => $this->productImageHelper->getProductBaseImage($product),
            'supplier_id' => $this->supplier_id,
            'requested_quantity' => $this->requested_quantity,
            'trim_comment' => mb_substr(strip_tags(html_entity_decode($this->comment, ENT_QUOTES, 'UTF-8')), 0, 100,'UTF-8') . '..',
            'comment' => $this->comment,
            'send_status' => $this->send_status,
            'request_status' => $this->request_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}