<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterWorkerRequest;
use App\Models\TypeUser;
use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\WorkerSallary;
use Validator;
class WorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $worker = Worker::where('user_id',auth()->user()->id)->get();
        return response()->json([
            'success'   => true,
            'msg'=>'  ',
            'data'=> $worker
        ]);
    }
    public function get($project_id)
    {
        $worker = Worker::where('user_id',auth()->user()->id)
                        ->where('project_id',$project_id)
                        ->get();
        return response()->json([
            'success'   => true,
            'msg'=>'',
            'data'=> $worker
        ]);
    }


    public function store(RegisterWorkerRequest $request)
   {
        TypeUser::create([
            'mobile' => $request->mobile,
            'type'=> 3
        ]);
        $worker=Worker::create([
            'name'=>$request->name,
            'mobile'=>$request->mobile,
            'person_id'=>$request->person_id,
            'sallary' =>$request->sallary,
            'project_id'=>$request->project_id,
            'user_id' => auth()->user()->id ,
            'password'=>bcrypt($request->password)
            ]);
            
    //     if($request->project_id!= null){
    //         DB::table('worker_project')->insert([
    //                 'worker_id' => $worker->id,
    //                 'project_id' => $request->project_id,
    //         ]);
    // }
            
            
        return response()->json([
            'success'   => true,
            'msg'=>'تم التعديل بنجاح',
            'data'=> $worker
        ],201);
    }
/**
     * Show the form for editing the specified resource.
     */


    public function update(Request $request, string $id)
    {
        $worker = Worker::where('id',$id);
        
        if($request->password != null){
        $worker->update (['password'=>bcrypt($request->password)]);}
        
        $worker->update([
            'project_id'=>$request->project_id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'sallary'=>$request->sallary,
            'person_id'=> $request->person_id,
        ]);



        // if($request->project_id!= null){

        //     $count = DB::table('worker_project')->where('id',$worker->id)->count();
        //     if($count == 0) {
        //         DB::table('worker_project')->insert([
        //         'worker_id' => $worker->id,
        //         'project_id' => $request->project_id,
        //         ]);
        //     }
        // }

        
        
        
        return response()->json([
            'success'   => true,
            'msg'=>'تم التسجيل بنجاح',
            'data'=> $worker
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $worker = Worker::findOrFail($id);
        $worker->delete();
        return response()->json([
            'success'   => true,
            'msg'=>'تم التسجيل بنجاح',
            'data'=> $worker
        ]);
    }
}
