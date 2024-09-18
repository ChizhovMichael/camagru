<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Camagru - Photo overlay tool</title>
    <?php $this->include('components/meta.blade.php'); ?>
    <?php $this->yield('css'); ?>
</head>
<body class="background-radial-gradient">
    <?php $this->include('components/header.blade.php'); ?>
    <div class="full-screen px-4 py-5 px-md-5 overflow-hidden">
        <div class="container my-5">
            <div class="row">
                <main class="col-12 col-lg-8 order-2 order-lg-1 mb-3">
                    <?php $this->yield('content'); ?>
                </main>
                <aside class="col-12 col-lg-4 order-1 order-lg-2 mb-3">
                    <?php $this->include('components/aside.blade.php'); ?>
                </aside>
            </div>
        </div>
    </div>
    <?php $this->include('components/footer.blade.php'); ?>

    <!-- Scripts -->
    <script src="<?= $this->assets('js/index.js') ?>"></script>
    <script src="<?= $this->assets('js/handle.js') ?>"></script>
    <script src="<?= $this->assets('js/component.js') ?>"></script>
    <?php $this->yield('script'); ?>
</body>
</html>