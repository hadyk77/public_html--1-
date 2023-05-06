<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentsRequest;

use App\Models\Payment;

use App\Models\WorkerSallary;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function getPayments(int $project_id,$worker_id)
    {
       $payment= Payment::where('project_id',$project_id)->where('worker_id',$worker_id)->get();
        return response()->json([
            'success'   => true,
            'message'   => '',
            'data' => $payment
        ]);
    }
    public function storePayments(PaymentsRequest $request,int $project_id)
    {
        $sallary_count = WorkerSallary::where('date_at',$request->date_at)
                                ->where('worker_id', $request->worker_id)
                                ->where('project_id',$project_id)->count();

    if ($sallary_count = 0) {
        $sallary= WorkerSallary::create([
            'worker_id'=> $request->worker_id,
            'project_id',$project_id,
            'payment' => $request-> value,
            'date_at' =>$request->date_at
        ]);
    }else{
        $sallary = WorkerSallary::where('date_at',$request->date_at)
            ->where('worker_id', $request->worker_id)
            ->where('project_id',$project_id);
        $sallary->update(['payment' => $request-> value]);
    }
    $payment= Payment::create([
        'worker_id'=> $request->worker_id,
        'value' => $request-> value,
        'statement'=>$request->statement,
        'project_id' => $project_id,
        'user_id' =>auth()->user()->id,
        'date_at' =>$request->date_at
    ]);
    return response()->json([
            'success'   => true,
            'message'   => 'تم التسجيل بنجاح',
            'data' => $payment
        ]);
    }
    public function updatePayments(Request $request, int $id)
    {

        $payment= Payment::where('id',$id);
        $payment->update($request->all());
        return response()->json([
            'success'   => true,
            'msg'=>'تم التسجيل بنجاح',
            'data'=> $payment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deletePayments (int $id)
    {
        $worker = Payment::findOrFail($id);
        $worker->delete();
        return response()->json([
            'success'   => true,
            'msg'=>'تم الحذف',
            'data'=> $worker
        ]);
    }

}
