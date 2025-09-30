<!--
=========================================================
* Argon Dashboard - v1.2.0
=========================================================
* Product Page: https://www.creative-tim.com/product/argon-dashboard

* Copyright  Creative Tim (http://www.creative-tim.com)
* Coded by www.creative-tim.com
=========================================================
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->

<?php ( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') ); ?>
<?php ( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') ); ?>
<?php ( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') ); ?>

<?php if(config('adminlte.use_route_url', false)): ?>
    <?php ( $login_url = $login_url ? route($login_url) : '' ); ?>
    <?php ( $register_url = $register_url ? route($register_url) : '' ); ?>
    <?php ( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' ); ?>
<?php else: ?>
    <?php ( $login_url = $login_url ? url($login_url) : '' ); ?>
    <?php ( $register_url = $register_url ? url($register_url) : '' ); ?>
    <?php ( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' ); ?>
<?php endif; ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
    <meta name="author" content="Creative Tim">
    <title>Login | <?php echo e(config('app.name')); ?></title>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(url('favicon.png')); ?>" type="image/png">
    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo e(url('argon')); ?>/assets/vendor/nucleo/css/nucleo.css" type="text/css">
    <link rel="stylesheet" href="<?php echo e(url('argon')); ?>/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" type="text/css">
    <!-- Iconify -->
    <script src="https://code.iconify.design/1/1.0.7/iconify.min.js"></script>
    <!-- Argon CSS -->
    <link rel="stylesheet" href="<?php echo e(url('argon')); ?>/assets/css/argon.css?v=1.2.0" type="text/css">
    <script src="<?php echo e(url('js/util.js')); ?>"></script>
    <!-- Custom CSS -->
    <link href="<?php echo e(asset('/assets/css/halamanAwal.css')); ?>" rel="stylesheet">


</head>

<body class="bg-default">
    <!-- Main content -->
    <div class="main-content halaman_awal login_page">
        <!-- Header -->
        <div class="header bg-gradient-primary py-7">
            <div class="container">
                <div class="header-body text-center mb-5">
                    <div class="row justify-content-center">
                        <div class="col-xl-5 col-lg-6 col-md-8 px-2">
                            <img src="<?php echo e(asset(config('adminlte.logo_img'))); ?>" height="50">                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator separator-bottom separator-skew zindex-100">
                <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1" xmlns="http://www.w3.org/2000/svg">
                    <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
        </div>
        <!-- Page content -->
        <div class="container mt--8 pb-7">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-7">
                    <div class="card bg-secondary mt-4 border-0 mb-0">
                        <div class="card-body">
							<?php if(config('services.oauth_server.sso_enable')): ?>
								
								<div class="text-center mb-1">
									<p>Silahkan klik login untuk masuk ke aplikasi</p>
								</div>
								
								<form method="post">
									<div class="text-center">
										<a href="<?php echo e(url('/oauth/login')); ?>" type="submit" class="btn-login btn btn-primary w-100 my-4-5 rounded-sm">Login</a>
										
									</div>
								</form>
							<?php else: ?>
								<form action="<?php echo e($login_url); ?>" method="post">
									<?php echo csrf_field(); ?>

									
									<div class="input-group mb-3">
										<input type="username" name="username" class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
											value="<?php echo e(old('username')); ?>" placeholder="username" autofocus>

										<div class="input-group-append">
											<div class="input-group-text">
												<span class="fas fa-user <?php echo e(config('adminlte.classes_auth_icon', '')); ?>"></span>
											</div>
										</div>

										
									</div>

									
									<div class="input-group mb-3">
										<input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
											placeholder="<?php echo e(__('adminlte::adminlte.password')); ?>">

										<div class="input-group-append">
											<div class="input-group-text">
												<span class="fas fa-lock <?php echo e(config('adminlte.classes_auth_icon', '')); ?>"></span>
											</div>
										</div>

										<?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
											<span class="invalid-feedback" role="alert">
												<strong><?php echo e($message); ?></strong>
											</span>
										<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
									</div>

									
									<div class="row">
										<div class="col-7">
											<div class="icheck-primary" title="<?php echo e(__('adminlte::adminlte.remember_me_hint')); ?>">
												
											</div>
										</div>

										<div class="col-5">
											<button type=submit class="btn btn-block btn-primary">
												
												Masuk
											</button>
										</div>
									</div>

								</form>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Argon Scripts -->
    <!-- Core -->
    <script src="<?php echo e(url('argon')); ?>/assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo e(url('argon')); ?>/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(url('argon')); ?>/assets/vendor/js-cookie/js.cookie.js"></script>
    <script src="<?php echo e(url('argon')); ?>/assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>
    <script src="<?php echo e(url('argon')); ?>/assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js"></script>
    <!-- Argon JS -->
    <script src="<?php echo e(url('argon')); ?>/assets/js/argon.js?v=1.2.0"></script>
    
</body>

</html><?php /**PATH C:\Users\faker\Documents\py\laravel-master\resources\views/autentikasi/login.blade.php ENDPATH**/ ?>