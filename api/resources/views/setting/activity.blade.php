<?php $this->extends('layouts/setting.blade.php'); ?>

<?php $this->section('content'); ?>
<div class="card border border-light">
    <div class="card-body px-4 py-5 px-md-5">
        <h2 class="mb-3 font-weight-bold">Activity</h2>
        <p class="text-muted">
            View and manage the items you've liked. Keep track of your favorites and
            revisit them anytime to stay connected with what interests you most.
        </p>
        <!-- Gallery -->
        <div class="row flex-column" data-gallery></div>
    </div>
</div>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/activity.js'); ?>"></script>
<?php $this->endsection(); ?>
