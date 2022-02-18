<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Evaluation;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class PaymentController extends Controller
{
    // CRUD
    public function index(){
        $payments = Http::get(env('LOGIC_APPLICATION_URL').'payments')['payments'];

        return Inertia::render('Payment/Index', [
            'payments' => $payments
        ]);
    }

    public function create(){
        $employees = Http::get(env('LOGIC_APPLICATION_URL').'employees')['employees'];

        return Inertia::render('Payment/Create', [
            'employees' => $employees,
            'bonusAmount' => (float)env('BONUS_AMOUNT', 1.02),
            'bonusScore' => (float)env('BONUS_SCORE', 7.5),
            'bonusHour' => (float)env('BONUS_HOUR', 110)
        ]);
    }

    public function store(Request $req){
        $req->validate(['employee_id' => ['required', 'exists:employees,id']]);

        $employee = Http::get(env('LOGIC_APPLICATION_URL').'employee/'.$req->employee_id)['employee'];
        $min = $employee['basic_salary'];
        $max = $employee['job']['maximum_basic'];

        $paymentData = $req->validate([
            'hours_worked' => ['required', 'numeric', 'min:1'],
            'overtime_worked' => ['required', 'numeric', 'min:0'],
       ]);

       $evaluationData = $req->validate([
           'performance' => ['required', 'numeric', 'min:0', 'max:10'],
           'communication' => ['required', 'numeric', 'min:0', 'max:10'],
           'teamwork' => ['required', 'numeric', 'min:0', 'max:10'],
           'dedication' => ['required', 'numeric', 'min:0', 'max:10'],
           'personality' => ['required', 'numeric', 'min:0', 'max:10'],
       ]);

       $avgScore = ($evaluationData['performance'] + $evaluationData['communication'] + $evaluationData['teamwork']
                    + $evaluationData['dedication'] + $evaluationData['personality']) / 5;
        $payAmnt = $min;
        $bonus = 0;
        if($avgScore > 8.5){
            $temp = $payAmnt;
            $payAmnt = $payAmnt * env('BONUS_AMOUNT', 1.02);
            $bonus = $payAmnt - $temp;
        }

        Http::post(env('LOGIC_APPLICATION_URL').'payment', [
            'employee_id' => $req->employee_id,
            'paymentData' => $paymentData,
            'payAmnt' => $payAmnt,
            'bonus' => $bonus,
            'evaluationData' => $evaluationData
        ]);
        return Inertia::render('Payment/Index');
    }
}
