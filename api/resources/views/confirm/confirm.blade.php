<?php $this->extends('layouts/app.blade.php'); ?>

<?php $this->section('content'); ?>
<section class="background-radial-gradient overflow-hidden full-screen">
    <div class="container px-4 py-5 px-md-5 my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card border border-light">
                    <div class="card-body px-4 py-5 px-md-5 text-center">
                        <h2 class="mb-3 font-weight-bold">Welcome!</h2>
                        <img src="https://img.icons8.com/clouds/100/000000/handshake.png" alt="image" class="my-2" style="width: 100px">
                        <p>We're excited to have you get started. First, you need to confirm your account.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/confirm/confirm.js'); ?>"></script>
<?php $this->endsection(); ?>
