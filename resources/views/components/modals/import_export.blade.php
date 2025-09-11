<div class="modal fade" id="modalImportExport" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ $title ?? 'Import / Export Data' }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        {{-- Import Form --}}
        @isset($importRoute)
        <form action="{{ $importRoute }}" method="POST" enctype="multipart/form-data" class="mb-3">
          @csrf
          <div class="form-group">
            <label for="fileImport">Pilih File Excel (.xlsx / .xls)</label>
            <input type="file" name="file" class="form-control" id="fileImport" required>
          </div>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-file-import"></i> Import
          </button>
        </form>
        @endisset

        {{-- Export Button --}}
        @isset($exportRoute)
        <a href="{{ $exportRoute }}" class="btn btn-info">
          <i class="fas fa-file-export"></i> Export
        </a>
        @endisset
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
