<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    // Show the form
    public function showForm()
    {
        return view('ml_form');
    }

    // Handle the form submission and call ML backend
    public function callML(Request $request)
    {
        $data = [
            'text' => $request->input('text')
        ];

        $response = Http::post('http://127.0.0.1:5000/predict', $data);

        return view('ml_form', [
            'result' => $response->json()['result'] ?? 'Error contacting ML service'
        ]);
    }
}
