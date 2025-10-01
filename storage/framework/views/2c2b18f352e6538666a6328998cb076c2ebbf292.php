
<?php $__env->startSection('title', 'Show role'); ?>
<?php $__env->startSection('content_header'); ?>
    <h1 class="m-0 text-dark">Show role</h1>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
	<div class="bg-light p-4 rounded">
        <h1><?php echo e(ucfirst($role->name)); ?> Role</h1>
        <div class="lead">
            
        </div>
        
        <div class="container mt-4">

            <h3>Assigned permissions</h3>

            <table class="table table-striped">
                <thead>
                    <th scope="col" width="20%">Name</th>
                    <th scope="col" width="1%">Guard</th> 
                </thead>

                <?php $__currentLoopData = $rolePermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($permission->name); ?></td>
                        <td><?php echo e($permission->guard_name); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        </div>

    </div>
    <div class="mt-4">
        <a href="<?php echo e(route('roles.edit', $role->id)); ?>" class="btn btn-info">Edit</a>
        <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-default">Back</a>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\PBL\Web\web-yud\resources\views/roles/show.blade.php ENDPATH**/ ?>