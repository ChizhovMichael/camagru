<?php $this->extends('layouts/setting.blade.php'); ?>

<?php $this->section('content'); ?>
<div class="card border border-light">
    <div class="card-body px-4 py-5 px-md-5">
        <h2 class="mb-3 font-weight-bold">Change profile information</h2>
        <p class="text-muted">
            Change your password to enhance your account security. Update your credentials
            easily and ensure your account remains protected at all times.
        </p>
        <form action="/setting/password" novalidate>
            <!-- Password input -->
            <div class="mb-3">
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
                    Change
                </button>
            </div>
        </form>
    </div>
</div>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/setting.js'); ?>"></script>
<?php $this->endsection(); ?>
