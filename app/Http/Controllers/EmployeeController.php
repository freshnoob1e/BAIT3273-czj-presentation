<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Job;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    // CRUD
    public function index(){
        $employees = Http::get(env('LOGIC_APPLICATION_URL').'employees')['employees'];
        return Inertia::render('Employee/Index', [
            'employees' => $employees
        ]);
    }

    public function show($employee){
        $employee = Http::get(env('LOGIC_APPLICATION_URL').'employee/'.$employee)['employee'];
        if($employee['image'] != ''){
            $employee['image'] = Storage::disk('s3')->url($employee['image']);
        }
        return Inertia::render('Employee/Show', [
            'employee' => $employee
        ]);
    }

    public function edit($employee){
        $res = Http::get(env('LOGIC_APPLICATION_URL').'employee/'.$employee.'/edit');
        $employee = $res['employee'];
        $jobs = $res['jobs'];

        return Inertia::render('Employee/Edit', [
            'employee' => $employee,
            'jobs' => $jobs,
        ]);
    }

    public function update(Request $req, $employee){
        $employee = Http::get(env('LOGIC_APPLICATION_URL').'employee/'.$employee)['employee'];

        $minBasic = 0;
        $maxBasic = 0;
        if($req->job_id != null){
            $job = Http::get(env('LOGIC_APPLICATION_URL').'job/'.$req->job_id)['job'];
            $minBasic = $job['minimum_basic'];
            $maxBasic = $job['maximum_basic'];
        }

        if($req->hasFile('image') && $req->file('image')->isValid()){
            $req->validate(['image' => ['nullable', 'image']]);
            $data = $req->validate([
                'employee_id' => ['required', 'string', 'unique:employees,employee_id,'.$employee['id']],
                'job_id' => ['required', 'exists:jobs,id'],
                'work_type' => ['required', Rule::in(['Full-Time', 'Part-Time'])],
                'basic_salary' => ['required', 'numeric', 'min:'.$minBasic, 'max:'.$maxBasic],
                'contact_number' => ['required', 'string'],
                'email' => ['required', 'string'],
                'birth_date' => ['required', 'date'],
                'qualification' => ['required', 'string']
            ]);

            try{
                if($employee['image'] != '' && Storage::disk('s3')->exists($employee['image'])){
                    Storage::disk('s3')->delete($employee['image']);
                }
                $file = $req->file('image');
                $filename = Carbon::now()->format('Y_m_d_G_i_s').'_'.$employee['name'].".".$file->getClientOriginalExtension();
                $imagePath = $file->storeAs('images', $filename, 's3');
            } catch (Exception $e){
                abort(500, 'Error uploading file');
            }
            $data['image'] = $imagePath;
            Http::patch(env('LOGIC_APPLICATION_URL').'employee/'.$employee['id'], $data);
        } else {
            $data = $req->validate([
                'employee_id' => ['required', 'string', 'unique:employees,employee_id,'.$employee['id']],
                'job_id' => ['required', 'exists:jobs,id'],
                'work_type' => ['required', Rule::in(['Full-Time', 'Part-Time'])],
                'basic_salary' => ['required', 'numeric', 'min:'.$minBasic, 'max:'.$maxBasic],
                'contact_number' => ['required', 'string'],
                'email' => ['required', 'string'],
                'birth_date' => ['required', 'date'],
                'qualification' => ['required', 'string']
            ]);
            Http::patch(env('LOGIC_APPLICATION_URL').'employee/'.$employee['id'], $data);
        }

        $employee = Http::get(env('LOGIC_APPLICATION_URL').'employee/'.$employee['id'])['employee'];
        if($employee['image'] != ''){
            $employee['image'] = Storage::disk('s3')->url($employee['image']);
        }
        return Redirect::route('employee.show', ['employee' => $employee['id']]);
        // return Inertia::render('Employee/Show', [
        //     'employee' => $employee
        // ]);
    }

    public function create(){
        $departments = Http::get(env('LOGIC_APPLICATION_URL').'departments')['departments'];
        $initialJobs = $departments[0]['jobs'];

        return Inertia::render('Employee/Create', [
            'departments' => $departments,
            'initialJobs' => $initialJobs
        ]);
    }

    public function store(Request $req){
        $minBasic = 0;
        $maxBasic = 0;
        if($req->job_id != null){
            $job = Http::get(env('LOGIC_APPLICATION_URL').'job/'.$req->job_id)['job'];
            $minBasic = $job['minimum_basic'];
            $maxBasic = $job['maximum_basic'];
        }

        $req->validate([
            'image' => ['required', 'image'],
            'employee_id' => ['required', 'string', 'unique:employees'],
            'name' => ['required', 'string'],
            'job_id' => ['required', 'exists:jobs,id'],
            'date_joined' => ['required', 'date'],
            'work_type' => ['required', Rule::in(['Full-Time', 'Part-Time'])],
            'basic_salary' => ['required', 'numeric', 'min:'.$minBasic, 'max:'.$maxBasic],
            'contact_number' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'birth_date' => ['required', 'date'],
            'qualification' => ['required', 'string'],
            'street_one' => ['required', 'string'],
            'street_two' => ['required', 'string'],
            'postcode' => ['required', 'string'],
            'state' => ['required', 'string'],
            'country' => ['required', 'string'],
        ]);

        $empData = [
            'employee_id' => $req->employee_id,
            'name' => $req->name,
            'job_id' => $req->job_id,
            'date_joined' => $req->date_joined,
            'work_type' => $req->work_type,
            'basic_salary' => $req->basic_salary,
            'contact_number' => $req->contact_number,
            'email' => $req->email,
            'birth_date' => $req->birth_date,
            'qualification' => $req->qualification,
        ];

        try{
            $file = $req->file('image');
            $filename = Carbon::now()->format('Y_m_d_G_i_s').'_'.$empData['name'].".".$file->getClientOriginalExtension();
            $imagePath = $file->storeAs('images', $filename, 's3');
        } catch (Exception $e){
            abort(500, 'Error uploading file');
        }

        $empData['image'] = $imagePath;

        $employee = Http::post(env('LOGIC_APPLICATION_URL').'employee', $empData)['employee'];

        $addData = [
            'employee_id' => $employee['id'],
            'street_one' => $req->street_one,
            'street_two' => $req->street_two,
            'postcode' => $req->postcode,
            'state' => $req->state,
            'country' => $req->country,
        ];
        Http::post(env('LOGIC_APPLICATION_URL').'address', $addData);

        return Redirect::route('employee.show', ['employee' => $employee['id']]);
    }

    // API CALLLS
    public function searchEmployees(Request $req){
        $employees = Http::get(env('LOGIC_APPLICATION_URL').'employees/'.$req->searchTerm)['employees'];

        return response()->json($employees);
    }
}
