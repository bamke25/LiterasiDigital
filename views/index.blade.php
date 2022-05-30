@extends('layouts.main')

@section('content')
    

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Pengujian</h1>
    <p class="mb-4">Menampilkan halaman pengujian dimana Anda dapat memilih gejala-gejala serta kondisi yang Anda alami untuk kemudian diproses oleh sistem guna menentukan jenis kasus covid-19 yang sedang Anda derita.</p>

    <!-- DataTales Example -->
    <div class="box shadow mb-2 border-bottom pb-2">
        <div class="card-body">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-exclamation-triangle">Perhatian!</i></h4>
                Silahkan memilih gejala-gejala sesuai dengan kondisi yang Anda alami. Cukup gejala yang Anda alami saja, selebihnya tidak perlu diisi. Kemudian tekan tombol proses (<i class='fa fa-search-plus'></i>) untuk melihat hasil.
            </div>

            <!-- Topbar Search -->
            <form action="/pengujian" class="d-none d-sm-inline-block form-inline mr-auto my-2 my-md-0 mw-100 navbar-search" style="width: 350px">  
                <div class="input-group mb-3">
                    <input name="search" value="{{ request('search') }}" type="text" class="form-control bg-light border-2 small" placeholder="Search for..."
                        aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" id="" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="20">No</th>
                            {{-- <th width="20">Kode</th> --}}
                            <th>Gejala</th>
                            <th width="80">Kondisi</th>
                        </tr>
                    </thead>
                    <form action="/pengujian/hasil" method="POST">
                    @csrf
                    <tbody>
                        @foreach ($symptoms as $symptom )
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            {{-- <td></td> --}}
                            <td>{!! $symptom->name !!}</td>
                            <td>
                                <select style="color: gray" name="nilai_cf[]" id="">
                                    <option value="">Pilih Kondisi Anda</option>
                                    @foreach ($kondisi as $item)
                                    <option value="{{ $symptom->symptom_code."_".$item->value }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                       <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="left" title="Klik di sini untuk melihat hasil diagnosis" style="display:block;width:63px;height:63px;line-height:30px;text-align:center;color:white;font-size:22px;font-family:Arial, Helvetica, sans-serif;border-radius:50%;transition:ease all 0.3s;position:fixed;right:50px;bottom:50px;"><i class='fa fa-search-plus'></i></button>
                    </tbody>
                    </form>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function(){
	  $('[data-toggle="tooltip"]').tooltip();
	});

    let cf_user = [];
    function load_kondisi()
    {
        $.get('/pengujian/list_kondisi', function(data){
            let option = ''
            option += `<option>Plilih Kondisi Anda</option>`
            $.each(data, function(i, kondisi){
                cf_user.push(kondisi)
                option += `<option value="${kondisi.value}">${kondisi.name}</option>`
            })
            $("[name=nilai_cfuser]").html(option).choosen({allow_singgle_deselect:true})
        })
    }
    load_kondisi()
</script>

@endsection

