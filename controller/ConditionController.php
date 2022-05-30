<?php

namespace App\Http\Controllers;

use App\Models\Conditions;
use Illuminate\Http\Request;

class ConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.nilai_bobot.index',[
            'title' => "Bobot Nilai",
            'n_bobot' => Conditions::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'value' => 'required'
        ]);
        $value = $request->value;
        $data['name'] = $request->name;
        $data['value'] = str_replace(',','.', $value);
        Conditions::create($data);
        return redirect('/kondisi');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'value' => 'required'
        ]);
        $value = $request->value;
        $data['name'] = $request->name;
        $data['value'] = str_replace(',','.', $value);
        Conditions::where('id', $id)->update($data);
        return redirect('/kondisi');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Conditions::destroy('id', $id);
        return redirect('/kondisi');
    }
}
