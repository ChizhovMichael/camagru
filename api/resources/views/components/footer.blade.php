<?php if ($this->v('footer')): ?>
<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 border-top border-dark" style="background-color: #202225">
    <p class="col-md-4 mb-0 text-secondary">© 2024 <?=$this->v('footer')['company']?></p>

    <a href="/" class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <img src="<?= $this->assets('favicon/android-chrome-512x512.png') ?>" width="40" height="40" alt="logo">
    </a>

    <ul class="nav col-md-4 justify-content-end">
        <?php foreach ($this->v('footer')['links'] as ['name' => $name, 'link' => $link]): ?>
        <li class="nav-item">
            <a href="<?=$link?>" class="nav-link px-2 text-light"><?=$name?></a>
        </li>
        <?php endforeach; ?>
        <?php if ($this->v('username')): ?>
        <li class="nav-item">
            <a href="#" class="nav-link px-2 text-light" onclick="logout()">Logout</a>
        </li>
        <?php endif; ?>
    </ul>
</footer>
<?php endif; ?>