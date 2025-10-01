
<?php $__env->startSection('title', 'List Menu'); ?>
<?php $__env->startSection('plugins.Select2', true); ?>
<?php $__env->startSection('content_header'); ?>
    <h1 class="m-0 text-dark"></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <link rel="stylesheet" href="/assets/css/jquery.nestable.min.css">
    <link rel="stylesheet" href="/assets/css/menu-manager.css">
    <link rel="stylesheet" href="/assets/css/bootstrap-select.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
				<h1>Menus</h1>
				<div class="lead">
					Kelola Menu
					<a href="<?php echo e(route('menus.create')); ?>" class="btn btn-primary btn-sm float-right">Tambah menu</a>
				</div>		
				<div class="mt-2">
					<?php echo $__env->make('layouts.partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				</div>                

                <hr />
                <div class="dd" id="nestable">
				<?php
                    $html_menu = BCL_menuTree();
                    echo (empty($html_menu)) ? '<ol class="dd-lisat"></ol>' : $html_menu;                    
                ?>
                </div>
                
                <hr />
                <form action="" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="nestable-output" name="menu">
                    <button type="submit" class="btn btn-primary">Save Menu</button>
                </form>
			</div>
        </div>
    </div>

    
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
    <script src="/assets/js/jquery.nestable.min.js"></script>
    <script src="/assets/js/menu-manager.js"></script>
    <script src="/assets/js/bootstrap-select.min.js"></script>
    <script>
         $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            $('#tt').selectpicker();
         });
        function setIcon(nama){
            $("#icon").val(nama);
            $('#modalIcon').modal('toggle');
        }
        
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\PBL\Web\web-yud\resources\views/menu/index.blade.php ENDPATH**/ ?>