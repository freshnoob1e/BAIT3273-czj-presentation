<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    // CRUD
    public function index(){
        $departments = Http::get(env('LOGIC_APPLICATION_URL').'departments')['departments'];
        return Inertia::render('Department/Index', [
            'departments' => Department::latest()->get()
        ]);
    }

    public function create(){
        return Inertia::render('Department/Create');
    }

    public function store(Request $req){
        $data = $req->validate([
            'title' => ['required', 'string', 'unique:departments,title'],
        ]);

        Http::post(env('LOGIC_APPLICATION_URL').'department', $data);

        return Redirect::route('departments');
    }

    public function edit($department){
        $department = Http::get(env('LOGIC_APPLICATION_URL').'department/'.$department)['department'];

        return Inertia::render('Department/Edit', [
            'department' => $department,
        ]);
    }

    public function update(Request $req, $department){
        $title = Http::get(env('LOGIC_APPLICATION_URL').'department/'.$department)['department']['title'];

        $data = $req->validate([
            'title' => ['required', 'string', 'unique:departments,title,'.$title],
        ]);

        Http::patch(env('LOGIC_APPLICATION_URL').'department/'.$department, $data);

        return Redirect::route('departments');
    }

    // API
    public function getJobInDepartment($department){
        $jobs = Http::get(env('LOGIC_APPLICATION_URL').'department/'.$department.'/jobs')['jobs'];
        return response()->json($jobs);
    }

    public function searchDepartments($searchTerm){
        $departments = Http::get(env('LOGIC_APPLICATION_URL').'departments/'.$searchTerm)['departments'];

        return response()->json($departments);
    }
}
