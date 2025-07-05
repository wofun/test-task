<style>
    .logo {
        border-radius: 50%;
        background-color: #D6D8D8;
        width: 33px;
        height: 33px;
        color: #353A40;
        opacity: .8;
        margin-right: 5px;
    }
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link d-flex">
        <div class="round-50 logo h3">G7</div>
        <span class="brand-text font-weight-light"><?= env('APP_NAME') ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php
            echo \hail812\adminlte\widgets\Menu::widget([
                'items' => [
                    ['label' => 'Posts', 'url' => ['post/index'], 'active' => Yii::$app->requestedRoute === 'post/index'],
                ],
            ]);
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>