<?php

namespace app\controllers;

use app\models\Post;
use app\models\PostSearch;
use app\models\PostTrack;
use app\models\PostVisitor;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;

class PostController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        return $this->render('index');
    }


    public function actionView(int $id)
    {
        $data = Post::findOne($id);
        if (!$data) {
            throw new NotFoundHttpException('Post not found');
        }
        return $this->render('view', [
            'post' => $data,
            'visitorsProvider' => PostVisitor::getDataProvider($id),
            'trackProvider' => PostTrack::getDataProvider($id),
        ]);
    }
}
