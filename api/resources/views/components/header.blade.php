<?php if ($this->v('header')): ?>
<?php $isProfile = str_starts_with($_SERVER['REQUEST_URI'], '/profile'); ?>
<?php $isConfirm = str_starts_with($_SERVER['REQUEST_URI'], '/confirm'); ?>
<header class="d-flex justify-content-end py-3 px-5 <?=$isProfile?'absolute-top':''?>">
    <?php if ($this->v('username')): ?>
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="logout" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
            </symbol>
            <symbol id="home" viewBox="0 0 16 16">
                <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z" />
            </symbol>
        </svg>

        <div class="dropdown">
            <div class="d-flex align-items-center">
                <img src="<?=$this->assets('img/avatar.jpg')?>" class="rounded-circle" style="width: 35px;" alt="Avatar" />
                <a href="#" class="text-light btn btn-link" onclick="dropdown(event)"><?=$this->v('username')?></a>
            </div>
            <div class="dropdown-menu">
                <a class="align-items-center d-flex dropdown-item" href="/">
                    <svg width="16" height="16" fill="currentColor" class="bi bi-house mr-2">
                        <use xlink:href="#home"></use>
                    </svg>
                    Home
                </a>
                <div class="dropdown-divider"></div>
                <?php if (!$isConfirm): ?>
                    <a class="dropdown-item" href="/profile">Snapshot</a>
                    <a class="dropdown-item" href="/setting/profile">Settings</a>
                    <div class="dropdown-divider"></div>
                <?php endif; ?>
                <a class="align-items-center d-flex dropdown-item" href="#" onclick="logout(event)">
                    <svg width="16" height="16" fill="currentColor" class="bi bi-house mr-2">
                        <use xlink:href="#logout"></use>
                    </svg>
                    Logout
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($this->v('header')['links'] as ['name' => $name, 'link' => $link]): ?>
            <a href="<?=$link?>" class="text-light btn btn-link"><?=$name?></a>
        <?php endforeach; ?>
    <?php endif; ?>
</header>
<?php endif; ?>
