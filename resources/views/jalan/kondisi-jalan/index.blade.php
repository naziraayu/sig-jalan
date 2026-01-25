@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"> 
            <h1>Kondisi Jalan & Perhitungan SDI</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item active">Kondisi Jalan</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Analisis Kondisi Jalan</h2>
            <p class="section-lead">Menampilkan data kondisi jalan dan perhitungan Surface Distress Index (SDI).</p>

            <!-- Info Tahun Terpilih -->
            @if($selectedYear)
            <div class="alert alert-primary">
                <i class="fas fa-calendar-check"></i> 
                <strong>Tahun yang dipilih: {{ $selectedYear }}</strong> - Data yang ditampilkan sesuai dengan tahun ini.
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Peringatan:</strong> Silakan pilih <strong>Tahun</strong> terlebih dahulu di header atas halaman.
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>Filter Ruas Jalan</h4>
                </div>
                <div class="card-body">
                    <div class="form-row">

                        {{-- Status Ruas --}}
                        <div class="form-group col-md-3">
                            <label for="filterStatus">Status Ruas</label>
                            <select id="filterStatus" class="form-control" disabled>
                                @foreach($statusRuas as $status)
                                    <option value="{{ $status->code }}"
                                        {{ $status->code == 'K' ? 'selected' : '' }}>
                                        {{ $status->code_description_ind }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Status ruas saat ini: Kabupaten</small>
                        </div>

                        {{-- Provinsi --}}
                        <div class="form-group col-md-3">
                            <label for="filterProvinsi">Provinsi</label>
                            <select id="filterProvinsi" class="form-control" disabled>
                                @foreach($provinsi as $prov)
                                    <option value="{{ $prov->province_code }}"
                                        {{ $prov->province_name == 'Jawa Timur' ? 'selected' : '' }}>
                                        {{ $prov->province_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Provinsi saat ini: Jawa Timur</small>
                        </div>

                        {{-- Kabupaten --}}
                        <div class="form-group col-md-3">
                            <label for="filterKabupaten">Kabupaten</label>
                            <select id="filterKabupaten" class="form-control">
                                @foreach($kabupaten as $kab)
                                    <option value="{{ $kab->kabupaten_code }}"
                                        {{ $kab->kabupaten_name == 'Jember' ? 'selected' : '' }}>
                                        {{ $kab->kabupaten_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Pilih kabupaten untuk melihat ruas</small>
                        </div>

                        {{-- Ruas --}}
                        <div class="form-group col-md-3">
                            <label for="filterRuas">
                                Pilih Ruas <span class="text-danger">*</span>
                            </label>
                            <select id="filterRuas" class="form-control" {{ !$selectedYear ? 'disabled' : '' }}>
                                <option value="">
                                    {{ $selectedYear ? '-- Pilih Ruas --' : '-- Pilih Tahun di Header Terlebih Dahulu --' }}
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                {{ $selectedYear ? 'Ruas tersedia untuk tahun ' . $selectedYear : 'Pilih tahun terlebih dahulu' }}
                            </small>
                        </div>

                        {{-- Tombol Filter --}}
                        <div class="form-group col-md-6 d-flex align-items-end">
                            <button type="button" id="btnFilter" class="btn btn-primary btn-block" disabled>
                                <i class="fas fa-filter"></i> Tampilkan Data
                            </button>
                        </div>

                        {{-- Tombol Reset --}}
                        <div class="form-group col-md-6 d-flex align-items-end">
                            <button type="button" id="btnReset" class="btn btn-secondary btn-block">
                                <i class="fas fa-redo"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info SDI --}}
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="mb-3"><i class="fas fa-info-circle"></i> Kategori Surface Distress Index (SDI)</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="badge badge-success badge-lg p-2 w-100">
                                <i class="fas fa-check-circle"></i> Baik (SDI < 50)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="badge badge-warning badge-lg p-2 w-100" style="background-color: #FFD700; color: #fff;">
                                <i class="fas fa-exclamation-circle"></i> Sedang (50-100)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="badge badge-danger badge-lg p-2 w-100" style="background-color: #FFA500; color: #fff;">
                                <i class="fas fa-times-circle"></i> Rusak Ringan (100-150)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="badge badge-danger badge-lg p-2 w-100">
                                <i class="fas fa-ban"></i> Rusak Berat (SDI ≥ 150)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel hasil pilihan --}}
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Data Kondisi Jalan & Perhitungan SDI</h4>
                    <div class="d-flex flex-wrap gap-2">

                        {{-- Hapus Semua --}}
                        @if(auth()->user()->hasPermission('delete','kondisi_jalan'))
                            <form action="{{ route('kondisi-jalan.destroyAll') }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus semua data kondisi jalan? Semua data akan hilang!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon icon-left btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            </form>
                        @endif

                        {{-- Tambah Data --}}
                        @if(auth()->user()->hasPermission('add','kondisi_jalan'))
                            <a href="{{ route('kondisi-jalan.create') }}" class="btn btn-icon icon-left btn-primary">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        @endif

                    </div>
                </div>

                <div class="card-body">
                    <div id="detailRuas">
                        @if(!$selectedYear)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Peringatan:</strong> Silakan pilih <strong>Tahun</strong> terlebih dahulu di header atas halaman, kemudian pilih <strong>Ruas Jalan</strong> untuk melihat data.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Petunjuk:</strong> Pilih <strong>Ruas Jalan</strong> yang tersedia pada tahun <strong>{{ $selectedYear }}</strong>, lalu klik tombol <strong>"Tampilkan Data"</strong> untuk melihat data kondisi jalan dan perhitungan SDI.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        
        // Inisialisasi: Load ruas berdasarkan tahun yang dipilih di header
        function initializeRuas() {
            const selectedYear = '{{ $selectedYear }}';
            
            if (selectedYear) {
                loadRuasByYear(selectedYear);
            } else {
                $('#filterRuas').html('<option value="">-- Pilih Tahun di Header Terlebih Dahulu --</option>').prop('disabled', true);
                $('#btnFilter').prop('disabled', true);
            }
        }

        // Muat ruas berdasarkan tahun
        function loadRuasByYear(year) {
            if(!year) return;

            console.log('Loading ruas for year:', year);

            $('#filterRuas').html('<option value="">-- Memuat ruas... --</option>').prop('disabled', true);
            $('#btnFilter').prop('disabled', true);
            
            $.ajax({
                url: "{{ route('kondisi-jalan.getRuasByYear') }}",
                type: "GET",
                data: { year: year },
                success: function(res){
                    console.log('Response:', res);
                    
                    if(res.success && res.data && res.data.length > 0){
                        let options = '<option value="">-- Pilih Ruas --</option>';
                        res.data.forEach(function(ruas){
                            console.log('Ruas:', ruas);
                            options += `<option value="${ruas.link_no}">${ruas.link_code} - ${ruas.link_name}</option>`;
                        });
                        $('#filterRuas').html(options).prop('disabled', false);
                        
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({
                                title: 'Berhasil',
                                message: `${res.data.length} ruas berhasil dimuat`,
                                position: 'topRight',
                                timeout: 2000
                            });
                        }
                    } else {
                        $('#filterRuas').html('<option value="">-- Tidak ada data ruas --</option>');
                        
                        if (typeof iziToast !== 'undefined') {
                            iziToast.warning({
                                title: 'Peringatan',
                                message: res.message || 'Tidak ada data ruas untuk tahun ' + year,
                                position: 'topRight'
                            });
                        }
                    }
                },
                error: function(xhr, status, error){
                    console.error('Error loading ruas:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    $('#filterRuas').html('<option value="">-- Gagal memuat ruas --</option>');
                    
                    if (typeof iziToast !== 'undefined') {
                        iziToast.error({
                            title: 'Error',
                            message: 'Terjadi kesalahan saat memuat data ruas',
                            position: 'topRight'
                        });
                    }
                }
            });
        }

        // Ketika RUAS dipilih
        $('#filterRuas').on('change', function(){
            let linkNo = $(this).val();
            
            if(linkNo){
                $('#btnFilter').prop('disabled', false);
            } else {
                $('#btnFilter').prop('disabled', true);
            }
        });

        // Tombol Filter
        $('#btnFilter').on('click', function(){
            let linkNo = $('#filterRuas').val();
            let year = '{{ $selectedYear }}';
            
            if(linkNo && year){
                loadRoadConditionData(linkNo, year);
            } else {
                if (typeof iziToast !== 'undefined') {
                    iziToast.warning({
                        title: 'Peringatan',
                        message: 'Silakan pilih ruas terlebih dahulu!',
                        position: 'topRight'
                    });
                } else {
                    alert('Silakan pilih ruas terlebih dahulu!');
                }
            }
        });

        // Tombol Reset
        $('#btnReset').on('click', function(){
            $('#filterRuas').val('');
            $('#btnFilter').prop('disabled', true);
            
            if ($.fn.DataTable.isDataTable('#detailRuasTable')) {
                $('#detailRuasTable').DataTable().destroy();
            }
            
            $('#detailRuas').html(`
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Petunjuk:</strong> Pilih <strong>Ruas Jalan</strong> yang tersedia pada tahun <strong>{{ $selectedYear }}</strong>, lalu klik tombol <strong>"Tampilkan Data"</strong> untuk melihat data kondisi jalan dan perhitungan SDI.
                </div>
            `);
        });

        function loadRoadConditionData(linkNo, year){
            $('#detailRuas').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data kondisi jalan...</p>
                </div>
            `);

            $.ajax({
                url: "{{ route('kondisi-jalan.getDetail') }}",
                type: "GET",
                data: {
                    link_no: linkNo,
                    year: year
                },
                success: function(res){
                    if(res.success && res.data.length > 0){
                        let html = `
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> 
                                Data berhasil dimuat: <strong>${res.data.length} segmen</strong> untuk ruas <strong>${linkNo}</strong> tahun <strong>${year}</strong>
                            </div>
                            <div class="table-responsive">
                                <table id="detailRuasTable" class="table table-striped table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Chainage</th>
                                            <th class="text-center">Tahun</th>
                                            <th class="text-center">Lebar Jalan (m)</th>
                                            <th class="text-center">SDI Final</th>
                                            <th class="text-center">Kategori</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                        </thead>

                                    <tbody>
                        `;

                        res.data.forEach(function(item, index){
                            let badgeClass = 'badge';
                            let badgeStyle = '';
                            let iconClass = 'fa-question';
                            
                            if(item.sdi_category === 'Baik') {
                                badgeClass += ' badge-success';
                                iconClass = 'fa-check-circle';
                            } 
                            else if(item.sdi_category === 'Sedang') {
                                badgeClass += ' badge-warning';
                                badgeStyle = 'background-color: #FFD700; color: #fff;';
                                iconClass = 'fa-exclamation-circle';
                            } 
                            else if(item.sdi_category === 'Rusak Ringan') {
                                badgeClass += ' badge-warning';
                                badgeStyle = 'background-color: #FFA500; color: #fff;';
                                iconClass = 'fa-times-circle';
                            } 
                            else if(item.sdi_category === 'Rusak Berat') {
                                badgeClass += ' badge-danger';
                                iconClass = 'fa-ban';
                            }

                            html += `
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td><strong>${item.chainage_from}</strong> - <strong>${item.chainage_to}</strong></td>
                                    <td class="text-center">${item.year}</td>
                                    <td class="text-center">${(item.pave_width ?? 0).toFixed(2)}</td>

                                    <td class="text-center font-weight-bold text-primary">
                                        ${(item.sdi_final ?? 0).toFixed(2)}
                                    </td>

                                    <td class="text-center">
                                        <span class="${badgeClass}" style="${badgeStyle}">
                                            <i class="fas ${iconClass}"></i> ${item.sdi_category}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="/kondisi-jalan/show/${item.link_no}" class="btn btn-sm btn-info" title="Lihat Detail Ruas">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->hasPermission('update','kondisi_jalan'))
                                        <a href="/kondisi-jalan/${item.link_no}/${item.chainage_from}/${item.chainage_to}/${item.year}/edit" class="btn btn-sm btn-warning" title="Edit Segmen">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                        @if(auth()->user()->hasPermission('delete','kondisi_jalan'))
                                        <button onclick="deleteSegment('${item.link_no}', ${item.chainage_from}, ${item.chainage_to}, ${item.year})" class="btn btn-sm btn-danger" title="Hapus Segmen">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                            `;
                        });

                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;

                        $('#detailRuas').html(html);

                        $('#detailRuasTable').DataTable({
                            responsive: true,
                            autoWidth: false,
                            pageLength: 25,
                            lengthMenu: [10, 25, 50, 100, 200],
                            order: [[1, 'asc']],
                            columnDefs: [
                                {
                                    targets: 1,
                                    type: 'num',
                                    render: function(data, type, row) {
                                        if (type === 'sort') {
                                            var match = data.match(/[\d.]+/);
                                            return match ? parseFloat(match[0]) : 0;
                                        }
                                        return data;
                                    }
                                }
                            ],
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                            },
                            dom: 'Bfrtip',
                            buttons: [
                                {
                                    extend: 'excel',
                                    text: '<i class="fas fa-file-excel"></i> Export Excel',
                                    className: 'btn btn-success btn-sm',
                                    exportOptions: {
                                        columns: [0,1,2,3,4,5]
                                    }
                                },
                                {
                                    extend: 'pdf',
                                    text: '<i class="fas fa-file-pdf"></i> Export PDF',
                                    className: 'btn btn-danger btn-sm',
                                    exportOptions: {
                                        columns: [0,1,2,3,4,5]
                                    },
                                    orientation: 'landscape',
                                    pageSize: 'A4'
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="fas fa-print"></i> Print',
                                    className: 'btn btn-info btn-sm',
                                    exportOptions: {
                                        columns: [0,1,2,3,4,5]
                                    }
                                }
                            ]
                        });

                    } else {
                        $('#detailRuas').html(`
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                ${res.message || 'Data tidak ditemukan untuk ruas dan tahun yang dipilih'}
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    $('#detailRuas').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> 
                            Terjadi kesalahan saat memuat data. Silakan coba lagi.
                        </div>
                    `);
                }
            });
        }

        // ✅ FUNCTION DELETE SEGMENT
        window.deleteSegment = function(linkNo, chainageFrom, chainageTo, year) {
            Swal.fire({
                title: 'Hapus Segmen?',
                html: `Yakin ingin menghapus segmen:<br><strong>${chainageFrom} - ${chainageTo}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/kondisi-jalan/${linkNo}/${chainageFrom}/${chainageTo}/${year}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Data berhasil dihapus',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    // Reload data
                                    $('#btnFilter').click();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message || 'Gagal menghapus data',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus data',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        };

        initializeRuas();
    });
</script>
@endpush