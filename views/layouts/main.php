<?php if (in_array(Yii::$app->controller->route, ['user/security/login'], true)): ?>
    <?= $this->render('main-login.php', ['content' => $content]) ?>
<?php else: ?>
    <?= $this->render('main.real.php', ['content' => $content]) ?>
<?php endif; ?>