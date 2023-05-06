<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpensesRequest;
use App\Models\MonthlyExpenses;
use App\Models\Project;
use Illuminate\Http\Request;
class ExpensesController extends Controller
{
     public function get($project_id)
    {
       $expenses = MonthlyExpenses::where('project_id',$project_id)
                                  ->where('user_id',auth()->user()->id)->orderBy('date_expenses','ASC')->get();
       return response()->json([
        'success'   => true,
        'msg'=>'',
        'data'=> $expenses
    ]);
    }
    public function storeExpenses(ExpensesRequest $request,$project_id)
    {
       $expenses = MonthlyExpenses::create([
            'project_id'=> $project_id,
            'name' =>$request->name,
            'sallary' => $request->	sallary,
            'statement'=>$request->statement,
            'date_expenses'=>$request->date_expenses,
            'user_id'=>auth()->user()->id,
        ]);
        return response()->json([
            'success'   => true,
            'msg'=>'تم التسجيل بنجاح',
            'data'=> $expenses
        ]);
    }
    public function updateExpenses(Request $request, int $id)
    {
        $expenses = MonthlyExpenses::where('id',$id);


        $expenses->update($request->all());
        return response()->json([
            'success'   => true,
            'msg'=>'تم التعديل بنجاح',
            'data'=> $expenses
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteExpenses(int $id)
    {
        $expenses = MonthlyExpenses::where('id',$id);
        $expenses->delete();
        return response()->json([
            'success'   => true,
            'msg'=>'تم الحذف بنجاح',
            'data'=> $expenses
        ]);
    }
    
    
 public function report($date, $project_id)
    {

        $time = strtotime($date);
        $year = date('Y', $time);
        $month = date('m', $time);


        $project = Project::where('id',$project_id)->get();

        $expenses = MonthlyExpenses::where('project_id', '=', $project_id)
        ->where('user_id', auth()->user()->id)
        ->whereYear('date_expenses', '=', $year)
        ->whereMonth('date_expenses', '=', $month)->count();

        if($expenses == 0) {
            $ex= (object) [
            'success'   => false,
            'msg'   => 'لا يوجد سجلات',
            'expenses'=> null,
            'total' =>null
            ];
            return response()->json($ex);
        }else {
            $expenses = MonthlyExpenses::where('project_id', '=', $project_id)
                        ->where('user_id', auth()->user()->id)
                        ->whereYear('date_expenses', '=', $year)
                        ->whereMonth('date_expenses', '=', $month)->get();

            $total = MonthlyExpenses::where('project_id', '=', $project_id)
                        ->where('user_id', auth()->user()->id)
                        ->whereYear('date_expenses', '=', $year)
                        ->whereMonth('date_expenses', '=', $month)->sum('sallary');

            $ex= (object) [
                'success'   => true,
                'msg'   => '',
                'project'=> $project[0],
                'expenses' =>$expenses,
                'total'=>$total
            ];


            return response()->json($ex);
        }









    }
}

