<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
class ProjectController extends Controller
{
    public function test(){
      $pdf=  Pdf::loadView('printtest');

      return $pdf->download('printtest.pdf');
    }
    public function index()
    {
       $project = Project::join('users' , 'projects.user_id','users.id')
                  ->select('projects.id','users.name as user','projects.name as project','projects.address',
                  'projects.sallary','projects.date_at','finsh','archive')
                  ->where('users.id' ,auth()->user()->id )
                  -> orderBy('date_at','desc')->get();
        return response()->json([
            'success'   => true,
            'msg'   => '',
            'data' => $project
        ]);
    }


 public function get()
    {
        $count = Project::where('worker_id','!=',0)
                          ->where('user_id' ,auth()->user()->id )->count('worker_id');
        if($count != 0){
            $project = Project::join('users' , 'projects.user_id','users.id')
                ->join('workers' , 'projects.worker_id','workers.id')
                ->select('projects.id','users.name as user','projects.name as project',
                         'projects.address','projects.sallary','projects.date_at','finsh','archive',
                         'workers.name as worker_name')
                ->where('users.id' ,auth()->user()->id)
                -> orderBy('date_at','desc')->get();
            return response()->json([
                'success'   => true,
                'msg'   => '',
                'data' => $project
            ]);
        }else{
    $project = Project::join('users', 'projects.user_id', 'users.id')
          ->select(
              'projects.id',
              'users.name as user',
              'projects.name as project',
              'projects.address',
              'projects.sallary',
              'projects.date_at',
              'finsh',
              'archive',
          )
          ->where('users.id', auth()->user()->id)
          -> orderBy('date_at', 'desc')->get();
        return response()->json([
            'success'   => true,
        'msg'   => '',
            'data' => $project
        ]);
        }
    }





    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'address' => 'required|string',
            'sallary' => 'required|integer',
            'date_at'=>'required'
        ]);

    if($validator->fails()){
        return response()->json($validator->errors()->toJson(),400 );
        }
    if($request->worker_id == null){
        $worker_id = 0;
    }else{
        $worker_id = $request->worker_id;
    }
        $project= Project::create(
            array_merge( $validator->validate(),
                ['user_id'=>auth()->user()->id],
                ['worker_id'=> $worker_id ]
            ));
            return response()->json([
                'success'   => true,
                'message'=>'تم انشاء المشروع بنجاح',
                'project'=>$project,
            ]);


    }//end of store



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        
        if($request->worker_id != 0 or $request->worker_id != null)
    {
        $worker_id = (int)$request->worker_id;
        $worker = Worker::find($worker_id);
        $worker->update(['admin'=> 1]);
    }

        $project = Project::find($id);
        $project->update($request->all());
       // $project->save();
        return response()->json([
            'success'   => true,
            'msg'   => '',
            'data' => $project
    ]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json([
            'success'   => true,
            'msg'   => '',
            'data'=>$project,
        ]);

    }
    public function summary($project_id){


$project = User::join('projects', 'users.id', '=', 'projects.user_id')
        ->join('payments','projects.id', '=','payments.project_id')
        ->join('project_budgets','projects.id', '=','project_budgets.project_id')
        ->join('monthly_expenses','projects.id','=', 'monthly_expenses.project_id')
        ->select(DB::raw('sum(payments.value)as payments,
                    sum(project_budgets.value)as budgets,
                    sum(monthly_expenses.sallary)as expenses,
                    projects.date_at,users.name ,
        sum(project_budgets.value) - sum(monthly_expenses.sallary)-sum(payments.value) as total'))
        ->where('projects.id', '=', $project_id)
        ->where('users.id', '=', auth()->user()->id)
        ->groupBy( 'projects.date_at','users.name')->count('projects.id');
        

        if($project == 0){
            return response()->json([
            'success'   => false,
            'msg'   => 'لم تكتمل البيانات',
             'data'=>null,
        ]);}
        
           


        $project = User::join('projects', 'users.id', '=', 'projects.user_id')
        ->join('payments','projects.id', '=','payments.project_id')
        ->join('project_budgets','projects.id', '=','project_budgets.project_id')
        ->join('monthly_expenses','projects.id','=', 'monthly_expenses.project_id')
  ->select(DB::raw('sum(payments.value)as payments,
                    sum(project_budgets.value)as budgets,
                    sum(monthly_expenses.sallary)as expenses,
                    projects.date_at,users.name ,projects.name as project_name,
sum(project_budgets.value) - sum(monthly_expenses.sallary)-sum(payments.value) as total'))
  ->where('projects.id', '=', $project_id)
  ->where('users.id', '=', auth()->user()->id)
  ->groupBy( 'projects.date_at','users.name','projects.name')->get();
           
           
           

           
              
      if (is_array($project)) {
    // Extract the first element of the array
    $project = reset($project);
}

$pr= (object) [
    'success'   => true,
    'msg'   => '',
    'data' => $project[0]

];
  return response()->json($pr);}
}
