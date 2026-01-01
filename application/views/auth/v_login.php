<div class="container">

    <!-- Floating Shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="glass-card my-5">
                <div class="text-center">
                    <h1 class="h4 mb-4">Welcome Back!</h1>
                </div>
                <div class="animated infinite flash">
                    <?= $this->session->flashdata('message'); ?>
                </div>
                <form action="<?= base_url('auth'); ?>" method="post" class="user">
                    <div class="form-group">
                        <input type="text" class="form-control form-control-user" id="username" name="username" placeholder="Enter Username..." value="<?= set_value('username'); ?>">
                        <?= form_error('username', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password">
                        <?= form_error('password', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                    <button type="submit" class="btn btn-glass btn-block">
                        Login
                    </button>
                </form>
                <hr style="border-top: 1px solid rgba(255,255,255,0.2);">
                <!-- 
                <div class="text-center">
                    <a class="small text-white" href="<?= base_url('auth/forgotPassword'); ?>">Forgot Password?</a>
                </div>
                <div class="text-center">
                    <a class="small text-white" href="<?= base_url() ?>auth/registration">Create an Account!</a>
                </div> 
                -->
            </div>
        </div>
    </div>
</div>