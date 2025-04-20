@extends('layouts.template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Profil Pengguna</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Foto Profil -->
                    <div class="col-md-3 text-center">
                        <img src="{{ Auth::user()->profile_photo_url ? asset('storage/' . Auth::user()->profile_photo_url) : asset('storage/profile_photos/default.png') }}"
                            alt="Foto Profil"
                            class="img-fluid img-circle elevation-2 mb-3"
                            style="width: 150px; height: 150px; object-fit: cover;">
                        <a href="{{ url('/profile/edit') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit Foto
                        </a>
                    </div>

                    <!-- Info Profil -->
                    <div class="col-md-9">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 150px;">Username</th>
                                <td>: {{ Auth::user()->username }}</td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td>: {{ Auth::user()->nama }}</td>
                            </tr>
                            <tr>
                                <th>Level</th>
                                <td>: {{ Auth::user()->level->level_nama }}</td>
                            </tr>
                        </table>
                    </div>
                </div> 
            </div>
        </div> 
    </div> 
</div> 
@endsection
