<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WorkerSallary;
use Illuminate\Support\Facades\DB;
class WorkerAccountController extends Controller
{
    public function workers($project_id,$date)
   {
    $time = strtotime($date);
    $year = date('Y',$time);
    $month = date('m',$time);


    $account = WorkerSallary::join('workers', 'worker_salaries.worker_id', '=', 'workers.id')
    ->select(DB::raw(
              'sum(add_sallary)as add_sallary,
                      sum(deduct_sallary)as deduct_sallary,
                      sum(total_sallary)as total_sallary,
                      sum(hours) as hours,
                      sum(payment) as payment,
                      name,workers.sallary,
                      sum(total_sallary) -  sum(payment) as motabky'))
    ->where('worker_salaries.project_id', '=', $project_id)
    ->whereYear('worker_salaries.date_at', '=', $year)
    ->whereMonth('worker_salaries.date_at', '=', $month)
    ->where(function ($query) {
        $query->where('worker_salaries.Presence', '=', 1)
              ->orWhere('payment', '!=', 0);})
    ->groupBy('name','workers.sallary')->orderBy('worker_salaries.date_at','ASC')->get();



    $total = WorkerSallary::select(DB::raw('sum(hours) as hours,
    sum(sallary)as sallary,
    sum(add_sallary) as add_sallary,
    sum(deduct_sallary) as deduct_sallary,
    sum(total_sallary) as total_sallary,
    sum(Presence) as Presence,
    sum(payment) as payment,
    sum(total_sallary) -  sum(payment) as motabky' ))
    ->whereYear('worker_salaries.date_at', '=', $year)
    ->whereMonth('worker_salaries.date_at', '=', $month)
    ->where('worker_salaries.project_id', $project_id)
    ->where(function ($query) {
    $query->where('worker_salaries.Presence', '=', 1)
    ->orWhere('payment', '!=', 0);})
    ->get();




    return response()->json([
           'success'   => true,
            'msg'   => '',
            'data' => $account,
            'total'=>$total
        ]);
   }

   public function worker($project_id,$date,$worker_id)
   {
    $time = strtotime($date);
    $year = date('Y',$time);
    $month = date('m',$time);

    $account = WorkerSallary::where('project_id', '=', $project_id)
    ->where('worker_id',$worker_id)
    ->where(function ($query) {
        $query->where('worker_salaries.Presence', '=', 1)
              ->orWhere('payment', '!=', 0);})
    ->whereYear('date_at', '=', $year)
    ->whereMonth('date_at', '=', $month)->orderBy('date_at', 'ASC')->get();



$total = WorkerSallary::select(DB::raw('sum(hours) as hours,
    sum(sallary)as sallary,
    sum(add_sallary) as add_sallary,
    sum(deduct_sallary) as deduct_sallary,
    sum(total_sallary) as total_sallary,
    sum(Presence) as Presence,
    sum(payment) as payment,
    sum(total_sallary) -  sum(payment) as motabky' ))
->where('worker_salaries.worker_id',$worker_id)
->where('worker_salaries.project_id',$project_id)
->whereYear('worker_salaries.date_at', '=', $year)
->whereMonth('worker_salaries.date_at', '=', $month)
->where(function ($query) {
$query->where('worker_salaries.Presence', '=', 1)
->orWhere('payment', '!=', 0);})
->get();


    return response()->json([
            'success'   => true,
            'msg'   => '',
            'data'=> $account,
            'total'=>$total]);
   }
}
