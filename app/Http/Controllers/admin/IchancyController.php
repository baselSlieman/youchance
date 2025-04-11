<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Ichancy;
use Illuminate\Http\Request;

class IchancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ichancies = Ichancy::query()->orderBy('created_at', 'desc')->with('chat')->paginate(5);
        return view("admin.ichancy.index",compact('ichancies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Ichancy $ichancy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ichancy $ichancy)
    {
        return view("admin.ichancy.edit",compact('ichancy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ichancy $ichancy)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ichancy $ichancy)
    {
        $ichancy->delete();
        return redirect()->route('ichancies.index')->with('success','Ichancy account in site deleted successfully');
    }
}
