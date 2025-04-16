<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Ichancy;
use App\Models\IchTransaction;
use Illuminate\Http\Request;

class IchancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.ichancy.index");
    }

    public function ichancy_transaction(Request $request,$type)
    {
        $id = $request->get('ichancyid');
        $ichancyTrans = IchTransaction::query();
        if(isset($id)){
            $ichancyTrans->where("ichancy_id",$id);
        }
        $ichancyTransRes = $ichancyTrans->where("type",$type)->orderBy('created_at', 'desc')->with('ichancy')->paginate(5);
        return view("admin.ichancy.ichancy_transactions",compact('ichancyTransRes','type'));
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
