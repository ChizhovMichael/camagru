<?php $this->extends('layouts/app.blade.php'); ?>

<?php $this->section('content'); ?>
<main class="full-screen px-4 py-5 px-md-5 overflow-hidden">
    <div class="container my-5">
        <?php $this->include('components/caption.blade.php'); ?>

        <p class="text-light my-5 text-justify">
            Our project focuses on developing a powerful tool that seamlessly merges multiple images into a single cohesive composition.
            Whether you're creating panoramic landscapes, combining various elements for creative designs, or stitching together a series of photos,
            our tool is designed to make the process intuitive and efficient. With advanced algorithms, it automatically aligns and blends images,
            ensuring smooth transitions and a natural look.
        </p>

        <p class="text-light text-justify mb-5">
            In addition to its core functionality, the project emphasizes user-friendly features such as adjustable blending modes,
            customizable layouts, and support for high-resolution images. This tool is perfect for photographers, graphic designers,
            and anyone looking to create stunning visual projects without the hassle of manual editing. Our goal is to provide a versatile
            and accessible solution for all your image merging needs.
        </p>
    </div>
</main>
<?php $this->endsection(); ?>

