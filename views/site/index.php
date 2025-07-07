<?php

use yii\helpers\Html;

$this->title = 'Dashboard';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <p>Welcome to the Administration panel.</p>
            <p>For now you can <?= Html::a('visit', '/post', ['class' => 'text-blue']) ?> the Posts page</p>
        </div>
    </div>

</div>