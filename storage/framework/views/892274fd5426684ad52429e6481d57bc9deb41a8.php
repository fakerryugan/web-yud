

<?php $layoutHelper = app('JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper'); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <?php echo $__env->yieldPushContent('css'); ?>
    <?php echo $__env->yieldContent('css'); ?>
	<link rel="stylesheet" href="/assets/css/admin_custom.css">
<?php $__env->stopSection(); ?>




<?php $__env->startSection('classes_body', $layoutHelper->makeBodyClasses()); ?>

<?php $__env->startSection('body_data', $layoutHelper->makeBodyData()); ?>

<?php $__env->startSection('body'); ?>
    <div class="wrapper">

        
        <?php if($layoutHelper->isLayoutTopnavEnabled()): ?>
            <?php echo $__env->make('adminlte::partials.navbar.navbar-layout-topnav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('adminlte::partials.navbar.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        
        <?php if(!$layoutHelper->isLayoutTopnavEnabled()): ?>
            <?php echo $__env->make('adminlte::partials.sidebar.left-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        
        <?php if(empty($iFrameEnabled)): ?>
            <?php echo $__env->make('adminlte::partials.cwrapper.cwrapper-default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('adminlte::partials.cwrapper.cwrapper-iframe', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        
        <?php if (! empty(trim($__env->yieldContent('footer')))): ?>
            <?php echo $__env->make('adminlte::partials.footer.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        
        <?php if(config('adminlte.right_sidebar')): ?>
            <?php echo $__env->make('adminlte::partials.sidebar.right-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
		
        <?php
            echo base64_decode('PGZvb3RlciBjbGFzcz0ibWFpbi1mb290ZXIiPgogICAgICAgIDxkaXYgY2xhc3M9ImZsb2F0LXJpZ2h0IGQtbm9uZSBkLXNtLWJsb2NrIj4KICAgICAgICA8Yj5WZXJzaW9uPC9iPiAxLjAuMAogICAgICAgIDwvZGl2PgogICAgICAgIDxzdHJvbmc+Q29weXJpZ2h0IMKpIDIwMjMtMjAyNCA8YSBocmVmPSJodHRwczovL3d3dy55b3V0dWJlLmNvbS9AYmFuZ2Nob2xpayI+QmFuZ0Nob2xpazwvYT4uPC9zdHJvbmc+IEFsbCByaWdodHMgcmVzZXJ2ZWQuCiAgICAgICAgPC9mb290ZXI+CjwvZGl2Pgo8eC1ub3RpZmljYXRpb24tY29tcG9uZW50Lz4=');
        ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('adminlte_js'); ?>
    <?php echo $__env->yieldPushContent('js'); ?>
    <?php echo $__env->yieldContent('js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\PBL\Web\web-yud\resources\views/vendor/adminlte/page.blade.php ENDPATH**/ ?>