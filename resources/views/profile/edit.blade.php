@extends('layouts.template')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header text-center">
                <h3 class="card-title">Edit Foto Profil</h3>
            </div>
            <div class="card-body text-center">
                <!-- Foto saat ini -->
                    <img src="{{ Auth::user()->profile_photo_url ? asset('storage/' . Auth::user()->profile_photo_url) : asset('storage/profile_photos/default.png') }}"
                        alt="Foto Profil"
                        class="img-fluid img-circle elevation-2 mb-3"
                        style="width: 150px; height: 150px; object-fit: cover;">

                <h4>{{ $user->nama }}</h4>

                <!-- Form Update Foto -->
                <form id="profile-form" action="{{ url('/profile/update/' . $user->user_id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                    @csrf
                    @method('PUT')
                    <div class="form-group text-left">
                        <label class="no-bold">Foto Baru</label>
                        <div class="input-group">
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="d-none">
                            <input type="text" class="form-control" placeholder="Belum ada file dipilih" id="filename" readonly>
                            <div class="input-group-append">
                                <label class="btn no-bold btn-custom-grey mb-0" for="profile_photo">Browse</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <!-- Hapus Foto -->
                        <button type="button" id="delete-photo-btn" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Hapus Foto
                        </button>

                        <!-- Simpan -->
                        <button type="submit" id="save-btn" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Menangani pemilihan file untuk foto profil baru
    document.getElementById('profile_photo').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Belum ada file dipilih';
        document.getElementById('filename').value = fileName;
    });

    // Menangani klik tombol simpan perubahan foto
    $('#save-btn').on('click', function(event) {
        event.preventDefault();  
        var form = $('#profile-form')[0];  
        var formData = new FormData(form); 

        $.ajax({
            url: form.action,
            method: form.method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil', 
                    text: response.message 
                }).then(() => {
                    location.reload();  
                });
            },
            error: function(response) {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Terjadi Kesalahan', 
                    text: 'Terjadi kesalahan saat mengubah foto profil.' 
                });
            }
        });
    });

    // Menangani klik tombol hapus foto dengan konfirmasi SweetAlert
    $('#delete-photo-btn').on('click', function(event) {
        event.preventDefault();  

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Tindakan ini akan menghapus foto profil Anda, dan tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika tombol "Hapus" diklik
                $.ajax({
                    url: '{{ url('profile/photo/' . $user->user_id) }}', 
                    method: 'PUT',  
                    data: {
                        _token: '{{ csrf_token() }}',
                        profile_photo: null,  
                    },
                    success: function(response) {
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'Berhasil', 
                            text: response.message 
                        }).then(() => {
                            location.reload(); 
                        });
                    },
                    error: function(response) {
                        Swal.fire({ 
                            icon: 'error', 
                            title: 'Terjadi Kesalahan', 
                            text: 'Terjadi kesalahan saat menghapus foto.' 
                        });
                    }
                });
            }
        });
    });
</script>
@endpush

@push('css')
<style>
    .btn-custom-grey {
        background-color: #e0e0e0;
        color: #333;
        border: 1px solid #ccc;
    }

    .btn-custom-grey:hover {
        background-color: #d5d5d5;
    }

    .no-bold {
        font-weight: normal !important;
    }
</style>
@endpush

@endsection
