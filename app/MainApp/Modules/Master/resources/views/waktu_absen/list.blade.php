@extends('layouts.layout-horizontal-sidenav')

@section('styles')
@endsection

@section('scripts')
@parent
@endsection

@section('content')

<h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-1">
    <div><span class="text-muted font-weight-light">Setup /</span> Waktu Absen</div>
</h4>
@include("alert")

<div class="card">                
  <div class="card-body">
    <div class="row">
      <div class="col">       

        <form action="{{ route('master.waktu_absen.update') }}" method="POST">
          <div class="form-row">
            
            <div class="form-group col-md-3">
              <label class="form-label">Jam Masuk Mulai Scan</label>
              <input type="number" class="form-control" placeholder="Jam Masuk Mulai Scan" name="jam_masuk_mulai_scan" value="{{ isset($jam_masuk_mulai_scan) ? $jam_masuk_mulai_scan : '' }}">
            </div>

            <div class="form-group col-md-3">
              <label class="form-label">Jam Masuk Akhir Scan</label>
              <input type="number" class="form-control" placeholder="Jam Masuk Akhir Scan" name="jam_masuk_akhir_scan" value="{{ isset($jam_masuk_akhir_scan) ? $jam_masuk_akhir_scan : '' }}">
            </div>

            <div class="form-group col-md-3">
              <label class="form-label">Jam Keluar Mulai Scan</label>
              <input type="number" class="form-control" placeholder="Jam Keluar Mulai Scan" name="jam_keluar_mulai_scan" value="{{ isset($jam_keluar_mulai_scan) ? $jam_keluar_mulai_scan : '' }}">
            </div>
            
            <div class="form-group col-md-3">
              <label class="form-label">Jam Keluar Akhir Scan</label>
              <input type="number" class="form-control" placeholder="Jam Keluar Akhir Scan" name="jam_keluar_akhir_scan" value="{{ isset($jam_keluar_akhir_scan) ? $jam_keluar_akhir_scan : '' }}">
            </div>

          </div>
          <div class="form-row">
            <div class="form-group col-md-10"></div>
            <div class="form-group col-md-2">
              <button type="submit" class="btn btn-primary form-control">Simpan</button>
            </div>
          </div>

          <input name="_method" type="hidden" value="PUT">
          {{ csrf_field() }}
        </form>

      </div>
    </div>
  </div>
</div>

@endsection