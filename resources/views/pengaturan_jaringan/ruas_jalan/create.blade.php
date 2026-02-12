@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Ruas Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('ruas-jalan.index') }}">Ruas Jalan</a></div>
                <div class="breadcrumb-item">Tambah</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Tambah Ruas Jalan</h2>
            <p class="section-lead">Gunakan form ini untuk menambahkan data ruas jalan baru.</p>

            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('ruas-jalan.store') }}" method="POST" id="formTambahRuas">
                        @csrf

                        {{-- ✅ SECTION 1: Tahun & Kode Otomatis --}}
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Tahun & Kode Ruas</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="year">
                                            <i class="fas fa-calendar"></i> Tahun <span class="text-danger">*</span>
                                        </label>
                                        <select name="year" id="year" class="form-control" required>
                                            @for($y = now()->year + 1; $y >= 2020; $y--)
                                                <option value="{{ $y }}" {{ old('year', $currentYear) == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                        <small class="form-text text-muted">
                                            Pilih tahun untuk data ruas jalan ini
                                        </small>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="link_no">
                                            <i class="fas fa-barcode"></i> Link No <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="link_no" id="link_no"
                                            class="form-control bg-light" 
                                            value="{{ old('link_no', $newLinkNo) }}" 
                                            readonly required>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Otomatis digenerate berdasarkan tahun
                                        </small>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="link_code">
                                            <i class="fas fa-code"></i> Kode Ruas (Link Code) <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="link_code" id="link_code"
                                            class="form-control bg-light" 
                                            value="{{ old('link_code', $newLinkCode) }}" 
                                            readonly required>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Format: 35.09.XXXX
                                        </small>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>&nbsp;</label>
                                        <button type="button" id="btnRegenerate" class="btn btn-info btn-block">
                                            <i class="fas fa-sync-alt"></i> Regenerate Kode
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ SECTION 2: Informasi Ruas --}}
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-road"></i> Informasi Ruas Jalan</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="link_name">
                                        <i class="fas fa-tag"></i> Nama Ruas <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="link_name" id="link_name"
                                        class="form-control" 
                                        value="{{ old('link_name') }}" 
                                        placeholder="Contoh: Jl. Raya Bondowoso - Situbondo"
                                        required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="province_code">
                                            <i class="fas fa-map-marked-alt"></i> Provinsi <span class="text-danger">*</span>
                                        </label>
                                        <select name="province_code" id="province_code" class="form-control" required>
                                            <option value="">-- Pilih Provinsi --</option>
                                            @foreach($provinsi as $prov)
                                                <option value="{{ $prov->province_code }}" 
                                                    {{ old('province_code', $defaultProvinsi ?? '') == $prov->province_code ? 'selected' : '' }}>
                                                    {{ $prov->province_code }} - {{ $prov->province_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="kabupaten_code">
                                            <i class="fas fa-city"></i> Kabupaten/Kota <span class="text-danger">*</span>
                                        </label>
                                        <select name="kabupaten_code" id="kabupaten_code" class="form-control" required>
                                            <option value="">-- Pilih Kabupaten --</option>
                                            @foreach($kabupaten as $kab)
                                                <option value="{{ $kab->kabupaten_code }}" 
                                                    {{ old('kabupaten_code', $defaultKabupaten ?? '') == $kab->kabupaten_code ? 'selected' : '' }}>
                                                    {{ $kab->kabupaten_code }} - {{ $kab->kabupaten_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ SECTION 3: Status & Fungsi --}}
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-cogs"></i> Status & Fungsi Ruas</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="status">
                                            <i class="fas fa-check-circle"></i> Status Ruas
                                        </label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">-- Pilih Status --</option>
                                            @foreach($statusList as $status)
                                                <option value="{{ $status->code }}" 
                                                    {{ old('status') == $status->code ? 'selected' : '' }}>
                                                    {{ $status->code_description_ind }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="function">
                                            <i class="fas fa-tasks"></i> Fungsi Ruas
                                        </label>
                                        <select name="function" id="function" class="form-control">
                                            <option value="">-- Pilih Fungsi --</option>
                                            @foreach($functionList as $func)
                                                <option value="{{ $func->code }}" 
                                                    {{ old('function') == $func->code ? 'selected' : '' }}>
                                                    {{ $func->code_description_ind }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ SECTION 4: Panjang Ruas --}}
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-ruler-horizontal"></i> Panjang Ruas (Km)</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="link_length_official">
                                            <i class="fas fa-file-contract"></i> Panjang Ruas (SK)
                                        </label>
                                        <input type="number" step="0.001" name="link_length_official" 
                                            id="link_length_official"
                                            class="form-control" 
                                            value="{{ old('link_length_official') }}"
                                            placeholder="0.000">
                                        <small class="form-text text-muted">
                                            Panjang resmi berdasarkan Surat Keputusan
                                        </small>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="link_length_actual">
                                            <i class="fas fa-map-marker-alt"></i> Panjang Ruas (Survei)
                                        </label>
                                        <input type="number" step="0.001" name="link_length_actual" 
                                            id="link_length_actual"
                                            class="form-control" 
                                            value="{{ old('link_length_actual') }}"
                                            placeholder="0.000">
                                        <small class="form-text text-muted">
                                            Panjang aktual berdasarkan survei lapangan
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ SECTION 5: Informasi Tambahan --}}
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Tambahan</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="access_status">
                                        <i class="fas fa-sign-in-alt"></i> Akses ke Jalan
                                    </label>
                                    <input type="text" name="access_status" id="access_status"
                                        class="form-control" 
                                        value="{{ old('access_status') }}"
                                        placeholder="Contoh: Terbuka untuk umum">
                                    <small class="form-text text-muted">
                                        Lewati jika tidak perlu
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ SECTION 6: Action Buttons --}}
                        <div class="form-group text-right">
                            <a href="{{ route('ruas-jalan.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    /**
     * ✅ Auto-generate Link No & Link Code saat tahun berubah
     */
    $('#year').on('change', function() {
        const year = $(this).val();
        
        if (!year) {
            return;
        }

        // Show loading state
        $('#link_no').val('Generating...').prop('disabled', true);
        $('#link_code').val('Generating...').prop('disabled', true);
        $('#btnRegenerate').prop('disabled', true);

        // AJAX request
        $.ajax({
            url: "{{ route('ruas-jalan.generateCodes') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                year: year
            },
            success: function(response) {
                console.log('Response:', response);
                console.log('Link No:', response.link_no);
                console.log('Link Code:', response.link_code);
                
                $('#link_no').val(response.link_no);
                $('#link_code').val(response.link_code);

                console.log('Link No setelah set:', $('#link_no').val());
                console.log('Link Code setelah set:', $('#link_code').val());
                
                // Show notification
                if (typeof iziToast !== 'undefined') {
                    iziToast.success({
                        title: 'Berhasil',
                        message: 'Kode ruas berhasil digenerate untuk tahun ' + year,
                        position: 'topRight',
                        timeout: 3000
                    });
                }
            },
            error: function(xhr) {
                console.error('Error generating codes:', xhr);
                console.error('Response Text:', xhr.responseText);
                
                let errorMsg = 'Gagal generate kode ruas';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                } catch(e) {
                    console.error('Parse error:', e);
                }
                
                if (typeof iziToast !== 'undefined') {
                    iziToast.error({
                        title: 'Error',
                        message: errorMsg,
                        position: 'topRight',
                        timeout: 5000
                    });
                } else {
                    alert('Error: ' + errorMsg);
                }
                
                // Reset to default values
                $('#link_no').val('{{ $newLinkNo }}');
                $('#link_code').val('{{ $newLinkCode }}');
            },
            complete: function() {
                $('#link_no').prop('disabled', false);
                $('#link_code').prop('disabled', false);
                $('#btnRegenerate').prop('disabled', false);
            }
        });
    });

    /**
     * ✅ Regenerate button handler
     */
    $('#btnRegenerate').on('click', function() {
        const year = $('#year').val();
        
        if (!year) {
            if (typeof iziToast !== 'undefined') {
                iziToast.warning({
                    title: 'Peringatan',
                    message: 'Silakan pilih tahun terlebih dahulu',
                    position: 'topRight',
                    timeout: 3000
                });
            } else {
                alert('Silakan pilih tahun terlebih dahulu');
            }
            return;
        }

        // Trigger year change to regenerate
        $('#year').trigger('change');
    });

    /**
     * ✅ Form validation sebelum submit
     */
    $('#formTambahRuas').on('submit', function(e) {
        const requiredFields = [
            { id: '#year', name: 'Tahun' },
            { id: '#link_no', name: 'Link No' },
            { id: '#link_code', name: 'Link Code' },
            { id: '#link_name', name: 'Nama Ruas' },
            { id: '#province_code', name: 'Provinsi' },
            { id: '#kabupaten_code', name: 'Kabupaten' }
        ];

        let hasError = false;
        let errorMsg = '';

        requiredFields.forEach(function(field) {
            const value = $(field.id).val();
            if (!value || value.trim() === '') {
                hasError = true;
                errorMsg += '• ' + field.name + ' harus diisi<br>';
                $(field.id).addClass('is-invalid');
            } else {
                $(field.id).removeClass('is-invalid');
            }
        });

        if (hasError) {
            e.preventDefault();
            
            if (typeof iziToast !== 'undefined') {
                iziToast.error({
                    title: 'Validasi Error',
                    message: errorMsg,
                    position: 'topRight',
                    timeout: 5000
                });
            } else {
                alert('Validasi Error:\n' + errorMsg.replace(/<br>/g, '\n'));
            }
            
            // Focus ke field pertama yang error
            $('.is-invalid').first().focus();
            
            return false;
        }
    });

    /**
     * ✅ Remove is-invalid class saat user mulai mengetik
     */
    $('input, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    /**
     * ✅ Auto-hide alerts after 5 seconds
     */
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
});
</script>

{{-- ✅ Custom CSS --}}
<style>
    /* Card header styling */
    .card-header h6 {
        font-weight: 600;
        margin: 0;
    }

    /* Required field indicator */
    .text-danger {
        font-weight: bold;
    }

    /* Form control focus state */
    .form-control:focus {
        border-color: #6777ef;
        box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.25);
    }

    /* Invalid state */
    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Readonly fields */
    .bg-light[readonly] {
        background-color: #f8f9fa !important;
        cursor: not-allowed;
    }

    /* Section cards */
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
    }

    /* Button spacing */
    .btn-lg {
        padding: 0.5rem 1.5rem;
        font-size: 1rem;
    }

    /* Icon spacing */
    .fas, .far {
        margin-right: 5px;
    }

    /* Small text */
    .form-text {
        font-size: 0.875rem;
    }

    /* Alert styling */
    .alert {
        border-radius: 5px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-lg {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .text-right {
            text-align: center !important;
        }
    }
</style>
@endpush