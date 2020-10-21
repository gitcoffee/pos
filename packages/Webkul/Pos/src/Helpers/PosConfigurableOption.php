<?php

namespace Webkul\Pos\Helpers;

use Webkul\Product\Helpers\ConfigurableOption;

class PosConfigurableOption extends ConfigurableOption
{
    

    /**
     * Returns the allowed variants
     *
     * @param  \Webkul\Product\Contracts\Product|\Webkul\Product\Contracts\ProductFlat  $product
     * @return array
     */
    public function getAllowedProducts($product)
    {
        $variants = [];

        if (count($variants)) {
            return $variants;
        }

        foreach ($product->variants as $variant) {
            if ($variant->isSaleable()) {
                $variants[] = $variant;
            }
        }

        return $variants;
    }

    /**
     * Get product prices for configurable variations
     *
     * @param  \Webkul\Product\Contracts\Product|\Webkul\Product\Contracts\ProductFlat  $product
     * @return array
     */
    protected function getVariantPrices($product)
    {
        $prices = [];

        foreach ($this->getAllowedProducts($product) as $variant) {
            if ($variant instanceof \Webkul\Product\Models\ProductFlat) {
                $variantId = $variant->product_id;
            } else {
                $variantId = $variant->id;
            }

            $prices[$variantId] = $variant->getTypeInstance()->getProductPrices();
        }

        return $prices;
    }
}