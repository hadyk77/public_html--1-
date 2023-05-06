<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WorkerSegal;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkerSegalController extends Controller
{
    public function get(int $project_id, $date, $segal_id)
    {
        $worker = Worker::where('project_id', '=', $project_id)->where('user_id', auth()->user()->id)->get();

        foreach ($worker as $workers) {
            $count= WorkerSegal::where('project_id', '=', $project_id)
                                 ->where('worker_id', $workers->id)
                                 ->whereDate('date_at', $date)
                                 ->where('segal_id', $segal_id)
                                 ->count();
            if($count == 0) {
            WorkerSegal::create([
                        'worker_id'=> $workers->id ,
                        'project_id' =>$workers->project_id,
                        'segal_id'=>$segal_id,
                        'sallary' => $workers->	sallary,
                        'date_at'=> $date
                    ]);
                }
            }

            $worker_get = WorkerSegal::join('workers', 'workers.id', '=', 'worker_segal.worker_id')
            ->select('worker_segal.id','workers.name','worker_segal.hours', 'workers.mobile',
            'worker_segal.sallary','total_sallary','Presence','date_at')
            ->where('worker_segal.project_id', '=', $project_id)
            ->where('workers.user_id', '=', auth()->user()->id)
            ->where( 'worker_segal.date_at','=',$date)->get();

            return response()->json([
                        'success'   => true,
                        'msg'       => '',
                        'data'      => $worker_get
                    ]);
        }

        public function sallarydaily(Request $request, $id)
        {
            if($request-> statement ==null)
            $statement = '-';
            else 
            $statement= $request->statement ;

            $sallarys=WorkerSegal::where('id', $id)->sum('sallary');

            $totals = ($sallarys/8)*$request->hours;

            $sallary = WorkerSegal::where('id', $id);

            $sallary->update([
                'Presence' => $request->Presence,
                'hours' => $request->hours,
                'statement'=>$statement,
                'total_sallary'=> $totals,
            ]);

            return response()->json([
                'success'   => true,
                'msg'       => '',
                'data'      => 'total_sallary = '. $totals
            ]);
        }
        
        
          public function sallarydailydetails($id){
        $d=WorkerSegal::where('id', $id)->get();
        
       
       $ex=(object)
       ['success'=>true,
       'msg'=>'',
       'data'=>$d[0]
       ];
        return response()->json($ex);
    }
        

    public function reprotall($project_id,$segal_id,$date)
    {
        $time = strtotime($date);
        $year = date('Y',$time);
        $month = date('m',$time);

        $report= WorkerSegal::join('segals','segals.id','worker_segal.segal_id')
                   ->join('workers', 'workers.id', '=', 'worker_segal.worker_id')
                   ->select(DB::raw('sum(worker_segal.hours) as hours,
                                     sum(total_sallary) as total_sallary,
                                     segals.name as segal_name,
                                     workers.name as worker_name' ))
                  ->where('worker_segal.project_id', '=', $project_id)
                  ->where('worker_segal.Presence', '=', 1)
                  ->where('segals.id',$segal_id)
                 
                  ->whereYear('worker_segal.date_at', '=', $year)
                  ->whereMonth('worker_segal.date_at', '=', $month)
                  ->groupBy('segals.name','workers.name')->get();

        $total= WorkerSegal::join('segals','segals.id','worker_segal.segal_id')
                  ->select(DB::raw('sum(worker_segal.hours) as hours,
                                    sum(total_sallary) as total_sallary' ))
                 ->where('worker_segal.project_id', '=', $project_id)
                 ->where('worker_segal.Presence', '=', 1)
                 ->where('segals.id',$segal_id)
               
                 ->whereYear('worker_segal.date_at', '=', $year)
                 ->whereMonth('worker_segal.date_at', '=', $month)->get();

        return response()->json([
                 'success'=>true,
                 'msg'=>'',
                 'data'=>$report,
                 'total'=>$total
                 ]);
    }


        
    }





