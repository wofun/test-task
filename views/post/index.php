<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PostSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index ms-2">
    <!--     <p>
        <?= Html::a('Create Post', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['width' => 70]
            ],
            'name',
            [
                'attribute' => 'visitors_count',
                'label' => 'Views count',
                'headerOptions' => ['width' => 110],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return $model['visitors_count'];
                }
            ],
            [
                'attribute' => 'subscribers_count',
                'label' => 'Subscribers count',
                'headerOptions' => ['width' => 110],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return $model['subscribers_count'];
                }
            ],
            [
                'attribute' => 'created_by',
                'headerOptions' => ['width' => 150],
                'contentOptions' => ['class' => 'text-center'],
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->creator->username, "/user/admin/index?UserSearch[id]={$model->creator->id}");
                }
            ],
            [
                'attribute' => 'created_at',
                'headerOptions' => ['width' => 100],
            ],
            [
                'headerOptions' => ['width' => 70, 'class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('Details', ['post/view', 'id' => $model['id']], ['class' => 'btn btn-sm btn-success']);
                }
            ],
        ],
    ]); ?>


</div>