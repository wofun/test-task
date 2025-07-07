<?php
$isUserModuleInstalled = true;
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link d-flex">
        <div class="logo h3">G7</div>
        <span class="brand-text font-weight-light pt-1 ps-1"><?= env('APP_NAME') ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">

            <?php
            echo \hail812\adminlte\widgets\Menu::widget([
                'items' => [

                    ['label' => 'Posts', 'url' => ['/post/index'], 'active' => (Yii::$app->controller->id === 'post' && Yii::$app->controller->action->id === 'index')],
                    [
                        'label' => \Yii::t('rbac', 'RBAC'),
                        'header' => true,
                    ],
                    [
                        'label'   => \Yii::t('rbac', 'Users'),
                        'url'     => ['/user/admin/index'],
                        'visible' => $isUserModuleInstalled,
                    ],
                    [
                        'label' => \Yii::t('rbac', 'Roles'),
                        'url'   => ['/rbac/role/index'],
                    ],
                    [
                        'label' => \Yii::t('rbac', 'Permissions'),
                        'url'   => ['/rbac/permission/index'],
                    ],
                    [
                        'label' => \Yii::t('rbac', 'Rules'),
                        'url'   => ['/rbac/rule/index'],

                    ],
                    [
                        'label' => \Yii::t('rbac', 'Create'),
                        'items' => [
                            [
                                'label'   => \Yii::t('rbac', 'New user'),
                                'url'     => ['/user/admin/create'],
                                'visible' => $isUserModuleInstalled,
                                'iconStyle' => 'far',
                            ],
                            [
                                'label' => \Yii::t('rbac', 'New role'),
                                'url'   => ['/rbac/role/create'],
                                'iconStyle' => 'far',
                            ],
                            [
                                'label' => \Yii::t('rbac', 'New permission'),
                                'url'   => ['/rbac/permission/create'],
                                'iconStyle' => 'far',
                            ],
                            [
                                'label' => \Yii::t('rbac', 'New rule'),
                                'url'   => ['/rbac/rule/create'],
                                'iconStyle' => 'far',
                            ]
                        ]
                    ],


                ],
            ]);
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>