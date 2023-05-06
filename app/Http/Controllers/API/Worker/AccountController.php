<?php

namespace App\Http\Controllers\API\Worker;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Project;
use App\Models\SolfaPayment;
use App\Models\Worker;
use App\Models\WorkerSallary;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class AccountController extends Controller
{
     public function change(Request $request )
     {
          $workers = Worker::where('id', auth()->guard('worker-api')->user()->id);
          $workers->update(['password'=>bcrypt($request->password)]);

          return response()->json([
            'success'   => true,
            'message'   => 'تم التعديل بنجاح',
            'data' => $workers
        ]);
     }

  public function account($date)
   {

    $time = strtotime($date);
    $year = date('Y',$time);
    $month = date('m',$time);



    $project = WorkerSallary::join('projects', 'worker_salaries.project_id', '=', 'projects.id')
    ->select('projects.name')
   ->where('worker_salaries.worker_id', auth()->guard('worker-api')->user()->id)
    ->whereYear('worker_salaries.date_at', '=', $year)
    ->whereMonth('worker_salaries.date_at', '=', $month)
         ->where(function ($query) {
    $query->where('worker_salaries.Presence', '=', 1)
    ->orWhere('payment', '!=', 0);})->distinct()
   ->count();


   if($project == 0 ){
       $pr= (object) [
    'success'   => true,
    'msg'   => '',
    'project'=> null,
    'data' =>null
];


    return response()->json($pr);

   }



    $project = WorkerSallary::join('projects', 'worker_salaries.project_id', '=', 'projects.id')
    ->select('projects.name')
   ->where('worker_salaries.worker_id', auth()->guard('worker-api')->user()->id)
    ->whereYear('worker_salaries.date_at', '=', $year)
    ->whereMonth('worker_salaries.date_at', '=', $month)
         ->where(function ($query) {
    $query->where('worker_salaries.Presence', '=', 1)
    ->orWhere('payment', '!=', 0);})->distinct()
   ->get();

    $sallary = WorkerSallary::join('projects', 'worker_salaries.project_id', '=', 'projects.id')
    ->select('worker_salaries.id','worker_salaries.hours',
                'worker_salaries.sallary','add_sallary','deduct_sallary','total_sallary','Presence' , 'payment','worker_salaries.date_at' ,'worker_salaries.statement as statement' )
   ->where('worker_salaries.worker_id', auth()->guard('worker-api')->user()->id)
    ->whereYear('worker_salaries.date_at', '=', $year)
    ->whereMonth('worker_salaries.date_at', '=', $month)
         ->where(function ($query) {
$query->where('worker_salaries.Presence', '=', 1)
->orWhere('payment', '!=', 0);})
   ->orderBy('date_at','ASC')->get();

   $total = WorkerSallary::join('projects', 'worker_salaries.project_id', '=', 'projects.id')
  -> select(DB::raw('sum(hours) as hours,
                                           sum(worker_salaries.sallary) as sallary ,
                                           sum(add_sallary) as add_sallary,
                                           sum(deduct_sallary) as deduct_sallary,
                                           sum(total_sallary) as total_sallary,
                                           sum(Presence) as Presence,
                                           sum(payment) as payment,
                                           sum(total_sallary) -  sum(payment) as motabky' ))
   ->where('worker_salaries.worker_id', auth()->guard('worker-api')->user()->id)
   ->whereYear('worker_salaries.date_at', '=', $year)
   ->whereMonth('worker_salaries.date_at', '=', $month)
        ->where(function ($query) {
    $query->where('worker_salaries.Presence', '=', 1)
    ->orWhere('payment', '!=', 0);})
    ->get();


$pr= (object) [
    'success'   => true,
    'msg'   => '',
    'project'=> $project[0],
    'data' =>$sallary,
    'total'=>$total
];


    return response()->json($pr);
}


}
