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
                    Register to create your account and unlock all features.
                    Your privacy and security are our top priorities.
                </p>
            </div>

            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card border border-light">
                    <div class="card-body px-4 py-5 px-md-5">
                        <h2 class="mb-3 font-weight-bold">Sign up</h2>
                        <form action="/register" novalidate>
                            <!-- Name input -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name">First name</label>
                                    <input name="first_name" type="text" class="form-control" id="first_name" placeholder="First name" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid first name.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name">Last name</label>
                                    <input name="last_name" type="text" class="form-control" id="last_name" placeholder="Last name" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid last name.
                                    </div>
                                </div>
                            </div>

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
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="progress" style="height: 5px"></div>
                                </div>
                            </div>
                            <div class="mt-5">
                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary btn-block mb-4">
                                    Sign up
                                </button>
                                <!-- Login buttons -->
                                <div class="text-center">
                                    <span class="text-muted">If you already have account</span>
                                    <a href="/login" class="text-primary">
                                        Login
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