<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AddressController extends Controller
{
    public function edit($address){
        $address = Http::get(env('LOGIC_APPLICATION_URL').'address/'.$address.'/edit')['address'];

        return Inertia::render('Address/Edit', [
            'address' => $address
        ]);
    }

    public function update($address, Request $req){
        $data = $req->validate([
            'street_one' => ['required', 'string'],
            'street_two' => ['required', 'string'],
            'postcode' => ['required', 'string'],
            'state' => ['required', 'string'],
            'country' => ['required', 'string']
        ]);

        $employee = Http::patch(env('LOGIC_APPLICATION_URL').'address/'.$address, $data)['employee'];
        if($employee['image'] != ''){
            $employee['image'] = Storage::disk('s3')->url($employee['image']);
        }
        return Inertia::render('Employee/Show', [
            'employee' => $employee
        ]);
    }
}
