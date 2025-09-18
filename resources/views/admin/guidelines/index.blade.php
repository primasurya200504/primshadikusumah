@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Manajemen Panduan</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addGuidelineModal">
                            <i class="fas fa-plus"></i> Tambah Panduan
                        </button>
                    </div>

                    <div class="card-body">
                        @if ($guidelines->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Konten</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($guidelines as $index => $guideline)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $guideline->title }}</td>
                                                <td>{{ Str::limit($guideline->content, 100) }}</td>
                                                <td>{{ $guideline->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info"
                                                        onclick="viewGuideline({{ $guideline->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning"
                                                        onclick="editGuideline({{ $guideline->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST"
                                                        action="{{ route('admin.guidelines.destroy', $guideline) }}"
                                                        style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Yakin hapus panduan ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Panduan</h5>
                                <p class="text-muted">Klik tombol "Tambah Panduan" untuk menambah panduan baru</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Panduan -->
    <div class="modal fade" id="addGuidelineModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Panduan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.guidelines.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Panduan</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Konten Panduan</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Persyaratan (pisahkan dengan enter)</label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="4"
                                placeholder="Contoh:&#10;Fotokopi KTP&#10;Dokumen asli&#10;Surat permohonan"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="example_data" class="form-label">Data Contoh (Format JSON)</label>
                            <textarea class="form-control" id="example_data" name="example_data" rows="3"
                                placeholder='{"biaya": "Rp 50.000", "waktu_proses": "3-5 hari"}'></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Panduan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
