@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Export / Import Data</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item">Export / Import</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Manajemen Data</h2>
            <p class="section-lead">Export dan Import data untuk berbagai modul sistem.</p>

            <div class="card">
                <div class="card-header">
                    <h4>Pilih Menu untuk Export / Import</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form id="menuForm">
                        <div class="form-group">
                            <label class="d-block mb-3"><strong>Pilih Menu:</strong></label>
                            <div class="row">
                                @foreach($availableMenus as $key => $config)
                                <div class="col-md-4 mb-3">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" 
                                               id="menu_{{ $key }}" 
                                               name="menu_type" 
                                               value="{{ $key }}" 
                                               class="custom-control-input"
                                               required>
                                        <label class="custom-control-label" for="menu_{{ $key }}">
                                            {{ $config['label'] }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ✅ IMPROVED: Filter untuk Kondisi Jalan --}}
                        <div class="form-group" id="filter_section" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3">
                                        <i class="fas fa-filter"></i> 
                                        <strong>Filter Export (Khusus Kondisi Jalan)</strong>
                                    </h6>
                                    <div class="alert alert-warning mb-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Penting:</strong> Untuk performa optimal, <u>sangat disarankan</u> menggunakan filter tahun. 
                                        Export semua data sekaligus bisa memakan waktu lama dan mengonsumsi banyak memory.
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label><strong>Filter Tahun:</strong> <span class="text-danger">*Disarankan</span></label>
                                            <select name="year" id="export_year" class="form-control">
                                                <option value="">⚠️ Semua Tahun (tidak disarankan - lambat!)</option>
                                                @php
                                                    $currentYear = date('Y');
                                                @endphp
                                                @for($y = $currentYear; $y >= 2015; $y--)
                                                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>
                                                        Tahun {{ $y }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Export per tahun <strong>jauh lebih cepat</strong> (estimasi 10-30 detik vs 5-10 menit)
                                            </small>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label><strong>Filter Provinsi:</strong> <span class="text-muted">(Opsional)</span></label>
                                            <select name="province_code" id="export_province" class="form-control">
                                                <option value="">Semua Provinsi</option>
                                                {{-- Isi dengan data provinsi dari database --}}
                                                @if(isset($provinces))
                                                    @foreach($provinces as $prov)
                                                        <option value="{{ $prov->code }}">{{ $prov->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Kombinasi tahun + provinsi untuk hasil lebih cepat
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="button" 
                                    class="btn btn-success btn-lg mr-2" 
                                    onclick="handleExport()">
                                <i class="fas fa-download"></i> Export Data
                            </button>
                            
                            <button type="button" 
                                    class="btn btn-primary btn-lg" 
                                    data-toggle="modal" 
                                    data-target="#importModal">
                                <i class="fas fa-upload"></i> Import Data
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> Informasi:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pilih menu yang ingin di-export atau import menggunakan radio button di atas</li>
                            <li><strong>Export:</strong> Mengunduh semua data dari menu yang dipilih dalam format Excel (.xlsx)</li>
                            <li><strong>Kondisi Jalan:</strong> 
                                <ul>
                                    <li><span class="badge badge-success">Disarankan</span> Export per tahun - proses <strong>10-30 detik</strong></li>
                                    <li><span class="badge badge-warning">Tidak Disarankan</span> Export semua tahun - bisa <strong>5-10 menit</strong> atau lebih</li>
                                    <li><span class="badge badge-info">Optimal</span> Gunakan filter tahun + provinsi untuk hasil tercepat</li>
                                </ul>
                            </li>
                            <li><strong>Import:</strong> Mengunggah file Excel untuk menambah data baru. Data dengan ID yang sudah ada akan diabaikan</li>
                            <li>File import harus menggunakan format yang sama dengan hasil export</li>
                            <li>Maksimal ukuran file import: 20MB</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('import_export.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="menu_type" id="import_menu_type">
                    
                    <div class="alert alert-warning">
                        <strong>Menu yang dipilih:</strong> <span id="selected_menu_label">-</span>
                    </div>

                    <div class="form-group">
                        <label for="file">Pilih File Excel (.xlsx atau .xls)</label>
                        <input type="file" 
                               class="form-control" 
                               id="file" 
                               name="file" 
                               accept=".xlsx,.xls" 
                               required>
                        <small class="form-text text-muted">Format file harus sesuai dengan template export</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ✅ Loading Modal untuk Export --}}
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <h5>Sedang Mengexport Data...</h5>
                <p class="text-muted mb-0">
                    Mohon tunggu, proses ini mungkin memakan waktu beberapa saat.<br>
                    <small id="loading_estimate"></small>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // ✅ Show/hide filter section based on selected menu
        $('input[name="menu_type"]').on('change', function() {
            const selectedValue = $(this).val();
            
            if (selectedValue === 'kondisi_jalan') {
                $('#filter_section').slideDown();
            } else {
                $('#filter_section').slideUp();
            }
        });
        
        // ✅ Update estimasi waktu berdasarkan filter
        $('#export_year, #export_province').on('change', function() {
            updateTimeEstimate();
        });
    });

    function updateTimeEstimate() {
        const year = $('#export_year').val();
        const province = $('#export_province').val();
        
        let estimate = '';
        if (year && province) {
            estimate = 'Estimasi: 5-15 detik';
        } else if (year) {
            estimate = 'Estimasi: 10-30 detik';
        } else if (province) {
            estimate = 'Estimasi: 1-3 menit';
        } else {
            estimate = 'Estimasi: 5-10 menit (atau lebih)';
        }
        
        $('#loading_estimate').text(estimate);
    }

    // ✅ IMPROVED: Handle Export dengan loading indicator
    function handleExport() {
        const selectedMenu = document.querySelector('input[name="menu_type"]:checked');
        
        if (!selectedMenu) {
            alert('Silakan pilih menu terlebih dahulu!');
            return;
        }

        // ✅ Validasi untuk kondisi jalan tanpa filter tahun
        if (selectedMenu.value === 'kondisi_jalan') {
            const yearValue = document.getElementById('export_year').value;
            
            if (!yearValue) {
                const confirmExport = confirm(
                    '⚠️ PERINGATAN!\n\n' +
                    'Anda akan mengexport SEMUA data kondisi jalan tanpa filter tahun.\n' +
                    'Proses ini bisa memakan waktu 5-10 menit atau lebih dan menggunakan banyak memory.\n\n' +
                    'SANGAT DISARANKAN untuk memilih tahun tertentu terlebih dahulu.\n\n' +
                    'Apakah Anda yakin ingin melanjutkan?'
                );
                
                if (!confirmExport) {
                    return;
                }
            }
        }

        // Update time estimate
        updateTimeEstimate();
        
        // Show loading modal
        $('#loadingModal').modal('show');

        // Create form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("import_export.export") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const menuInput = document.createElement('input');
        menuInput.type = 'hidden';
        menuInput.name = 'menu_type';
        menuInput.value = selectedMenu.value;
        
        form.appendChild(csrfToken);
        form.appendChild(menuInput);
        
        // ✅ FIXED: Kirim filter tahun dan provinsi untuk kondisi_jalan
        if (selectedMenu.value === 'kondisi_jalan') {
            const yearValue = document.getElementById('export_year').value;
            const provinceValue = document.getElementById('export_province').value;
            
            if (yearValue) {
                const yearInput = document.createElement('input');
                yearInput.type = 'hidden';
                yearInput.name = 'year';
                yearInput.value = yearValue;
                form.appendChild(yearInput);
            }
            
            if (provinceValue) {
                const provinceInput = document.createElement('input');
                provinceInput.type = 'hidden';
                provinceInput.name = 'province_code';
                provinceInput.value = provinceValue;
                form.appendChild(provinceInput);
            }
        }
        
        document.body.appendChild(form);
        form.submit();
        
        // Hide loading after 3 seconds (download will start)
        setTimeout(function() {
            $('#loadingModal').modal('hide');
        }, 3000);
    }

    // Handle Import Modal
    $('#importModal').on('show.bs.modal', function (e) {
        const selectedMenu = document.querySelector('input[name="menu_type"]:checked');
        
        if (!selectedMenu) {
            alert('Silakan pilih menu terlebih dahulu!');
            e.preventDefault();
            return;
        }

        const menuLabel = selectedMenu.nextElementSibling.textContent.trim();
        document.getElementById('import_menu_type').value = selectedMenu.value;
        document.getElementById('selected_menu_label').textContent = menuLabel;
    });
</script>
@endpush