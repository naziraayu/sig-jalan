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
        <form action="{{ $importRoute }}" method="POST" enctype="multipart/form-data" class="mb-3" id="formImport">
          @csrf
          <div class="form-group">
            <label for="fileImport">Pilih File Excel (.xlsx / .xls)</label>
            <input type="file" name="file" class="form-control" id="fileImport" required>
          </div>
          <button type="submit" class="btn btn-success" id="btnImport">
            <i class="fas fa-file-import"></i> Import
          </button>

          {{-- Spinner hidden default --}}
          <div id="loadingSpinner" class="mt-3 text-center d-none">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
              <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Sedang memproses import, mohon tunggu...</p>
          </div>
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

@push('script')
<script>
  $(document).ready(function () {
    $("#formImport").on("submit", function (e) {
      e.preventDefault(); // cegah reload form

      let form = this;
      let formData = new FormData(form);

      // tombol import -> loading
      $("#btnImport").prop("disabled", true)
        .html('<i class="fas fa-spinner fa-spin"></i> Importing...');

      // spinner tampil
      $("#loadingSpinner").removeClass("d-none");

      $.ajax({
        url: $(form).attr("action"),
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
          // kalau sukses kasih notifikasi
          alert("✅ Import berhasil!");

          // reload halaman biar data baru muncul
          location.reload();
        },
        error: function (xhr) {
          alert("❌ Import gagal: " + xhr.responseText);
        },
        complete: function () {
          // reset tombol + spinner (kalau ga reload)
          $("#btnImport").prop("disabled", false)
            .html('<i class="fas fa-file-import"></i> Import');
          $("#loadingSpinner").addClass("d-none");
        }
      });
    });
  });
</script>
@endpush
