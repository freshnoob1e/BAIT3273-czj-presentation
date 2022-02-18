<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class JobController extends Controller
{
    // CRUD
    public function index(){
        $jobs = Http::get(env('LOGIC_APPLICATION_URL').'jobs')['jobs'];
        return Inertia::render('Job/Index', [
            'jobs' => $jobs
        ]);
    }

    public function create(){
        $departments = Http::get(env('LOGIC_APPLICATION_URL').'departments')['departments'];

        return Inertia::render('Job/Create', [
            'departments' => $departments
        ]);
    }

    public function store(Request $req){
        $data = $req->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'title' => ['required', 'string', 'unique:jobs,title'],
            'description' => ['required', 'string'],
            'minimum_basic' => ['required', 'numeric', 'max:'.$req->maximum_basic],
            'maximum_basic' => ['required', 'numeric', 'min:'.$req->minimum_basic]
        ]);

        Http::post(env('LOGIC_APPLICATION_URL').'job', $data);

        return Redirect::route('jobs');
    }

    public function edit($job){
        $job = Http::get(env('LOGIC_APPLICATION_URL').'job/'.$job)['job'];
        $departments = Http::get(env('LOGIC_APPLICATION_URL').'departments')['departments'];

        return Inertia::render('Job/Edit', [
            'job' => $job,
            'departments' => $departments,
        ]);
    }

    public function update($job, Request $req){
        $job = Http::get(env('LOGIC_APPLICATION_URL').'job/'.$job)['job'];

        $data = $req->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'minimum_basic' => ['required', 'numeric', 'max:'.$req['maximum_basic']],
            'maximum_basic' => ['required', 'numeric', 'min:'.$req['minimum_basic']]
        ]);

        Http::patch(env('LOGIC_APPLICATION_URL').'job/'.$job['id'], $data);

        return Redirect::route('jobs');
    }

    // API CALLLS
    public function searchJobs($searchTerm){
        $jobs = Http::get(env('LOGIC_APPLICATION_URL').'jobs/'.$searchTerm)['jobs'];

        return response()->json($jobs);
    }
}
