

<?php $__env->startSection('title', 'Dasbor Persuratan Digital'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row align-item-center">
    <h1 class="m-1 text-dark">Dashboard</h1>
    <div class="col-md-10">
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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
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
    $(document).ready(function() {
        $('#btnOpenSaltB').click(function() {
            Swal.fire(
                'Good job!',
                'You clicked the button!',
                'success'
            );
        });

        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        $('#btnOpenSaltC').click(function() {
            Toast.fire({
                icon: 'success',
                title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
            });
        });
    })
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\PBL\Web\web-yud\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>