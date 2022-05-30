<?php

namespace App\Http\Controllers;

use App\Models\Conditions;
use App\Models\Diseases;
use App\Models\Knowledges;
use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class KnowledgeController extends Controller
{
    public function index()
    {
        return view('admin.knowledge.index',[
            'title' => "Basis Pengetahuan",
            'knowledges' => Knowledges::all()
        ]);
    }

    public function create()
    {
        return view('admin.knowledge.create',[
            'title' => "Tambah Data Pengetahuan",
            'Diseases' => Diseases::all(),
            'Symptoms' => Symptom::all(),
            'Conditions' => Conditions::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Penyakit' => 'required',
            'Gejala' => 'required',
            'CF_Pakar' => 'required'
        ]);
        $data['level_id'] = $request->Penyakit;
        $data['symptom_code'] = $request->Gejala;
        $data['cf_pakar'] = $request->CF_Pakar;
        // $data['cf_pakar'] = str_replace(',','.', $cf_pakar);

        Knowledges::create($data);
        return redirect('/pengetahuan');
    }

    public function hapus($id)
    {
        Knowledges::find($id)->delete();
        return redirect('/pengetahuan');

    }

    public function edit($id)
    {
        return view('admin.knowledge.edit',[
            'title' => "Edit Pengetahuan",
            'Diseases' => Diseases::all(),
            'Symptoms' => Symptom::all(),
            'Conditions' => Conditions::all(),
            'knowledges' =>Knowledges::find($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'Penyakit' => 'required',
            'Gejala' => 'required',
            'CF_Pakar' => 'required'
        ]);
        $data['level_id'] = $request->Penyakit;
        $data['symptom_code'] = $request->Gejala;
        $data['cf_pakar'] = $request->CF_Pakar;
        // $data['cf_pakar'] = str_replace(',','.', $cf_pakar);

        Knowledges::find($id)->update($data);
        return redirect('/pengetahuan');
    }

}
