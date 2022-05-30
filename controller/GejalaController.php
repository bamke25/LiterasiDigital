<?php

namespace App\Http\Controllers;

use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpParser\Node\Stmt\Catch_;

class GejalaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.hipotesa_gejala.index',[
            'title' => "Hipotesa Gejala",
            'l_gejala' => Symptom::all()
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
    public function generate_code()
    {
        $prefix = 0;
        $data = Symptom::orderBy('symptom_code', 'desc')->first();
        if(!$data){
            return $prefix += 1;
        }
        $code = str_replace($prefix,'', $data->symptom_code);
        $code = str_pad((int)$code+1,2,'0', STR_PAD_LEFT);
        return $prefix += $code;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);
        $data['symptom_code'] = $this->generate_code();
        Symptom::create($data);
        return redirect('/hipotesa_gejala');
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
            'name' => 'required'
        ]);
        Symptom::where('id', $id)->update($data);
        return redirect('/hipotesa_gejala');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($symptom_code)
    {
        // Symptom::destroy($symptom_code);
        // return redirect('/hipotesa_gejala');
    }

    public function gejala(Request $request)
    {
        $id = $request->id;
        $data = $request->validate([
            'name' => 'required'
        ]);
        Symptom::where('id', $id)->update($data);
        return redirect('/hipotesa_gejala');
    }

    public function import(Request $request, Symptom $symptom)
    {
        $file = $request->file_excel;
        $ext = $file->getClientOriginalExtension();

        if ($ext == 'xls') {
            $render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        }else{
            $render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
        $spreadsheet = $render->load($file);
        $sheet = $spreadsheet->getActiveSheet()->toArray();

        
        foreach ($sheet as $key) {
            //skip judul tabel
            if ($key == 0) {
                continue;
            }
            //skip jika ada data yg sama
            $cek = Symptom::where('name', $symptom->name)->first();
            // $cuy = $this->$cek($key['1']);
            if ($key['1'] == $cek->name) {
                continue;
            }
            Symptom::create([
                'name' => $key['1']
            ]);
        }
        return redirect('/hipotesa_gejala');
    }

    public function importdata(Request $request)
    {
        $this->validate($request,[
            'file_excel' => 'required|file|mimes:xls,xlsx'
        ]);
        $the_file = $request->file('file_excel');

        $spreadsheet = IOFactory::load($the_file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $row_limit = $sheet->getHighestDataRow();
        $column_limit = $sheet->getHighestDataColumn();
        $row_range = range(4, $row_limit);
        $column_range = range('B', $column_limit);
        $start_count = 4;

        $data= [];

        foreach ($row_range as $row ) {
            $name = $sheet->getCell('B'.$row)->getValue();
            $symptom = new Symptom();

            // $nama = $symptom->cekdata($name);
            // if ($name == $nama->name) {
            //     continue;
            // }
            
            $data[] = [
                'name' => $name
            ];
            $start_count++;
        }

        DB::table('symptoms')->insert($data);
        return redirect('/hipotesa_gejala');
    }

    public function hapus($symptom_code)
    {
        Symptom::find($symptom_code)->delete();
        return redirect('/hipotesa_gejala');

    }
}
