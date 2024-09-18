<?php $this->extends('layouts/app.blade.php'); ?>

<?php $this->section('content'); ?>
<main class="position-relative">
    <div class="video-container">
        <video class="video-stream object-fit-contain bg-dark" data-stream></video>
        <img class="video-stream object-fit-contain bg-dark" alt="upload-image" data-poster style="display: none"/>
        <canvas class="video-overlay"></canvas>
        <div class="video-nav">
            <button type="button" class="video-button" id="save"></button>
            <div class="video-upload" data-upload></div>
            <div class="video-stickers" data-stickers></div>
        </div>
    </div>
    <div class="video-preview">
        <div class="video-preview-gallery" data-gallery></div>
    </div>
</main>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/camera.js'); ?>"></script>
<?php $this->endsection(); ?>
