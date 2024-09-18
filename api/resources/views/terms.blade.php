<?php $this->extends('layouts/app.blade.php'); ?>

<?php $this->section('content'); ?>
<main class="full-screen px-4 py-5 px-md-5 overflow-hidden">
    <div class="container my-5">
        <?php $this->include('components/caption.blade.php'); ?>

        <p class="text-light my-5 text-justify">
            Our Terms of Service outline the guidelines and rules governing the use of our platform.
            By accessing or using our services, you agree to comply with these terms, which are designed to ensure a safe, fair,
            and enjoyable experience for all users. The terms cover essential aspects such as user responsibilities,
            privacy protection, and the acceptable use of our tools and resources.
        </p>

        <p class="text-light text-justify mb-5">
            We are committed to transparency and fairness in our operations,
            and these terms reflect our dedication to maintaining a trustworthy environment.
            It is important to review these terms regularly, as they may be updated to accommodate new features or changes in legal requirements.
            Your continued use of our platform signifies your acceptance of any modifications.
        </p>
    </div>
</main>
<?php $this->endsection(); ?>

