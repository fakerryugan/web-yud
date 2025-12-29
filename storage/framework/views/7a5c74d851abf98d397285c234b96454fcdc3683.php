

<?php $__env->startSection('title', 'Dasbor Persuratan Digital'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex justify-content-between align-items-center mx-auto" style="max-width: 1180px;">
  <h1 class="m-0 text-dark">Dashboard</h1>
  <div class="d-flex align-items-center">
    <!-- Upload Button -->
    <input type="file" id="fileUploadInput" style="display: none;">
    <a href="#" onclick="document.getElementById('fileUploadInput').click(); return false;"
      class="btn btn-primary d-flex align-items-center justify-content-center text-white text-decoration-none"
      style="width: 196px; height: 40px; border-radius: 5px; background-color: #007bff; border: none; font-size: 14px;">
      <i class="fas fa-file-upload mr-2"></i> Upload Dokumen
    </a>

    <!-- Filter Dropdown -->
    <div class="d-flex align-items-center" style="margin-left: 12px; margin-right: 24px;">
      <select class="custom-select" id="recipientFilter"
        style="width: auto; height: 40px; border-radius: 5px; border: 1px solid #ced4da;">
        <option value="semua">Semua</option>
        <option value="kajur">Kajur</option>
        <option value="kaprodi">Kaprodi</option>
      </select>
    </div>

    <!-- Search Bar -->
    <form action="#" method="GET" class="m-0">
      <div class="input-group" style="width: 250px;">
        <input type="text" class="form-control" placeholder="Search" name="keyword"
          style="height: 40px; border-top-left-radius: 5px; border-bottom-left-radius: 5px;">
        <div class="input-group-append">
          <button class="btn btn-default bg-white border-left-0" type="submit"
            style="height: 40px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border: 1px solid #ced4da;">
            <i class="fas fa-search text-secondary"></i>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/css/dashboard.css')); ?>?v=<?php echo e(time()); ?>">
<style>
  .more-wrapper {
    position: relative;
  }

  .action-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 8px;
    width: 120px;
    z-index: 100;
    overflow: hidden;
    padding: 6px 0;
  }

  .action-dropdown.show {
    display: block;
  }

  .action-item {
    display: block;
    padding: 8px 16px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.1s;
  }

  .action-item:hover {
    background: #f8f9fa;
    text-decoration: none;
    color: #333;
  }

  .action-item.text-danger {
    color: #dc3545;
  }

  .action-item.text-danger:hover {
    background: #fff5f5;
    color: #dc3545;
  }
