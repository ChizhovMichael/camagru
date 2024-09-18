<?php $this->extends('layouts/app.blade.php'); ?>

<?php $this->section('content'); ?>
<main class="full-screen px-4 py-5 px-md-5 overflow-hidden">
    <div class="container my-5">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-5 mb-lg-0 text-center text-lg-left" style="z-index: 10">
                <h1 class="my-5 font-weight-bold" style="color: hsl(218, 81%, 95%)">
                    Camagru <br />
                    <span style="color: hsl(218, 81%, 75%)">Photo overlay tool</span>
                </h1>
                <p class="mb-4" style="color: hsl(218, 81%, 85%)">
                    Log in to securely access your account and explore all available features.
                    Your privacy and security are our top priorities.
                </p>
            </div>

            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card border border-light">
                    <div class="card-body px-4 py-5 px-md-5">
                        <h2 class="mb-3 font-weight-bold">Sign in</h2>

                        <form action="/login" novalidate>

                            <!-- Email input -->
                            <div class="mb-3">
                                <label for="email">Email</label>
                                <input name="email" type="email" class="form-control" id="email" placeholder="Email" required>
                                <div class="invalid-feedback">
                                    Please provide a valid email.
                                </div>
                            </div>

                            <!-- Password input -->
                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input name="password" type="password" class="form-control" id="password" placeholder="Password" required minlength="8">
                                <div class="invalid-feedback">
                                    Password must be more than 8 characters.
                                </div>
                            </div>

                            <div class="mt-5">
                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary btn-block mb-4">
                                    Sign in
                                </button>
                                <hr>
                                <!-- Recovery buttons -->
                                <div class="text-center">
                                    <span class="text-muted">If you forgot password</span>
                                    <a href="/recovery" class="text-primary">
                                        Recovery password
                                    </a>
                                    <span class="text-muted">or</span>
                                    <a href="/register" class="text-primary">
                                        Register
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/auth.js'); ?>"></script>
<?php $this->endsection(); ?>