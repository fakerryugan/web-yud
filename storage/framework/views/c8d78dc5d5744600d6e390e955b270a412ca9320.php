

<?php $__env->startSection('title', 'Dasbor Persuratan Digital'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row align-item-center">
    <h1 class="m-1 text-dark">Dashboard</h1>
    <div class="col-md-12">
                        <form action="#" method="GET" class="float-md-right">
                            <div class="input-group"> 
                                <input type="text" class="form-control" placeholder="Search" name="keyword">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    </div>
                    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('assets/css/dashboard.css')); ?>">
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

                <div class="table-head d-none d-lg-grid">
                  <div>Dokumen</div>
                  <div>Tanggal Unggah</div>
                  <div>Status</div>
                  <div></div>
                </div>

                <div class="list-rows">
                  <div class="row-item d-grid" data-status="selesai">
                    <div class="doc-col d-flex align-items-center">
                      <div class="avatar-circle">
                        <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                      </div>
                      <div class="doc-title">Dokumen semua korupsi.pdf</div>
                    </div>
                    <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
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
                        </div>
                      </td>
                  </div>

                  <div class="row-item d-grid" data-status="ditolak">
                    <div class="doc-col d-flex align-items-center">
                      <div class="avatar-circle">
                        <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                      </div>
                      <div class="doc-title">Dokumen penjualan pulau.pdf</div>
                    </div>
                    <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
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
                        </div>
                      </td>
                  </div>

                  <div class="row-item d-grid" data-status="selesai">
                    <div class="doc-col d-flex align-items-center">
                      <div class="avatar-circle">
                        <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                      </div>
                      <div class="doc-title">Pasar gelap bebas akses.pdf</div>
                    </div>
                    <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
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
                        </div>
                      </td>
                  </div>

                  <div class="row-item d-grid" data-status="tertunda">
                    <div class="doc-col d-flex align-items-center">
                      <div class="avatar-circle">
                        <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                      </div>
                      <div class="doc-title">Dokumen semua korupsi.pdf</div>
                    </div>
                    <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
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
                        </div>
                      </td>
                  </div>

                  <div class="row-item d-grid" data-status="selesai">
                    <div class="doc-col d-flex align-items-center">
                      <div class="avatar-circle">
                        <img src="<?php echo e(asset('assets/img/userdokumen.png')); ?>" alt="user" class="avatar-img">
                      </div>
                      <div class="doc-title">Strategi Korupsi Projek Jalan Layang.pdf</div>
                    </div>
                    <div class="date-col">Des, 07-12-2025 <span class="time">18.00</span></div>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
	 <!--some css
    <link rel="stylesheet" href="/assets/css/admin_custom.css">-->
<?php $__env->stopSection(); ?>
<?php $__env->startPush('js'); ?>
<script>
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\PBL\Web\web-yud\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>