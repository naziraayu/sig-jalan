{{-- File: resources/views/jalan/inventarisasi-jalan/show.blade.php --}}
@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Detail Ruas Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('inventarisasi-jalan.index') }}">Inventarisasi Jalan</a></div>
                <div class="breadcrumb-item active">Detail</div>
            </div>
        </div>

        <div class="section-body">
            {{-- Header Info Card --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h4><i class="fas fa-road"></i> {{ $ruas->link_code }} - {{ $ruas->link_name }}</h4>
                    <div class="card-header-action">
                        <a href="{{ route('inventarisasi-jalan.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                                <h6 class="font-weight-bold">Lokasi</h6>
                                <p class="mb-0">{{ $ruas->province->province_name ?? '-' }}</p>
                                <small class="text-muted">{{ $ruas->kabupaten->kabupaten_name ?? '-' }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-hashtag fa-3x text-info mb-3"></i>
                                <h6 class="font-weight-bold">Nomor SK</h6>
                                <p class="mb-0 h5">{{ $ruas->link_no }}</p>
                                <small class="text-muted">Kode: {{ $ruas->link_code }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-list-ol fa-3x text-success mb-3"></i>
                                <h6 class="font-weight-bold">Total Segmen</h6>
                                <p class="mb-0 h5">{{ $inventories->count() }}</p>
                                <small class="text-muted">Segmen Jalan</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-ruler fa-3x text-warning mb-3"></i>
                                <h6 class="font-weight-bold">Total Panjang</h6>
                                <p class="mb-0 h5">{{ number_format($statistics['total_length'], 2) }}</p>
                                <small class="text-muted">Meter</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-road"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Rata-rata Lebar</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($statistics['average_width'], 2) }} m
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Dapat Dilalui</h4>
                            </div>
                            <div class="card-body">
                                {{ $statistics['passable_count'] }} Segmen
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Bermasalah</h4>
                            </div>
                            <div class="card-body">
                                {{ $statistics['impassable_count'] }} Segmen
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Rata-rata ROW</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($statistics['average_row'], 2) }} m
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Problematic Segments Alert --}}
            @if($statistics['impassable_count'] > 0)
            <div class="alert alert-danger alert-has-icon">
                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="alert-body">
                    <div class="alert-title">Peringatan!</div>
                    Terdapat {{ $statistics['impassable_count'] }} segmen yang tidak dapat dilalui. 
                    <a href="#problematic-segments" class="alert-link">Lihat detail di bawah</a>
                </div>
            </div>
            @endif

            {{-- Inventory Segments --}}
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-list"></i> Detail Inventarisasi per Segmen</h4>
                    <div class="card-header-action">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary" id="viewCards">
                                <i class="fas fa-th-large"></i> Card View
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="viewTable">
                                <i class="fas fa-table"></i> Table View
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    {{-- Card View --}}
                    <div id="cardView" class="p-3" style="display: block;">
                        <div class="row">
                            @foreach($inventories as $index => $inventory)
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="card {{ $inventory->impassable == 1 ? 'border-danger' : 'border-success' }}">
                                    <div class="card-header {{ $inventory->impassable == 1 ? 'bg-danger' : 'bg-success' }} text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-road"></i> 
                                            Segmen {{ $index + 1 }} 
                                            ({{ number_format($inventory->chainage_from, 3) }} - {{ number_format($inventory->chainage_to, 3) }}m)
                                        </h6>
                                        <div class="card-header-action">
                                            <span class="badge {{ $inventory->impassable == 1 ? 'badge-light' : 'badge-light' }}">
                                                {{ number_format($inventory->chainage_to - $inventory->chainage_from, 3) }}m
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Basic Info --}}
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-info-circle text-primary"></i> Informasi Dasar</h6>
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <td class="text-muted">Lebar Perkerasan:</td>
                                                        <td><strong>{{ $inventory->pave_width ? number_format($inventory->pave_width, 2) . 'm' : '-' }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Tipe Perkerasan:</td>
                                                        <td><strong>{{ $inventory->pavementType->code_description_ind ?? '-' }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">ROW:</td>
                                                        <td><strong>{{ $inventory->row ? number_format($inventory->row, 2) . 'm' : '-' }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Terrain:</td>
                                                        <td><strong>{{ $inventory->terrainType->code_description_ind ?? '-' }}</strong></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            
                                            {{-- Additional Info --}}
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-cogs text-info"></i> Detail Tambahan</h6>
                                                
                                                {{-- Shoulder Info --}}
                                                @if($inventory->should_with_L || $inventory->should_with_R)
                                                <div class="mb-2">
                                                    <small class="text-muted">Bahu Jalan:</small><br>
                                                    @if($inventory->should_with_L)
                                                        <span class="badge badge-info">Kiri: {{ $inventory->should_with_L }}m</span>
                                                    @endif
                                                    @if($inventory->should_with_R)
                                                        <span class="badge badge-info">Kanan: {{ $inventory->should_with_R }}m</span>
                                                    @endif
                                                </div>
                                                @endif

                                                {{-- DRP Info --}}
                                                @if($inventory->drp_from || $inventory->drp_to)
                                                <div class="mb-2">
                                                    <small class="text-muted">DRP:</small><br>
                                                    <small>From: {{ $inventory->drp_from ?? '-' }} | To: {{ $inventory->drp_to ?? '-' }}</small>
                                                </div>
                                                @endif

                                                {{-- Status --}}
                                                <div class="mb-2">
                                                    @if($inventory->impassable == 1)
                                                        <span class="badge badge-danger">Tidak Dapat Dilalui</span><br>
                                                        <small class="text-danger">{{ $inventory->impassableReason->code_description_ind ?? '-' }}</small>
                                                    @else
                                                        <span class="badge badge-success">Dapat Dilalui</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Land Use & Drainage --}}
                                        <div class="row mt-3">
                                            <div class="col-6">
                                                <h6 class="text-muted mb-2"><i class="fas fa-seedling"></i> Penggunaan Lahan</h6>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted">Kiri:</small><br>
                                                        <small>{{ $inventory->landUseL->code_description_ind ?? '-' }}</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Kanan:</small><br>
                                                        <small>{{ $inventory->landUseR->code_description_ind ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-muted mb-2"><i class="fas fa-tint"></i> Drainase</h6>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted">Kiri:</small><br>
                                                        <small>{{ $inventory->drainTypeL->code_description_ind ?? '-' }}</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Kanan:</small><br>
                                                        <small>{{ $inventory->drainTypeR->code_description_ind ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Table View --}}
                    <div id="tableView" style="display: none;" class="table-responsive">
                        <table class="table table-striped" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Segmen</th>
                                    <th>Chainage</th>
                                    <th>Lebar</th>
                                    <th>Tipe Perkerasan</th>
                                    <th>ROW</th>
                                    <th>Terrain</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventories as $index => $inventory)
                                <tr class="{{ $inventory->impassable == 1 ? 'table-danger' : '' }}">
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td>
                                        {{ number_format($inventory->chainage_from, 3) }} - {{ number_format($inventory->chainage_to, 3) }}<br>
                                        <small class="text-muted">({{ number_format($inventory->chainage_to - $inventory->chainage_from, 3) }}m)</small>
                                    </td>
                                    <td>{{ $inventory->pave_width ? number_format($inventory->pave_width, 2) . 'm' : '-' }}</td>
                                    <td>{{ $inventory->pavementType->code_description_ind ?? '-' }}</td>
                                    <td>{{ $inventory->row ? number_format($inventory->row, 2) . 'm' : '-' }}</td>
                                    <td>{{ $inventory->terrainType->code_description_ind ?? '-' }}</td>
                                    <td>
                                        @if($inventory->impassable == 1)
                                            <span class="badge badge-danger">Bermasalah</span><br>
                                            <small>{{ $inventory->impassableReason->code_description_ind ?? '-' }}</small>
                                        @else
                                            <span class="badge badge-success">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Analysis Cards --}}
            <div class="row">
                {{-- Pavement Types Distribution --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-chart-pie"></i> Distribusi Tipe Perkerasan</h4>
                        </div>
                        <div class="card-body">
                            @forelse($statistics['pavement_types'] as $type => $count)
                                @php
                                    $pavementType = $inventories->where('pave_type', $type)->first()?->pavementType;
                                    $percentage = ($count / $statistics['total_segments']) * 100;
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>{{ $pavementType->code_description_ind ?? $type }}</span>
                                        <span>{{ $count }} segmen ({{ number_format($percentage, 1) }}%)</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">Tidak ada data tipe perkerasan</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Terrain Types Distribution --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-mountain"></i> Distribusi Tipe Terrain</h4>
                        </div>
                        <div class="card-body">
                            @forelse($statistics['terrain_types'] as $type => $count)
                                @php
                                    $terrainType = $inventories->where('terrain', $type)->first()?->terrainType;
                                    $percentage = ($count / $statistics['total_segments']) * 100;
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>{{ $terrainType->code_description_ind ?? $type }}</span>
                                        <span>{{ $count }} segmen ({{ number_format($percentage, 1) }}%)</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">Tidak ada data tipe terrain</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shoulder Analysis --}}
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-road"></i> Analisis Bahu Jalan</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-arrow-left fa-2x text-primary mb-2"></i>
                                <h6>Bahu Kiri</h6>
                                <p class="mb-0">{{ $shoulder_analysis['left_shoulder_exists'] }}/{{ $statistics['total_segments'] }} segmen</p>
                                <small class="text-muted">Rata-rata: {{ number_format($shoulder_analysis['avg_left_shoulder'], 2) }}m</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-arrow-right fa-2x text-info mb-2"></i>
                                <h6>Bahu Kanan</h6>
                                <p class="mb-0">{{ $shoulder_analysis['right_shoulder_exists'] }}/{{ $statistics['total_segments'] }} segmen</p>
                                <small class="text-muted">Rata-rata: {{ number_format($shoulder_analysis['avg_right_shoulder'], 2) }}m</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Status Bahu Jalan</h6>
                            @php
                                $both_shoulders = $inventories->where('should_with_L', '>', 0)->where('should_with_R', '>', 0)->count();
                                $only_left = $inventories->where('should_with_L', '>', 0)->where('should_with_R', '=', 0)->count();
                                $only_right = $inventories->where('should_with_L', '=', 0)->where('should_with_R', '>', 0)->count();
                                $no_shoulder = $inventories->where('should_with_L', '=', 0)->where('should_with_R', '=', 0)->count();
                            @endphp
                            <div class="progress-label mb-2">
                                Kedua Sisi: <span class="float-right">{{ $both_shoulders }}/{{ $statistics['total_segments'] }}</span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" style="width: {{ ($both_shoulders/$statistics['total_segments'])*100 }}%"></div>
                            </div>
                            
                            <div class="progress-label mb-2">
                                Hanya Kiri: <span class="float-right">{{ $only_left }}/{{ $statistics['total_segments'] }}</span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-warning" style="width: {{ ($only_left/$statistics['total_segments'])*100 }}%"></div>
                            </div>

                            <div class="progress-label mb-2">
                                Tidak Ada: <span class="float-right">{{ $no_shoulder }}/{{ $statistics['total_segments'] }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" style="width: {{ ($no_shoulder/$statistics['total_segments'])*100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Problematic Segments Detail --}}
            @if($statistics['impassable_count'] > 0)
            <div class="card" id="problematic-segments">
                <div class="card-header bg-danger text-white">
                    <h4><i class="fas fa-exclamation-triangle"></i> Segmen Bermasalah</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($problematic_segments as $segment)
                        <div class="col-md-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h6 class="text-danger">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Chainage: {{ number_format($segment->chainage_from, 3) }} - {{ number_format($segment->chainage_to, 3) }}m
                                    </h6>
                                    <p class="mb-1"><strong>Alasan:</strong> {{ $segment->impassableReason->code_description_ind ?? '-' }}</p>
                                    <p class="mb-1"><strong>Panjang:</strong> {{ number_format($segment->chainage_to - $segment->chainage_from, 3) }}m</p>
                                    <p class="mb-0"><strong>Terrain:</strong> {{ $segment->terrainType->code_description_ind ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Reasons Summary --}}
                    <div class="mt-4">
                        <h6>Distribusi Alasan Masalah:</h6>
                        <div class="row">
                            @foreach($impassable_reasons as $reason => $count)
                            @php
                                $reasonType = $problematic_segments->where('impassable_reason', $reason)->first()?->impassableReason;
                                $percentage = ($count / $statistics['impassable_count']) * 100;
                            @endphp
                            <div class="col-md-6 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $reasonType->code_description_ind ?? $reason }}</span>
                                    <span>{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
// Pastikan script berjalan setelah DOM ready dan jQuery tersedia
document.addEventListener('DOMContentLoaded', function() {
    // Jika menggunakan jQuery
    if (typeof $ !== 'undefined') {
        // Table initialization
        $('#inventoryTable').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100]
        });

        // View toggle dengan jQuery
        $('#viewCards').on('click', function(e) {
            e.preventDefault();
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $('#viewTable').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#cardView').show();
            $('#tableView').hide();
        });

        $('#viewTable').on('click', function(e) {
            e.preventDefault();
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $('#viewCards').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#tableView').show();
            $('#cardView').hide();
        });
    } else {
        // Fallback dengan vanilla JavaScript jika jQuery tidak tersedia
        const viewCardsBtn = document.getElementById('viewCards');
        const viewTableBtn = document.getElementById('viewTable');
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');

        if (viewCardsBtn && viewTableBtn && cardView && tableView) {
            viewCardsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Update button states
                viewCardsBtn.classList.remove('btn-outline-primary');
                viewCardsBtn.classList.add('btn-primary');
                viewTableBtn.classList.remove('btn-primary');
                viewTableBtn.classList.add('btn-outline-primary');
                
                // Show/hide views
                cardView.style.display = 'block';
                tableView.style.display = 'none';
            });

            viewTableBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Update button states
                viewTableBtn.classList.remove('btn-outline-primary');
                viewTableBtn.classList.add('btn-primary');
                viewCardsBtn.classList.remove('btn-primary');
                viewCardsBtn.classList.add('btn-outline-primary');
                
                // Show/hide views
                tableView.style.display = 'block';
                cardView.style.display = 'none';
            });
        }
    }
});
</script>
@endpush