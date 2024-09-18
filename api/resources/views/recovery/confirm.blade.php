<?php $this->extends('layouts/app.blade.php'); ?>

<?php $this->section('content'); ?>
<section class="background-radial-gradient overflow-hidden full-screen">
    <div class="container px-4 py-5 px-md-5 my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card border border-light">
                    <div class="card-body px-4 py-5 px-md-5">
                        <form action="/recovery/password" novalidate>
                            <h2 class="mb-3 font-weight-bold">Recovery password</h2>
                            <p class="text-muted">Recover your password to regain secure access to your account. Your privacy and security are our top priorities.</p>
                            <!-- Password input -->
                            <div class="mt-5 mb-3">
                                <label for="password">Password</label>
                                <input name="password" type="password" class="form-control" id="password" placeholder="Password" required minlength="8">
                                <div class="invalid-feedback">
                                    Password must be more than 8 characters.
                                </div>
                            </div>

                            <!-- Confirm password input -->
                            <div>
                                <label for="confirm_password">Confirm password</label>
                                <input name="confirm_password" type="password" class="form-control" id="confirm_password" placeholder="Confirm password" required minlength="8">
                                <div class="invalid-feedback">
                                    Confirm password must be more than 8 characters.
                                </div>
                            </div>

                            <div class="mt-5">
                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary btn-block mb-4">
                                    Save new password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/recovery/confirm.js'); ?>"></script>
<?php $this->endsection(); ?>