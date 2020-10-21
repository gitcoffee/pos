<?php

namespace Webkul\Pos\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * PosOutlet Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOutletRepository extends Repository
{

    public function model() {
        return 'Webkul\Pos\Contracts\PosOutlet';
    }

    public function attachOrDetach($posOutlet, $data) {
        $outletProducts = $posOutlet->outlet_products;

        $this->model->findOrFail($posOutlet->id)->outlet_products()->sync($data);

        return true;
    }
}