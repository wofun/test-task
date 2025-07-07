<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "posts_track".
 *
 * @property int $id_post
 * @property int $id_user
 * @property string $track_at
 *
 * @property Posts $post
 * @property User $user
 */
class PostTrack extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts_track';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_post', 'id_user', 'track_at'], 'required'],
            [['id_post', 'id_user'], 'integer'],
            [['track_at'], 'safe'],
            [['id_post', 'id_user'], 'unique', 'targetAttribute' => ['id_post', 'id_user']],
            [['id_post'], 'exist', 'skipOnError' => true, 'targetClass' => Post::class, 'targetAttribute' => ['id_post' => 'id']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_post' => 'Id Post',
            'id_user' => 'Id User',
            'track_at' => 'Track At',
        ];
    }

    /**
     * Gets query for [[Post]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'id_post']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'id_user']);
    }


    static public function getDataProvider(int $postId)
    {
        return new ActiveDataProvider([
            'query' => self::find()->with(['user'])->where(['id_post' => $postId]),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'track_at' => SORT_DESC,
                ]
            ],
        ]);
    }
}
