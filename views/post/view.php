<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PostSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Post Details';
$this->params['breadcrumbs'][] = ['label' => 'Post', 'url' => '/post/index'];
$this->params['breadcrumbs'][] = 'Details';
?>

<div class="post-view ms-2">
    <div class="row">
        <div class="col">
            <?= \hail812\adminlte\widgets\Callout::widget([
                'type' => 'info',
                'head' => 'Title: ' . $post['name'],
                'body' => 'Text: ' . $post['text'],
            ]) ?>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-6">
            <h4>Visitors</h4>

            <?= GridView::widget([
                'dataProvider' => $visitorsProvider,
                'columns' => [
                    [
                        'attribute' => 'name',
                        'value' => function ($model) {
                            return $model->user->username;
                        }
                    ],
                    'view_at',
                ],
            ]); ?>
        </div>
        <div class="col-6">
            <h3>Track</h3>

            <?= GridView::widget([
                'dataProvider' => $trackProvider,
                'columns' => [
                    [
                        'attribute' => 'name',
                        'value' => function ($model) {
                            return $model->user->username;
                        }
                    ],
                    'track_at',
                ],
            ]); ?>
        </div>
    </div>




</div>