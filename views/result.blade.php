@extends('layouts.main')

@section('content')

<div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Hasil Diagnosis</h1>
        <p class="mb-4">Berikut merupakah hasil analisis atau diagnosis jenis kasus covid-19 yang Anda alami berdasarkan gejala-gejala serta kondisi yang telah Anda pilih sebelumnya.</p>

        <div class="box shadow mb-4">
            <div class="card-header py-3">
                <a href="/pengujian" class="btn btn-info"> <span><- </span> Kembali</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered"  width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="20px">No</th>
                            <th>Gejala yang dialami (keluhan)</th>
                            <th>Pilihan</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($argejala as $key => $value)  
                    <?php $sql = $symptom->where('symptom_code', $key )->get(); 
                          $knds = $kondisi->where('value', $value)->get(); ?>                      
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            @foreach ($sql as $item)
                            <td>{{ $item->name }}</td>
                            @endforeach
                            @foreach ($knds as $k)
                            <td>{{ $k->name }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>


                <hr class="mb-4">
                <table>
                    <div class="row">
                       <div class="col-lg-6">
                       <h4>Hasil Diagnosa</h4>
                            <div class="callout callout-default">Dari hasil perhitungan yang telah dilakukan oleh sistem menggunakan metode Certainty Factor berdasarkan gejala-gejala serta kondisi yang telah Anda pilih sebelumnya, maka diagnosa jenis kasus covid-19 yang diderita adalah <b class="text text-success">{{ $arpkt[$idpenyakit] }}</b></div>
                        </div>
                        <div class="col-lg-6">
                            <img src="{{ asset('storage/images/diseases/'.$argpkt[$idpenyakit]) }}" alt="" class="card-img-top img-bordered-sm" style="float: rigth; margin-left:15px; height:180px; ">
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="info-box bg-info" style="color: white; "><h5 class="box-title">Detail</h5></div>
                        <div class="box-body"><h6>{!! $ardpkt[$idpenyakit] !!}</h6></div>
                    </div>
                    <hr>
                    <div>
                        <div class="info-box bg-danger" style="color: white; "><h5 class="box-title">Solusi</h5></div>
                        <div class="box-body"><h6>{!! $arspkt[$idpenyakit] !!}</h6></div>
                    </div>
                
                </table>

               
                
            </div>
        </div>
        
</div>
    
@endsection