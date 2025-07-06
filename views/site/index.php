<?php

use yii\helpers\Html;

$this->title = 'Admin panel';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <?= \hail812\adminlte\widgets\Callout::widget([
                'type' => 'info',
                'head' => 'Posts',
                'body' => 'For now you can ' . Html::a('visit', '/posts', ['class' => 'text-blue']) . ' the Posts page'
            ]) ?>
        </div>
    </div>

</div>