<?php

namespace Webkul\Pos\Http\Controllers\Admin;

use Carbon\Carbon;
use Webkul\Pos\Repositories\PosOutletRepository;
use Webkul\Pos\Repositories\PosBankRepository;
use Webkul\Pos\Repositories\PosOrderRepository;

/**
 * Reports controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ReportsController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * string object
     *
     * @var array
     */
    protected $startDate;

    /**
     * string object
     *
     * @var array
     */
    protected $lastStartDate;

    /**
     * string object
     *
     * @var array
     */
    protected $endDate;

    /**
     * string object
     *
     * @var array
     */
    protected $lastEndDate;

    /**
     * string object
     *
     * @var array
     */
    protected $posOutletRepository;

    /**
     * string object
     *
     * @var array
     */
    protected $posBankRepository;

    /**
     * string object
     *
     * @var array
     */
    protected $posOrderRepository;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PosOutletRepository $posOutletRepository,
        PosBankRepository $posBankRepository,
        PosOrderRepository $posOrderRepository
        )
    {
        $this->_config = request('_config');

        $this->posOutletRepository  = $posOutletRepository;

        $this->posBankRepository  = $posBankRepository;

        $this->posOrderRepository  = $posOrderRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->setStartEndDate();

        $posOutlets = $this->posOutletRepository->all();

        $posBanks = $this->posBankRepository->all();

        return view($this->_config['view'])->with([
            'startDate'     => $this->startDate,
            'endDate'       => $this->endDate,
            'posOutlets'    => $posOutlets,
            'posBanks'      => $posBanks,
            ]);
    }

    /**
     * Sets start and end date
     *
     * @return void
     */
    public function setStartEndDate()
    {
        $this->startDate = request()->get('start')
            ? Carbon::createFromTimeString(request()->get('start'))
            : Carbon::createFromTimeString(Carbon::now()->subDays(30)->format('Y-m-d H:i:s'));

        $this->endDate = request()->get('end')
            ? Carbon::createFromTimeString(request()->get('end'))
            : Carbon::now();

        if ($this->endDate > Carbon::now())
            $this->endDate = Carbon::now();

        $this->lastStartDate = clone $this->startDate;
        $this->lastEndDate = clone $this->startDate;

        $this->lastStartDate->subDays($this->startDate->diffInDays($this->endDate));
    }

    /**
     * Result of search product.
     *
     * @return \Illuminate\Http\Response
     */
    public function orderedProductSearch()
    {
        if (request()->ajax()) {
            $results = [];

            foreach ($this->posOrderRepository->searchProductByAttribute(request()->input('query')) as $row) {
                $results[] = [
                        'id' => $row->main_order_id,
                        'sku' => $row->sku,
                        'name' => $row->name,
                    ];
            }

            return response()->json($results);
        } else {
            return view($this->_config['view']);
        }
    }
}