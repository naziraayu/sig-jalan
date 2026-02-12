@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Manajemen Ruas Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item active">Ruas Jalan</div>
            </div>
        </div>

        <div class="section-body">
            {{-- ✅ TAMBAH: Info tahun dan filter aktif --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="section-title mb-4">Daftar Ruas Jalan</h2>
                    <p class="section-lead mb-0">
                        Menampilkan data ruas jalan 
                        <span class="badge badge-primary" style="font-size: 13px; padding: 5px 10px;">
                            <i class="fas fa-calendar-alt"></i> Tahun {{ $selectedYear ?? date('Y') }}
                        </span>
                        <span class="badge badge-info" style="font-size: 13px; padding: 5px 10px;">
                            <i class="fas fa-map-marker-alt"></i> Provinsi: 35 - Jawa Timur
                        </span>
                        <span class="badge badge-info" style="font-size: 13px; padding: 5px 10px;">
                            <i class="fas fa-city"></i> Kabupaten: 09 - Jember
                        </span>
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Tabel Ruas Jalan</h4>
                    
                    {{-- ✅ Card Header Tools --}}
                    <div class="card-header-action">
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            @if(auth()->user()->hasPermission('add','ruas_jalan'))
                                <a href="{{ route('ruas-jalan.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Ruas Jalan
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('delete','ruas_jalan'))
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalDeleteAll">
                                    <i class="fas fa-trash"></i> Hapus Semua Data
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- ✅ Alert Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

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

                    {{-- ✅ DataTable --}}
                    <div class="table-responsive">
                        <table id="ruasJalanTable" class="table table-striped table-hover table-bordered" style="width: 100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 12%;">Kode SK</th>
                                    <th style="width: 30%;">Nama Ruas</th>
                                    <th style="width: 15%;">Provinsi</th>
                                    <th style="width: 15%;">Kabupaten</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 10%;">Fungsi Ruas</th>
                                    <th style="width: 8%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data will be loaded via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- ✅ Modal Delete All Confirmation --}}
<div class="modal fade" id="modalDeleteAll" tabindex="-1" role="dialog" aria-labelledby="modalDeleteAllLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalDeleteAllLabel">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus Semua Data
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('ruas-jalan.destroyAll') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Peringatan!</strong> Tindakan ini akan menghapus:
                    </div>
                    <ul>
                        <li>Semua data ruas jalan <strong>tahun {{ $selectedYear ?? date('Y') }}</strong></li>
                        <li>Filter: <strong>Provinsi 35 - Jawa Timur, Kabupaten 09 - Jember</strong></li>
                        <li>Data link master yang tidak memiliki data tahun lain</li>
                        <li>Tindakan ini <strong class="text-danger">TIDAK DAPAT DIBATALKAN</strong></li>
                    </ul>
                    <p class="mb-0">Apakah Anda yakin ingin melanjutkan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Ya, Hapus Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // ✅ Initialize DataTable
    let table = $('#ruasJalanTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('ruas-jalan.data') }}",
            type: 'GET',
            data: function (d) {
                d.filterProvinsi = '{{ $defaultProvinsi }}';
                d.filterKabupaten = '{{ $defaultKabupaten }}';
                console.log('DataTables Request:', d);
            },
            error: function(xhr, error, code) {
                console.error('DataTables Error:', {
                    status: xhr.status,
                    error: error,
                    code: code,
                    responseText: xhr.responseText
                });
                
                let errorMsg = 'Gagal memuat data. Silakan refresh halaman.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    } else if (response.error) {
                        errorMsg = response.error;
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
            }
        },
        columns: [
            { 
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'link_code', 
                name: 'link_code',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'link_name', 
                name: 'link_name',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'province_name', 
                name: 'province_name',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'kabupaten_name', 
                name: 'kabupaten_name',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'status_name', 
                name: 'status_name',
                render: function(data, type, row) {
                    if (!data || data === '-') {
                        return '<span class="badge badge-secondary">N/A</span>';
                    }
                    return '<span class="badge badge-info">' + data + '</span>';
                }
            },
            { 
                data: 'function_name', 
                name: 'function_name',
                render: function(data, type, row) {
                    if (!data || data === '-') {
                        return '<span class="badge badge-secondary">N/A</span>';
                    }
                    return '<span class="badge badge-primary">' + data + '</span>';
                }
            },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        order: [[1, 'asc']],
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
            searchPlaceholder: 'Cari data...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Tidak ada data yang ditemukan',
            emptyTable: 'Tidak ada data ruas jalan untuk tahun {{ $selectedYear ?? date("Y") }}, Provinsi 35 - Jawa Timur, Kabupaten 09 - Jember',
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                last: '<i class="fas fa-angle-double-right"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>'
            }
        },
        responsive: true,
        autoWidth: false,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
    });

    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const linkName = form.data('link-name');
        const linkCode = form.data('link-code');
        const year = form.data('year');
        const actionUrl = form.attr('action');
        
        // Extract link ID dari URL
        const linkId = actionUrl.split('/').pop();
        
        // ✅ Tampilkan loading indicator
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memeriksa data...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        // ✅ AJAX: Cek relasi terlebih dahulu
        $.ajax({
            url: `/ruas-jalan/${linkId}/check-relations`,
            method: 'GET',
            success: function(response) {
                if (!response.success) {
                    showErrorAlert('Gagal memeriksa data relasi');
                    return;
                }
                
                // ❌ JIKA ADA RELASI, TAMPILKAN PERINGATAN DAN BLOKIR HAPUS
                if (response.hasRelations) {
                    let relatedDataList = '';
                    
                    if (response.relations.drp) {
                        relatedDataList += `<li><i class="fas fa-road"></i> <strong>${response.relations.drp}</strong> Detail Ruas Panggal (DRP)</li>`;
                    }
                    if (response.relations.roadInventories) {
                        relatedDataList += `<li><i class="fas fa-clipboard-list"></i> <strong>${response.relations.roadInventories}</strong> Data Inventarisasi Jalan</li>`;
                    }
                    if (response.relations.roadConditions) {
                        relatedDataList += `<li><i class="fas fa-wrench"></i> <strong>${response.relations.roadConditions}</strong> Data Kondisi Jalan</li>`;
                    }
                    if (response.relations.linkKecamatans) {
                        relatedDataList += `<li><i class="fas fa-map-marked-alt"></i> <strong>${response.relations.linkKecamatans}</strong> Data Link Kecamatan</li>`;
                    }
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Tidak Dapat Menghapus!',
                            html: `
                                <div class="text-left">
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Data tidak dapat dihapus karena masih digunakan oleh data lain!</strong>
                                    </div>
                                    <hr>
                                    <table class="table table-sm table-borderless mb-3">
                                        <tr>
                                            <td width="40%"><strong><i class="fas fa-road"></i> Nama Ruas</strong></td>
                                            <td>: ${response.linkName || linkName}</td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="fas fa-barcode"></i> Kode SK</strong></td>
                                            <td>: ${response.linkCode || linkCode}</td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="fas fa-calendar-alt"></i> Tahun</strong></td>
                                            <td>: ${response.year || year}</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="mb-2"><strong><i class="fas fa-link"></i> Data yang masih terkait:</strong></p>
                                    <ul class="list-unstyled ml-3">${relatedDataList}</ul>
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="fas fa-info-circle"></i>
                                        <small><strong>Petunjuk:</strong> Silakan hapus atau pindahkan data terkait terlebih dahulu sebelum menghapus ruas jalan ini.</small>
                                    </div>
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonColor: '#6c757d',
                            confirmButtonText: '<i class="fas fa-check"></i> Mengerti',
                            customClass: {
                                popup: 'swal-wide',
                                htmlContainer: 'text-left'
                            }
                        });
                    } else {
                        // Fallback jika SweetAlert tidak tersedia
                        alert(`Tidak dapat menghapus!\n\nData tidak dapat dihapus karena masih digunakan oleh:\n${relatedDataList.replace(/<[^>]*>/g, '')}`);
                    }
                    
                    return; // ❌ STOP, tidak lanjut ke hapus
                }
                
                // ✅ JIKA TIDAK ADA RELASI, LANJUTKAN KONFIRMASI HAPUS
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        html: `
                            <div class="text-left">
                                <p>Apakah Anda yakin ingin menghapus ruas jalan berikut?</p>
                                <hr>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong><i class="fas fa-road"></i> Nama Ruas</strong></td>
                                        <td>: ${response.linkName || linkName}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-barcode"></i> Kode SK</strong></td>
                                        <td>: ${response.linkCode || linkCode}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-calendar-alt"></i> Tahun</strong></td>
                                        <td>: ${response.year || year}</td>
                                    </tr>
                                </table>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                        cancelButtonText: '<i class="fas fa-times"></i> Batal',
                        reverseButtons: true,
                        customClass: {
                            popup: 'swal-wide'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form[0].submit();
                        }
                    });
                } else {
                    // Fallback confirm dialog
                    const confirmMsg = `Apakah Anda yakin ingin menghapus ruas jalan berikut?\n\n` +
                                    `Nama Ruas: ${response.linkName || linkName}\n` +
                                    `Kode SK: ${response.linkCode || linkCode}\n` +
                                    `Tahun: ${response.year || year}\n\n` +
                                    `Tindakan ini tidak dapat dibatalkan!`;
                    
                    if (confirm(confirmMsg)) {
                        form[0].submit();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error checking relations:', {xhr, status, error});
                
                // Fallback: tetap tampilkan konfirmasi delete biasa
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Gagal memeriksa relasi data. Tetap lanjutkan hapus?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form[0].submit();
                        }
                    });
                } else {
                    if (confirm(`Gagal memeriksa relasi data. Tetap lanjutkan hapus ${linkName}?`)) {
                        form[0].submit();
                    }
                }
            }
        });
        
        return false;
    });

    // Helper function untuk error alert
    function showErrorAlert(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error',
                text: message,
                icon: 'error',
                confirmButtonColor: '#6c757d'
            });
        } else {
            alert('Error: ' + message);
        }
    }

    // ✅ Auto-adjust DataTable on sidebar toggle
    function adjustDataTable() {
        setTimeout(function () {
            table.columns.adjust().responsive.recalc();
        }, 350);
    }

    $(document).on('click', "[data-toggle='sidebar'], #sidebarToggle", function () {
        adjustDataTable();
    });

    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === "class") {
                adjustDataTable();
            }
        });
    });
    
    observer.observe(document.body, { 
        attributes: true,
        attributeFilter: ['class']
    });

    // ✅ Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
});
</script>

