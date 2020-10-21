<?php

namespace Webkul\Pos\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Pos\Repositories\PosOrderRepository as PosOrder;

/**
 * Orders controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class OrdersController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * PosOrderRepository object
     *
     * @var array
     */
    protected $posOrder;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PosOrder $posOrder
    )
    {
        $this->_config = request('_config');

        $this->posOrder = $posOrder;
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
}