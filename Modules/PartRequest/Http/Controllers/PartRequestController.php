<?php

namespace Modules\PartRequest\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

use Modules\PartRequest\Repositories\PartRequestRepository;
use Modules\Carline\Repositories\CarlineRepository;
use Modules\Machine\Repositories\MachineRepository;
use Modules\CarlineCategory\Repositories\CarlineCategoryRepository;
use Modules\Part\Repositories\PartRepository;
use App\Helpers\DataHelper;
use App\Helpers\LogHelper;
use DB;
use Validator;

class PartRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->_partRepository = new PartRepository;
        $this->_PartRequestRepository = new PartRequestRepository;
        $this->_CarlineRepository = new CarlineRepository;
        $this->_MachineRepository = new MachineRepository;
        $this->_CarlineCategoryRepository = new CarlineCategoryRepository;
        $this->_logHelper = new LogHelper;
        $this->module = "PartRequest";
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function index()
    {
        // Authorize
        if (Gate::denies(__FUNCTION__, $this->module)) {
            return redirect('unauthorize');
        }

        $partrequests = $this->_PartRequestRepository->getAll();
        $parts = $this->_partRepository->getAll();
        $carlines = $this->_CarlineRepository->getAll();
        $machines = $this->_MachineRepository->getAll();
        $carlinecategories = $this->_CarlineCategoryRepository->getAll();

        return view('partrequest::index', compact('partrequests', 'parts', 'carlines', 'carlinecategories', 'machines'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        // Authorize
        if (Gate::denies(__FUNCTION__, $this->module)) {
            return redirect('unauthorize');
        }

        return view('partrequest::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $part = $this->_partRepository->getById($request->part_id);
        $last = $this->_PartRequestRepository->getLast();

        $file = $request->image_part;
        $fileName = DataHelper::getFileName($file);
        $filePath = DataHelper::getFilePath(false, true);
        $request->file('image_part')->storeAs($filePath, $fileName, 'public');

        if (Gate::denies(__FUNCTION__, $this->module)) {
            return redirect('unauthorize');
        }

        $currentMonth = date('F');
        $currentYear = date('Y');

        if ($last != null) {
            $padded_part_req_id = str_pad($last->part_req_id, 4, '0', STR_PAD_LEFT);
            $part_req_number = "$padded_part_req_id/TO/SPM/$currentMonth/$currentYear";
        } else {
            $part_req_number = "0000/TO/SPM/$currentMonth/$currentYear";
        }


        $partreq = [
            'part_req_pic_filename' => $fileName,
            'part_req_pic_path' => $filePath,
            'part_id' => $request->part_id,
            'part_req_number' => $part_req_number,
            'carline' => $request->carline,
            'car_model' => $request->car_model,
            'alasan' => $request->alasan,
            'order' => $request->order,
            'shift' => $request->shift,
            'machine_no' => $request->machine_no,
            'applicator_no' => $request->applicator_no,
            'wear_and_tear_code' => $request->wear_and_tear_code,
            'wear_and_tear_status' => $request->wear_and_tear_status,
            'serial_no' => $request->serial_no,
            'side_no' => $request->side_no,
            'stroke' => $request->stroke,
            'pic' => $request->pic,
            'remarks' => $request->remarks,
            'part_qty' => $request->part_qty,
            'status' => $request->status,
            'approved_by' => $request->approved_by,
            'part_no' => $part->part_no,
        ];

        // dd($partreq);



        // $part_req_number = "PR/$currentMonth/SPM/1";
        // $part_req_number = "PR/$currentMonth/SPM/". $last->part_req_id;
        // $part_req_number = "$last->part_req_id/TO/SPM/$currentMonth/$currentYear";



        // if ($last) {
        //     $part_req_number = rand(1,9999).$last->part_req_id;
        // }else{
        //     $part_req_number = rand(1,9999).'1';
        // }

        DB::beginTransaction();
        $this->_PartRequestRepository->insert(DataHelper::_normalizeParams($partreq, true));
        // $check = $this->_PartRequestRepository->insert(DataHelper::_normalizeParams($partreq, true));
        $this->_logHelper->store($this->module, $request->part_req_number, 'create');
        DB::commit();
        // dd($check);

        $whatsappResponse = Http::get('https://api.fonnte.com/send', [
            'target' => '6288223492747',
            // Ganti dengan nomor penerima yang sesuai
            'message' => 'Pemberitahuan! Ada Part Request masuk, mohon untuk segera periksa. Terimakasih',
            'token' => 'fU7Xwicj-MrQ!hcHTNgp',
            // Ganti dengan token Anda
        ]);

        return redirect('partrequest')->with('message', 'PartRequest berhasil ditambahkan');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        // Authorize
        if (Gate::denies(__FUNCTION__, $this->module)) {
            return redirect('unauthorize');
        }

        return view('partrequest::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        // Authorize
        if (Gate::denies(__FUNCTION__, $this->module)) {
            return redirect('unauthorize');
        }

        return view('partrequest::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {

        // dd($request->all());
        // Authorize
        if (Gate::denies(__FUNCTION__, $this->module)) {
            return redirect('unauthorize');
        }

        $validator = Validator::make($request->all(), $this->_validationRules($id));

        if ($validator->fails()) {
            return redirect('partrequest')
                ->withErrors($validator)
                ->withInput();
        }
        // dd($request);
        DB::beginTransaction();

        $this->_PartRequestRepository->update(DataHelper::_normalizeParams($request->all(), false, true), $id);
        $this->_logHelper->store($this->module, $request->part_req_number, 'update');

        DB::commit();

        return redirect('partrequest')->with('message', 'PartRequest berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        // Authorize
        if (Gate::denies(__FUNCTION__, $this->module)) {
            return redirect('unauthorize');
        }
        // Check detail to db
        $detail = $this->_PartRequestRepository->getById($id);

        if (!$detail) {
            return redirect('partrequest');
        }

        DB::beginTransaction();

        $this->_PartRequestRepository->delete($id);
        $this->_logHelper->store($this->module, $detail->part_req_number, 'delete');

        DB::commit();

        return redirect('partrequest')->with('message', 'PartRequest berhasil dihapus');
    }

    /**
     * Get data the specified resource in storage.
     * @param int $id
     * @return Response
     */
    public function getdata($id)
    {

        $response = array('status' => 0, 'result' => array());
        $getDetail = $this->_PartRequestRepository->getById($id);

        if ($getDetail) {
            $response['status'] = 1;
            $response['result'] = $getDetail;
        }

        // dd($response);

        return $response;
    }

    private function _validationRules($id = '')
    {
        if ($id == '') {
            return [
                'part_id' => 'required',
            ];
        } else {
            return [
                'part_id' => 'required',
            ];
        }
    }
}
