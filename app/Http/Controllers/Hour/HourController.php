<?php

namespace App\Http\Controllers\Hour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $hours = [
            [
                "id" => "08",
                "name" => "8:00 AM" 
            ],
            [
                "id" => "09",
                "name" => "9:00 AM" 
            ],
            [
                "id" => "10",
                "name" => "10:00 AM" 
            ],
            [
                "id" => "11",
                "name" => "11:00 AM" 
            ],
            [
                "id" => "12",
                "name" => "12:00 PM" 
            ],
            [
                "id" => "13",
                "name" => "1:00 PM" 
            ],
            [
                "id" => "14",
                "name" => "2:00 PM" 
            ],
            [
                "id" => "15",
                "name" => "3:00 PM" 
            ],
            [
                "id" => "16",
                "name" => "4:00 PM" 
            ],
            [
                "id" => "17",
                "name" => "5:00 PM" 
            ],
           
        ];

        return response()->json([
            "data" => $hours      
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
