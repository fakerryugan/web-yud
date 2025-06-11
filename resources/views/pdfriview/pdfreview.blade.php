@extends('adminlte::page')

@section('title', 'Preview PDF')

@section('content_header')
    <h1 class="m-0 text-dark">Preview Dokumen PDF</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
			<div class="card-header d-flex justify-content-between">
				Review Dokumen
			</div>
            <div class="card-body">
                {{-- Ganti src sesuai file PDF yang ingin ditampilkan --}}
                <iframe 
                    src="{{ url('/lihat-pdf/surat.pdf') }}" 
                    width="100%" 
                    height="600px"
                    style="border: 1px solid #ccc; border-radius: 4px;"
                ></iframe>
            </div>
        </div>
    </div>
</div>
@stop
