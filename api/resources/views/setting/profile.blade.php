<?php $this->extends('layouts/setting.blade.php'); ?>

<?php $this->section('content'); ?>
<div class="card border border-light">
    <div class="card-body px-4 py-5 px-md-5">
        <h2 class="mb-3 font-weight-bold">Change profile information</h2>
        <p class="text-muted">
            Update your personal information securely and easily. Keep your profile current by editing your details,
            ensuring that your account remains accurate and up-to-date. Your privacy is protected throughout the process.
        </p>
        <form action="/setting/profile" novalidate>
            <!-- Username input -->
            <div class="mb-3">
                <label for="username">Username</label>
                <input name="username" type="text" class="form-control" id="username" placeholder="Username" required value="<?=$this->v('username')?>">
                <div class="invalid-feedback">
                    Please provide a valid username.
                </div>
            </div>

            <!-- Email input -->
            <div class="mb-3">
                <label for="email">Email</label>
                <input name="email" type="email" class="form-control" id="email" placeholder="Email" required value="<?=$this->v('email')?>">
                <div class="invalid-feedback">
                    Please provide a valid email.
                </div>
            </div>

            <!-- Comment notification input -->
            <p class="text-dark mb-2">Send comment notification</p>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input
                                name="send_comment"
                                type="checkbox"
                                class="form-control"
                                id="send_comment"
                                placeholder="Send comment notification"
                                <?=$this->v('send_comment')?'checked':''?>
                        >
                    </div>
                </div>
                <label for="send_comment" class="form-control">Send comment notification</label>
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


