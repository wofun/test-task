<?php

use yii\helpers\Html;
use yii\helpers\Url;


use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light none">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::home() ?>" class="nav-link <?= Yii::$app->requestedRoute === '' ? 'active' : '' ?>">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= Url::to(['/post/index']) ?>" class="nav-link <?= Yii::$app->requestedRoute === 'post/index' ? 'active' : '' ?>">Posts</a>
        </li>
        <li class="nav-item dropdown">
            <a id="sidebarSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">RBAC</a>
            <ul aria-labelledby="sidebarSubMenu1" class="dropdown-menu border-0 shadow">
                <li><a href="<?= Url::to(['/user/admin/index']) ?>" class="dropdown-item">Users</a></li>
                <li><a href="<?= Url::to(['/rbac/role/index']) ?>" class="dropdown-item">Roles</a></li>
                <li><a href="<?= Url::to(['/rbac/permission/index']) ?>" class="dropdown-item">Permissions</a></li>
                <li><a href="<?= Url::to(['/rbac/rule/index']) ?>" class="dropdown-item">Rules</a></li>
                <li class="dropdown-divider"></li>

                <!-- Level two dropdown-->
                <li class="dropdown-submenu dropdown-hover">
                    <a id="sidebarSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Create</a>
                    <ul aria-labelledby="sidebarSubMenu2" class="dropdown-menu border-0 shadow">
                        <li>
                            <a tabindex="-1" href="<?= Url::to(['/user/admin/create']) ?>" class="dropdown-item">New user</a>
                        </li>
                        <li><a href="<?= Url::to(['/rbac/role/create']) ?>" class="dropdown-item">New role</a></li>
                        <li><a href="<?= Url::to(['/rbac/permission/create']) ?>" class="dropdown-item">New permission</a></li>
                        <li><a href="<?= Url::to(['/rbac/rule/create']) ?>" class="dropdown-item">New rule</a></li>
                    </ul>
                </li>
                <!-- End Level two -->
            </ul>
        </li>
    </ul>

    <?php if (!Yii::$app->user->isGuest): ?>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item nav-link" style="padding-top:12px;">Hello, <?= Yii::$app->user->getIdentity()->username ?></li>
            <li class=" nav-item">
                <?= Html::a('<i class="fas fa-sign-out-alt"></i> ' . 'Sign out', ['/admin/logout'], ['data-method' => 'post', 'class' => 'nav-link']) ?>
            </li>
        </ul>
    <?php endif; ?>
</nav>
<!-- /.navbar -->