<?php

namespace App\Http\Controllers;

use App\Models\Bobot_value;
use App\Models\Conditions;
use App\Models\Diseases;
use App\Models\Knowledges;
use App\Models\Results;
use App\Models\Symptom;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\VarDumper;

class PengujianController extends Controller
{
    public function index()
    {
        $pengujian = Symptom::orderBy('symptom_code', 'asc');
        if (request('search')) {
            $pengujian->where('name', 'like', '%' . request('search') . '%');
        }
        return view('admin.pengujian.index', [
            'title' => "Pengujian",
            'knowledges' => Knowledges::all(),
            'symptoms' => $pengujian->get(),
            'kondisi' => new Collection(Conditions::orderBy('id', 'asc')->get())
        ]);
    }

    public function list_kondisi()
    {
        $data = [];
        $bobot = Conditions::all();
        foreach ($bobot as $kondisi) {
            $data[] = [
                'id' => $kondisi->id,
                'name' => $kondisi->name,
                'value' => $kondisi->value
            ];
        }
        return response($data);
    }


    public function hasil(Request $request)
    {
        $argejala = [];

        /* Yang ini menggunakan full php
        for ($i = 0; $i < count($request->nilai_cf); $i++) {
            $arkondisi = explode("_", $request->nilai_cf[$i]);
            if (strlen($request->nilai_cf[$i]) > 1) {
                $argejala += array($arkondisi[0] => $arkondisi[1]);
            }
        }
        */
        foreach ($request->nilai_cf as $nilaicf) {
            $arkondisi = explode("_", $nilaicf);
            if (strlen($nilaicf > 1)) {
                $argejala += array($arkondisi[0] => $arkondisi[1]);
            }
        }
        if ($argejala == null) {
            return "Gagal! Kondisi tidak boleh kosong";
        }

        $rkondisi = Conditions::orderBy('id', 'asc')->get();
        foreach ($rkondisi as $kondisi) {
            $arkondisitext[$kondisi->id] = $kondisi->name;
        }

        $sqlpkt = Diseases::orderBy('id', 'asc')->get();
        foreach ($sqlpkt as $penyakit) {
            $arpkt[$penyakit->level] = $penyakit->name;
            $ardpkt[$penyakit->level] = $penyakit->detail;
            $arspkt[$penyakit->level] = $penyakit->solutions;
            $argpkt[$penyakit->level] = $penyakit->image;
        }

        // -------- perhitungan certainty factor (CF) ---------
        // --------------------- START ------------------------
        $sqlpenyakit = Diseases::orderBy('id', 'asc')->get();
        $arpenyakit = array();
        foreach ($sqlpenyakit as $rpenyakit) {
            $cf_total_temp = 0;
            $cf = 0;
            $sqlgejala = Knowledges::where('level_id', $rpenyakit->level)->get();
            $cflama = 0;

            foreach ($sqlgejala as $rgejala) {
                $arkondisi = explode("_", $request->nilai_cf[0]);
                $gejala = $arkondisi[0];
                for ($i = 0; $i < count($request->nilai_cf); $i++) {
                    $arkondisi = explode("_", $request->nilai_cf[$i]);
                    $gejala = $arkondisi[0];
                    if ($rgejala->symptom_code == $gejala) {
                        $cf = $rgejala->cf_pakar * $arkondisi[1];
                    }


                    $bobot_nilai = Conditions::all();
                    $arkondisi = explode("_", $request->nilai_cf[$i]);
                    $gejala = $arkondisi[0];

                    if ($rgejala->symptom_code == $gejala) {

                        // if($arkondisi[1] < $bobot_nilai[2]->value ){

                        // }
                        $cf = $rgejala->cf_pakar * $arkondisi[1];

                        if (($cf >= 0) && ($cf * $cflama >= 0)) {
                            $cflama = $cflama + ($cf * (1 - $cflama));
                        }
                        if ($cf * $cflama < 0) {
                            $cflama = ($cflama + $cf) / (1 - 'Math' . Min('Math' . abs($cflama), 'Math' . abs($cf)));
                        }
                        if (($cf < 0) && ($cf * $cflama >= 0)) {
                            $cflama = $cflama + ($cf * (1 + $cflama));
                        }
                    }
                }
            }
            if ($cflama > 0) {
                $arpenyakit += array($rpenyakit->level => number_format($cflama, 4));
            }
            // if ($cflama == 0) {
            //     $arpenyakit += array($rpenyakit->level => number_format($cflama, 4));
            // }
        }

        if($arpenyakit == null){
            $gj = new Symptom();
            $kds = new Conditions();
            return view('admin.pengujian.negatif',[
                'title' => "Hasil Diagnosa",
                'argejala' => $argejala,
                'symptom' => $gj,
                'kondisi' => $kds
            ]);
        }

        arsort($arpenyakit);
        $inpgejala = serialize($argejala);
        $inppenyakit = serialize($arpenyakit);

        // return $arpenyakit;

        $np1 = 0;
        foreach ($arpenyakit as $key1 => $value1) {
            $np1++;
            $idpkt1[$np1] = $key1;
            $vlpkt1[$np1] = $value1;
        }


        $i = 0;
        while ($i < count($arpenyakit)) {

            $key = array_keys($arpenyakit);
            $value = array_values($arpenyakit);


            if ($value[0] == $value[$i]) {
                //Mencari nilai max pada value yang memiliki nilai sama && terdapat value yang lebih kecil namun key nya lebih besar --start--
                $nilai_ke1 = $value[0];
                $kelompok = array_filter($arpenyakit, function ($arpenyakit) use ($nilai_ke1) {
                    return $arpenyakit == $nilai_ke1;
                });
                $key_kel = array_keys($kelompok);
                //--end--

                $hasil2 = max($key_kel);
                $hasil1 = $value[$i];
            }
            $i++;
        }
        // return $hasil1;

        if($hasil2 == 1){
            if($hasil1 < 0.36){
                $gj = new Symptom();
                $kds = new Conditions();
                return view('admin.pengujian.negatif',[
                    'title' => "Hasil Diagnosa",
                    'argejala' => $argejala,
                    'symptom' => $gj,
                    'kondisi' => $kds
                ]);
            }
        }
        if($hasil2 == 3){
            if($hasil1 < 0.36){
                $gj = new Symptom();
                $kds = new Conditions();
                return view('admin.pengujian.negatif',[
                    'title' => "Hasil Diagnosa",
                    'argejala' => $argejala,
                    'symptom' => $gj,
                    'kondisi' => $kds
                ]);
            }
        }
        if($hasil2 == 2 ){
            if($hasil1 < 0.20 ){
                $gj = new Symptom();
                $kds = new Conditions();
                return view('admin.pengujian.negatif',[
                    'title' => "Hasil Diagnosa",
                    'argejala' => $argejala,
                    'symptom' => $gj,
                    'kondisi' => $kds
                ]);
            }
        }
        if($hasil2 == 4){
            if($hasil1 < 0.60){
                $gj = new Symptom();
                $kds = new Conditions();
                return view('admin.pengujian.negatif',[
                    'title' => "Hasil Diagnosa",
                    'argejala' => $argejala,
                    'symptom' => $gj,
                    'kondisi' => $kds
                ]);
            }
        }
        if($hasil2 == 5){
            if($hasil1 < 0.60){
                $gj = new Symptom();
                $kds = new Conditions();
                return view('admin.pengujian.negatif',[
                    'title' => "Hasil Diagnosa",
                    'argejala' => $argejala,
                    'symptom' => $gj,
                    'kondisi' => $kds
                ]);
            }
        }
        if($hasil2 == 6){
            if($hasil1 < 0.60){
                $gj = new Symptom();
                $kds = new Conditions();
                return view('admin.pengujian.negatif',[
                    'title' => "Hasil Diagnosa",
                    'argejala' => $argejala,
                    'symptom' => $gj,
                    'kondisi' => $kds
                ]);
            }
        }
        if($hasil2 == 7){
            if($hasil1 < 0.60){
                $gj = new Symptom();
                $kds = new Conditions();
                return view('admin.pengujian.negatif',[
                    'title' => "Hasil Diagnosa",
                    'argejala' => $argejala,
                    'symptom' => $gj,
                    'kondisi' => $kds
                ]);
            }
        }
        if(!auth()->user()){
            echo "Untuk melihat hasil diagnosa, Anda diharuskan untuk login terlebih dahulu"; echo "<br>";
            return "<a href='/login'>klik tautan ini!</a>";
        }

        Results::create([
            'date' => date('Y-m-d H:i:s'),
            'disease' => $inppenyakit,
            'symptom' => $inpgejala,
            'result_id' => $hasil2,
            'result_value' => $hasil1
        ]);


        $gejala = new Symptom();
        $kds = new Conditions();
        return view('admin.pengujian.result', [
            'title' => "Hasil Diagnosa",
            'argejala' => $argejala,
            'symptom' => $gejala,
            'kondisi' => $kds,
            'arpenyakit' => $arpenyakit,
            'arpkt' => $arpkt,
            'argpkt' => $argpkt,
            'ardpkt' => $ardpkt,
            'arspkt' => $arspkt,
            'idpenyakit' => $hasil2,
            'vlpenyakit' => $hasil1
        ]);
    }

}