</style>
<div class="background-bottom"></div>
<main class="container-fluid content-wrap">
  <div class="dash-card position-relative mx-auto">
    <div class="row gx-4 py-4">
      <div class="col-lg-3 col-md-4">
        <div class="filters">
          <div class="filters-header">Semua Dokumen</div>

          <ul class="filter-list list-unstyled mt-3">
            <li class="filter-item active" data-filter="semua">
              <img src="<?php echo e(asset('assets/img/semua.png')); ?>" alt="Semua" class="icon-sm">
              <span>Semua Dokumen</span>
            </li>

            <li class="filter-item" data-filter="tertunda">
              <img src="<?php echo e(asset('assets/img/jam_icon.png')); ?>" alt="Tertunda" class="icon-sm">
              <span>Tertunda</span>
            </li>

            <li class="filter-item" data-filter="selesai">
              <img src="<?php echo e(asset('assets/img/centang_book.png')); ?>" alt="Selesai" class="icon-sm">
              <span>Selesai</span>
            </li>

            <li class="filter-item" data-filter="ditolak">
              <img src="<?php echo e(asset('assets/img/tolak_book.png')); ?>" alt="Ditolak" class="icon-sm">
              <span>Ditolak</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="col-lg-9 col-md-8">
        <div class="doc-list">

          <div class="table-head"
            style="display: grid; grid-template-columns: 1.5fr 160px 220px 100px 40px; padding: 8px 12px; color: #535353; font-weight: 600; border-bottom: 2px solid rgba(15, 23, 42, 0.04); margin-bottom: 8px;">
            <div>Dokumen</div>
            <div>Tanggal Unggah</div>
            <div>Ditujukan Kepada</div>
            <div class="text-center">Status</div>
            <div></div>
          </div>

          <div class="list-rows">
            <div class="row-item d-grid" data-status="selesai" data-recipient="kaprodi">
              <div class="doc-col d-flex align-items-center">
                <div class="avatar-circle">
                  <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                </div>
                <div class="doc-title">Dokumen semua korupsi.pdf</div>
              </div>
              <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
              <div class="recipient-col" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Dianni
                Yusuf, S.Kom., M.K....</div>
              <div class="status-col">
                <span class="status">
                  <img src="<?php echo e(asset('assets/img/done.png')); ?>" alt="ok" class="status-icon">
                </span>
              </div>
              <td class="text-end">
                <div class="more-wrapper">
                  <button class="btn btn-light btn-sm more-btn">
                    ...
                  </button>
                  <div class="action-dropdown">
                    <a href="#" class="action-item">Detail</a>
                    <a href="#" class="action-item">Unduh</a>
                    <a href="#" class="action-item text-danger">Hapus</a>
                  </div>
                </div>
              </td>
            </div>

            <div class="row-item d-grid" data-status="ditolak" data-recipient="kajur">
              <div class="doc-col d-flex align-items-center">
                <div class="avatar-circle">
                  <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                </div>
                <div class="doc-title">Dokumen penjualan pulau.pdf</div>
              </div>
              <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
              <div class="recipient-col" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Devit
                Suwardiyanto,S.Si....</div>
              <div class="status-col">
                <span class="status">
                  <img src="<?php echo e(asset('assets/img/cancel.png')); ?>" alt="ok" class="status-icon">
                </span>
              </div>
              <td class="text-end">
                <div class="more-wrapper">
                  <button class="btn btn-light btn-sm more-btn">
                    ...
                  </button>
                  <div class="action-dropdown">
                    <a href="#" class="action-item">Detail</a>
                    <a href="#" class="action-item">Unduh</a>
                    <a href="#" class="action-item text-danger">Hapus</a>
                  </div>
                </div>
              </td>
            </div>

            <div class="row-item d-grid" data-status="selesai" data-recipient="kajur">
              <div class="doc-col d-flex align-items-center">
                <div class="avatar-circle">
                  <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                </div>
                <div class="doc-title">Pasar gelap bebas akses.pdf</div>
              </div>
              <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
              <div class="recipient-col" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Devit
                Suwardiyanto,S.Si....</div>
              <div class="status-col">
                <span class="status">
                  <img src="<?php echo e(asset('assets/img/done.png')); ?>" alt="ok" class="status-icon">
                </span>
              </div>
              <td class="text-end">
                <div class="more-wrapper">
                  <button class="btn btn-light btn-sm more-btn">
                    ...
                  </button>
                  <div class="action-dropdown">
                    <a href="#" class="action-item">Detail</a>
                    <a href="#" class="action-item">Unduh</a>
                    <a href="#" class="action-item text-danger">Hapus</a>
                  </div>
                </div>
              </td>
            </div>

            <div class="row-item d-grid" data-status="tertunda" data-recipient="kaprodi">
              <div class="doc-col d-flex align-items-center">
                <div class="avatar-circle">
                  <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                </div>
                <div class="doc-title">Dokumen semua korupsi.pdf</div>
              </div>
              <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
              <div class="recipient-col" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Devit
                Suwardiyanto,S.Si....</div>
              <div class="status-col">
                <span class="status">
                  <img src="<?php echo e(asset('assets/img/proses.png')); ?>" alt="ok" class="status-icon">
                </span>
              </div>
              <td class="text-end">
                <div class="more-wrapper">
                  <button class="btn btn-light btn-sm more-btn">
                    ...
                  </button>
                  <div class="action-dropdown">
                    <a href="#" class="action-item">Detail</a>
                    <a href="#" class="action-item">Unduh</a>
                    <a href="#" class="action-item text-danger">Hapus</a>
                  </div>
                </div>
              </td>
            </div>

            <div class="row-item d-grid" data-status="selesai" data-recipient="kaprodi">
              <div class="doc-col d-flex align-items-center">
                <div class="avatar-circle">
                  <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                </div>
                <div class="doc-title">Strategi Korupsi Projek Jalan...</div>
              </div>
              <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
              <div class="recipient-col" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Dianni
                Yusuf, S.Kom., M.K....</div>
              <div class="status-col">
                <span class="status">
                  <img src="<?php echo e(asset('assets/img/done.png')); ?>" alt="ok" class="status-icon">
                </span>
              </div>
              <td class="text-end">
                <div class="more-wrapper">
                  <button class="btn btn-light btn-sm more-btn">
                    ...
                  </button>
                  <div class="action-dropdown">
                    <a href="#" class="action-item">Detail</a>
                    <a href="#" class="action-item">Unduh</a>
                    <a href="#" class="action-item text-danger">Hapus</a>
                  </div>
                </div>
              </td>
            </div>
          </div>

          <div class="backup-wrap">
            <button class="btn backup-btn">
              <img src="<?php echo e(asset('assets/img/backup.png')); ?>" alt="backup" class="icon-img-sm">
              Backup All Data
            </button>
          </div>

        </div>
      </div>

    </div>
  </div>

  <!-- Document View Container -->
  <div id="document-view" class="document-view" style="display: none; margin-top: -560px;">
    <div class="doc-view-header">
      <h2 class="doc-view-title">Tanda Tangan</h2>
    </div>

    <div class="doc-card">
      <div class="doc-action-bar">
        <div class="doc-filename" id="displayFilename">Ini adalah Dokumen Rahasia.pdf</div>
        <div class="doc-buttons">
          <button class="btn-add-ttd">
            + Add TTD
          </button>
          <button class="btn-back-custom" id="btnBackToDashboard">
            Kembali &rarr;
          </button>
        </div>
      </div>

      <div class="doc-preview-wrapper">
        <div class="doc-preview-content">
          <div id="pdfContainer"
            style="width: 100%; height: 100%; overflow: auto; display: none; -ms-overflow-style: none; scrollbar-width: none;">
            <style>
              #pdfContainer::-webkit-scrollbar {
                display: none;
              }
            </style>
            <canvas id="the-canvas"></canvas>
          </div>
          <div id="pdfPlaceholder" class="doc-preview-placeholder">
            Preview dokumen akan muncul di sini
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add TTD Modal -->
  <div id="ttdModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
      <div class="modal-header">
        <h3 class="modal-title">Tambahkan Tanda Tangan</h3>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <select id="ttdRecipient" class="form-control-modal">
            <option value="" disabled selected>Ditujukan Kepada</option>
            <option value="Kajur">Kajur</option>
            <option value="Kaprodi">Kaprodi</option>
          </select>
        </div>
        <div class="form-group">
          <textarea id="ttdPurpose" class="form-control-modal textarea-modal" placeholder="Tujuan Surat..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button id="btnCancelTtd" class="btn-modal btn-cancel">Batal</button>
        <button id="btnConfirmTtd" class="btn-modal btn-confirm">Ok</button>
      </div>
    </div>
  </div>
  <?php $__env->stopSection(); ?>

  <?php $__env->startSection('css'); ?>
  <!--some css
    <link rel="stylesheet" href="/assets/css/admin_custom.css">-->
  <?php $__env->stopSection(); ?>
  <?php $__env->startPush('js'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
      // Worker PDF.js
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

      document.addEventListener('DOMContentLoaded', function () {
        // Elemen-elemen
        const recipientFilter = document.getElementById('recipientFilter');
        const filterItems = document.querySelectorAll('.filter-item');
        const rows = document.querySelectorAll('.list-rows .row-item');

        // Status State
        let currentStatus = 'semua';
        let currentRecipient = 'semua';

        // Logika Filter
        function filterRows() {
          rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowRecipient = row.getAttribute('data-recipient');

            const statusMatch = (currentStatus === 'semua') || (rowStatus === currentStatus);
            const recipientMatch = (currentRecipient === 'semua') || (rowRecipient === currentRecipient);

            if (statusMatch && recipientMatch) {
              row.style.display = 'grid'; // Pertahankan layout grid
            } else {
              row.style.display = 'none';
            }
          });
        }

        // Event: Dropdown Header
        if (recipientFilter) {
          recipientFilter.addEventListener('change', function () {
            currentRecipient = this.value;
            filterRows();
          });
        }

        // Event: Filter Sidebar
        filterItems.forEach(item => {
          item.addEventListener('click', function () {
            // Status Aktif
            filterItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // Set filter
            currentStatus = this.getAttribute('data-filter');
            filterRows();
          });
        });

        // Event: Toggle Menu Aksi (Klik Global)
        document.addEventListener('click', function (e) {
          const moreBtn = e.target.closest('.more-btn');
          const dropdown = e.target.closest('.action-dropdown');

          // Tutup semua dropdown kecuali tombol yang diklik
          document.querySelectorAll('.action-dropdown').forEach(d => {
            if (moreBtn && moreBtn.nextElementSibling === d) return;
            d.classList.remove('show');
          });

          // Toggle dropdown spesifik
          if (moreBtn) {
            e.stopPropagation();
            const currentDropdown = moreBtn.nextElementSibling;
            if (currentDropdown) currentDropdown.classList.toggle('show');
          }
        });

        // --- Logika Upload & View Dokumen ---
        const fileUploadInput = document.getElementById('fileUploadInput');
        const dashCard = document.querySelector('.dash-card');
        const contentHeader = document.querySelector('.d-flex.justify-content-between.align-items-center');

        const docView = document.getElementById('document-view');
        const displayFilename = document.getElementById('displayFilename');
        const btnBackToDashboard = document.getElementById('btnBackToDashboard');
        const pdfPreviewFrame = document.getElementById('pdfPreviewFrame');
        const pdfPlaceholder = document.getElementById('pdfPlaceholder');

        if (fileUploadInput) {
          fileUploadInput.addEventListener('change', function (e) {
            if (this.files && this.files.length > 0) {
              const file = this.files[0];

              // 1. Update nama file
              if (displayFilename) displayFilename.textContent = file.name;

              // 2. Render Preview PDF dengan PDF.js
              if (file.type === 'application/pdf') {
                const fileURL = URL.createObjectURL(file);
                const loadingTask = pdfjsLib.getDocument(fileURL);
                const container = document.getElementById('pdfContainer');
                const canvas = document.getElementById('the-canvas');

                if (container) container.style.display = 'block';
                if (pdfPlaceholder) pdfPlaceholder.style.display = 'none';

                loadingTask.promise.then(function (pdf) {
                  // Bersihkan konten yang ada
                  container.innerHTML = '';

                  // Loop semua halaman
                  for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    pdf.getPage(pageNum).then(function (page) {
                      const scale = 1.5;
                      const viewport = page.getViewport({ scale: scale });

                      const canvas = document.createElement('canvas');
                      canvas.id = 'the-canvas-' + pageNum;
                      canvas.style.display = 'block'; // Tampilkan block agar menumpuk vertikal
                      canvas.style.margin = '0 auto'; // Tengahkan horizontal

                      const context = canvas.getContext('2d');
                      canvas.height = viewport.height;
                      canvas.width = viewport.width;

                      container.appendChild(canvas);

                      const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                      };
                      page.render(renderContext);
                    });
                  }
                });
              } else {
                // Fallback untuk non-PDF
                const container = document.getElementById('pdfContainer');
                if (container) container.style.display = 'none';
                if (pdfPlaceholder) {
                  pdfPlaceholder.textContent = 'Preview tidak tersedia untuk tipe file ini.';
                  pdfPlaceholder.style.display = 'block';
                }
              }

              // 3. Ganti View
              if (dashCard) dashCard.style.display = 'none';
              // Sembunyikan header dashboard utama
              if (contentHeader) contentHeader.parentElement.style.display = 'none';

              if (docView) docView.style.display = 'block';
            }
          });
        }

        if (btnBackToDashboard) {
          btnBackToDashboard.addEventListener('click', function () {
            // Kembali ke dashboard
            if (docView) docView.style.display = 'none';
            if (dashCard) dashCard.style.display = 'block';
            if (contentHeader) contentHeader.parentElement.style.display = ''; // Restore header

            // Bersihkan canvas/container
            const container = document.getElementById('pdfContainer');
            const canvas = document.getElementById('the-canvas');
            if (container) container.style.display = 'none';
            if (canvas) {
              const context = canvas.getContext('2d');
              context.clearRect(0, 0, canvas.width, canvas.height);
            }

            // Bersihkan input file agar bisa memilih file yang sama lagi
            if (fileUploadInput) fileUploadInput.value = '';
          });
        }

        // Logika Modal
        const btnAddTtd = document.querySelector('.btn-add-ttd');
        const ttdModal = document.getElementById('ttdModal');
        const btnCancelTtd = document.getElementById('btnCancelTtd');
        const btnConfirmTtd = document.getElementById('btnConfirmTtd');

        if (btnAddTtd && ttdModal) {
          btnAddTtd.addEventListener('click', function () {
            ttdModal.style.display = 'flex';
          });
        }

        if (btnCancelTtd && ttdModal) {
          btnCancelTtd.addEventListener('click', function () {
            ttdModal.style.display = 'none';
          });
        }

        if (btnConfirmTtd && ttdModal) {
          btnConfirmTtd.addEventListener('click', function () {
            // Tentukan layout berdasarkan pilihan user
            const recipient = document.getElementById('ttdRecipient').value;
            const purpose = document.getElementById('ttdPurpose').value;

            alert('Tanda tangan akan ditambahkan untuk: ' + recipient + '\nTujuan: ' + purpose);
            ttdModal.style.display = 'none';
          });
        }

        // Tutup modal saat klik di luar area
        window.addEventListener('click', function (event) {
          if (event.target === ttdModal) {
            ttdModal.style.display = 'none';
          }
        });
      });
    </script>
  <?php $__env->stopPush(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\KULIAH\web-yud\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>