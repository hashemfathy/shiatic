<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClientPublicController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $data['gender']= 'male';
        $data['code']= Carbon::now()->timestamp;
        // $data['numbness_in_limbs'] = implode(',', $data['numbness_in_limbs']);
        $client = Client::create($data);

        return redirect('/new-client')->with('success', 'Client created successfully!');
    }
}
