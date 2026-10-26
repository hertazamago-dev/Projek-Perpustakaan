@extends('member.layout.main')

@section('title', 'Katalog Buku')
@section('content')
    <main role="main" class="container pt-4">
        <div class="row">
            @foreach ($buku as $index => $item)
                <div class="col-lg-3 col-md-4 col-sm-6 d-flex align-items-stretch mb-3">
                    <div class="card" style="width: 100%;">
                        @if ($item->stok > 0)
                            <div class="ribbon-wrapper ribbon-lg">
                                <div class="ribbon bg-success">
                                    Stok: {{ $item->stok }}
                                </div>
                            </div>
                        @else
                            <div class="ribbon-wrapper ribbon-lg">
                                <div class="ribbon bg-danger">
                                    Tidak Tersedia
                                </div>
                            </div>
                        @endif
                        <div class="card-header d-flex justify-content-center align-items-center"
                            style="background-color: lightgrey; height: 175px;">
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->judul_buku }}"
                                style="max-width: 120px; max-height: 150px; object-fit: contain;">
                        </div>
                        <div class="card-body text-center pb-1">
                            <p class="card-text text-bold">{{ $item->judul_buku }}</p>
                            <h6 class="text-center">
                                <span class="badge badge-pill badge-info">{{ $item->kategori->nama_kategori ?? '-' }}
                                </span>
                            </h6>

                            <p class="card-text text-center text-primary mb-0">
                                {{ $item->pengarang }}
                            </p>
                            <p class="card-text text-center text-danger mb-0">
                                {{ $item->penerbit }}
                            </p>
                            <p class="card-text text-center text-success mb-0">
                                {{ $item->tahun_terbit }}
                            </p>
                        </div>

                        <div class="card-footer">
                            <button type="button" class="btn btn-sm btn-primary" onclick="detailBuku('{{ $item->id }}')">
                                Detail
                            </button>

                            @if ($item->stok > 0)
                                <form action="{{ route('member.tambahKeranjang') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        Tambah ke keranjang
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $buku->links() }}
    </main>

    <!-- Modal Detail Buku -->
    <div class="modal fade" id="detailBukuModal" tabindex="-1" role="dialog" aria-labelledby="detailBukuModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detailBukuModalLabel">Detail Buku</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-3 d-flex justify-content-center align-items-center"
                            style="border-right: 1px solid black">
                            <img src="" class="card-img-top" alt="Cover Buku" style="max-width: 120px;" id="gambar">
                        </div>

                        <div class="col-lg-9 pl-2">
                            <dl>
                                <dt>Judul Buku</dt>
                                <dd id="judul_buku"></dd>

                                <dt>Kategori</dt>
                                <dd id="kategori"></dd>

                                <dt>Pengarang</dt>
                                <dd id="pengarang"></dd>

                                <dt>Penerbit</dt>
                                <dd id="penerbit"></dd>

                                <dt>Tahun Terbit</dt>
                                <dd id="tahun_terbit"></dd>

                                <dt>ISBN</dt>
                                <dd id="isbn"></dd>

                                <dt>Stok</dt>
                                <dd id="stok"></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Tutup
                    </button>

                    <form action="{{ route('member.tambahKeranjang') }}" method="POST" id="formTambahKeranjang">
                        @csrf
                        <input type="hidden" name="id" id="idBukuKeranjang">

                        <button type="submit" class="btn btn-success" id="tombolTambahKeranjang">
                            Tambah ke keranjang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Login -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('loginMember') }}" method="POST">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="loginModalLabel">Login Member</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="login_email">Email</label>
                            <input type="email" class="form-control" name="email" id="login_email" required>
                        </div>

                        <div class="form-group">
                            <label for="login_password">Password</label>
                            <input type="password" class="form-control" name="password" id="login_password" required>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <p class="mb-0">
                            Belum punya akun?
                            <span class="text-primary" style="cursor: pointer;" onclick="daftarModal()">Daftar di
                                sini</span>
                        </p>

                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Daftar Member -->
    <div class="modal fade" id="daftarMemberModal" tabindex="-1" role="dialog" aria-labelledby="daftarMemberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="daftarMemberForm">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="daftarMemberModalLabel">Daftar Member</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama lengkap"
                                        required>
                                    <span class="invalid-feedback" role="alert"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <input type="text" name="alamat" class="form-control" id="alamat" placeholder="Alamat"
                                        required>
                                    <span class="invalid-feedback" role="alert"></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Email"
                                        required>
                                    <span class="invalid-feedback" role="alert"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" class="form-control" id="password"
                                        placeholder="Password" required>
                                    <span class="invalid-feedback" role="alert"></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="konfirmasi_password">Konfirmasi Password</label>
                                    <input type="password" name="konfirmasi_password" class="form-control"
                                        id="konfirmasi_password" placeholder="Konfirmasi Password" required>
                                    <span class="invalid-feedback" role="alert"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <p class="mb-0">
                            Sudah punya akun?
                            <span class="text-primary" style="cursor: pointer;" onclick="loginModal()">Login di sini</span>
                        </p>

                        <button type="submit" class="btn btn-primary">Daftar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function detailBuku(id) {
            $.ajax({
                url: '{{ url("/detail-buku") }}/' + id,
                dataType: 'json',
                type: 'GET',

                error: function () {
                    toastr.error('Gagal mengambil detail buku', 'Error', {
                        closeButton: true,
                        progressBar: true
                    });
                },

                success: function (data) {
                    $('#gambar').attr('src', APP_URL + '/storage/' + data.image);
                    $('#judul_buku').text(data.judul_buku);
                    $('#kategori').text(data.kategori ? data.kategori.nama_kategori : '-');
                    $('#pengarang').text(data.pengarang);
                    $('#penerbit').text(data.penerbit);
                    $('#tahun_terbit').text(data.tahun_terbit);
                    $('#isbn').text(data.isbn);
                    $('#stok').text(data.stok);

                    // Isi ID buku untuk form POST tambah keranjang.
                    $('#idBukuKeranjang').val(data.id);

                    if (data.stok > 0) {
                        $('#tombolTambahKeranjang').removeClass('d-none');
                    } else {
                        $('#tombolTambahKeranjang').addClass('d-none');
                    }

                    $('#detailBukuModal').modal('show');
                }
            });
        }

        function daftarModal() {
            $('#loginModal').modal('hide');
            $('#daftarMemberModal').modal('show');
        }

        function loginModal() {
            $('#daftarMemberModal').modal('hide');
            $('#loginModal').modal('show');
        }

        $(document).ready(function () {
            $('#daftarMemberForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('registerMember') }}",
                    method: "POST",
                    data: $(this).serialize(),

                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.success);
                            $('#daftarMemberModal').modal('hide');
                            $('#daftarMemberForm')[0].reset();
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').empty();
                        }
                    },

                    error: function (xhr) {
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;

                            $('#daftarMemberForm .form-control').removeClass('is-invalid');
                            $('#daftarMemberForm .invalid-feedback').empty();

                            $.each(errors, function (key, value) {
                                var field = $('#daftarMemberForm [name="' + key + '"]');
                                field.addClass('is-invalid');
                                field.next('.invalid-feedback').text(value[0]);
                            });
                        } else {
                            toastr.error('Pendaftaran gagal. Silakan coba lagi.', 'Error');
                        }
                    }
                });
            });
        });
    </script>
@endpush