<?php ( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') ); ?>
<?php ( $profile_url = View::getSection('profile_url') ?? config('adminlte.profile_url', 'logout') ); ?>

<?php if(config('adminlte.usermenu_profile_url', false)): ?>
    <?php ( $profile_url = Auth::user()->adminlte_profile_url() ); ?>
<?php endif; ?>

<?php if(config('adminlte.use_route_url', false)): ?>
    <?php ( $profile_url = $profile_url ? route($profile_url) : '' ); ?>
    <?php ( $logout_url = $logout_url ? route($logout_url) : '' ); ?>
<?php else: ?>
    <?php ( $profile_url = $profile_url ? url($profile_url) : '' ); ?>
    <?php ( $logout_url = $logout_url ? url($logout_url) : '' ); ?>
<?php endif; ?>
<li class="nav-item dropdown user-menu">

    
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        <?php if(config('adminlte.usermenu_image')): ?>
            <img src="<?php echo e(Auth::user()->adminlte_image()); ?>"
                 class="user-image img-circle elevation-2"
                 alt="<?php echo e(Auth::user()->name); ?>">
        <?php endif; ?>
        <span <?php if(config('adminlte.usermenu_image')): ?> class="d-none d-md-inline" <?php endif; ?>>
            <?php echo e(Auth::user()->name); ?>

        </span>
    </a>

    
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        
        <?php if(!View::hasSection('usermenu_header') && config('adminlte.usermenu_header')): ?>
            <li class="user-header <?php echo e(config('adminlte.usermenu_header_class', 'bg-primary')); ?>

                <?php if(!config('adminlte.usermenu_image')): ?> h-auto <?php endif; ?>">
                <?php if(config('adminlte.usermenu_image')): ?>
                    <img src="<?php echo e(Auth::user()->adminlte_image()); ?>"
                         class="img-circle elevation-2"
                         alt="<?php echo e(Auth::user()->name); ?>">
                <?php endif; ?>
                <p class="<?php if(!config('adminlte.usermenu_image')): ?> mt-0 <?php endif; ?>">
                    <?php echo e(Auth::user()->name); ?>

                    <?php if(config('adminlte.usermenu_desc')): ?>
                        <small><?php echo e(Auth::user()->adminlte_desc()); ?></small>
                    <?php endif; ?>
                </p>
            </li>
        <?php else: ?>
            <?php echo $__env->yieldContent('usermenu_header'); ?>
        <?php endif; ?>
        <form action="<?php echo e(route('beralih.peran')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <br>
            <center>
            <div class="input-group mb-3" style="margin:0px 10px 0px 10px;">
                <div class="input-group-prepend">
                    <span class="input-group-text">Hak Akses</span>
                </div>
                <select name="role" id="role"  onchange="this.form.submit()" class="form-control col-7">
                <?php ($count=0); ?>
                <?php if((Auth::user())): ?>
                <?php $__currentLoopData = Auth::user()->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role->name); ?>" <?php if(Auth::user()->role_aktif == $role->name): ?> selected <?php endif; ?>><?php echo e($role->name); ?></option>							
                        <?php ($count++); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                </select>
            </div>
            </center>
        </form>
        
        <?php echo $__env->renderEach('adminlte::partials.navbar.dropdown-item', $adminlte->menu("navbar-user"), 'item'); ?>

        
        <?php if (! empty(trim($__env->yieldContent('usermenu_body')))): ?>
            <li class="user-body">
                <?php echo $__env->yieldContent('usermenu_body'); ?>
            </li>
        <?php endif; ?>

        
        <li class="user-footer">
            <?php if($profile_url): ?>
                <a href="<?php echo e($profile_url); ?>" class="btn btn-default btn-flat">
                    <i class="fa fa-fw fa-user text-lightblue"></i>
                    <?php echo e(__('adminlte::menu.profile')); ?>

                </a>
            <?php endif; ?>
			<a href="<?php echo e(route('logout.perform')); ?>" class="btn btn-default btn-flat float-right <?php if(!$profile_url): ?> btn-block <?php endif; ?>">Keluar</a>
			<!--<a class="btn btn-default btn-flat float-right <?php if(!$profile_url): ?> btn-block <?php endif; ?>"
               href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-fw fa-power-off text-red"></i>
                <?php echo e(__('adminlte::adminlte.log_out')); ?>

            </a>
            <form id="logout-form" action="<?php echo e($logout_url); ?>" method="POST" style="display: none;">
                <?php if(config('adminlte.logout_method')): ?>
                    <?php echo e(method_field(config('adminlte.logout_method'))); ?>

                <?php endif; ?>
                <?php echo e(csrf_field()); ?>

            </form>-->
        </li>

    </ul>

</li>
<?php /**PATH D:\KULIAH\web-yud\resources\views/vendor/adminlte/partials/navbar/menu-item-dropdown-user-menu.blade.php ENDPATH**/ ?>