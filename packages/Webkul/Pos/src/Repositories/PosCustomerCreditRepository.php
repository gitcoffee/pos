<?php

namespace Webkul\Pos\Repositories;

use Illuminate\Container\Container as App;
use DB;
use Webkul\Core\Eloquent\Repository;

/**
 * PosCustomerCredit Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosCustomerCreditRepository extends Repository
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    public function __construct(
        App $app
    )
    {
        $this->_config = request('_config');

        parent::__construct($app);
    }
    
    public function model() {
        return 'Webkul\Pos\Contracts\PosCustomerCredit';
    }

}