{{-- ✅ Custom CSS --}}
<style>
    .swal-wide {
        width: 650px !important;
        max-width: 90% !important;
    }
    
    .swal2-html-container {
        overflow: visible !important;
    }
    
    .swal2-html-container .list-unstyled li {
        padding: 5px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .swal2-html-container .list-unstyled li:last-child {
        border-bottom: none;
    }
    .badge {
        font-size: 11px;
        padding: 4px 8px;
        font-weight: 600;
    }

    .dataTables_filter input {
        border-radius: 20px;
        padding-left: 15px;
    }

    .table .btn {
        padding: 4px 8px;
        font-size: 12px;
        margin: 0 2px;
    }

    .gap-2 > * {
        margin-right: 0.5rem;
    }
    
    .gap-2 > *:last-child {
        margin-right: 0;
    }

    @media (max-width: 768px) {
        .card-header-action {
            width: 100%;
            margin-top: 10px;
        }
        
        .card-header-action .d-flex {
            justify-content: flex-start;
        }
    }

    .dataTables_processing {
        background: rgba(255, 255, 255, 0.9) !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
    }

    .section-lead .badge {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    /* ✅ TAMBAHAN: Custom SweetAlert2 width */
    .swal-wide {
        width: 600px !important;
    }
    
    .swal2-html-container {
        overflow: visible !important;
    }
</style>
@endpush