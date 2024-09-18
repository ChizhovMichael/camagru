<?php $this->extends('layouts/app.blade.php'); ?>
<?php $this->section('content'); ?>
<main class="full-screen px-4 py-5 px-md-5 overflow-hidden">
    <div class="container-fluid my-5">
        <?php $this->include('components/caption.blade.php'); ?>
        <!-- Gallery -->
        <div class="row" data-gallery></div>
        <!-- Gallery -->
        <!-- Pagination -->
        <nav class="mt-3" aria-label="Page navigation example" data-pagination></nav>
        <!-- Pagination -->
    </div>
</main>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/welcome.js'); ?>"></script>
<?php $this->endsection(); ?>

