<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "posts_visitors".
 *
 * @property int $id_post
 * @property int $id_visitor
 * @property string $view_at
 *
 * @property Posts $post
 * @property User $visitor
 */
class PostVisitor extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts_visitors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_post', 'id_visitor', 'view_at'], 'required'],
            [['id_post', 'id_visitor'], 'integer'],
            [['view_at'], 'safe'],
            [['id_post', 'id_visitor'], 'unique', 'targetAttribute' => ['id_post', 'id_visitor']],
            [['id_post'], 'exist', 'skipOnError' => true, 'targetClass' => Post::class, 'targetAttribute' => ['id_post' => 'id']],
            [['id_visitor'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_visitor' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_post' => 'Id Post',
            'id_visitor' => 'Id Visitor',
            'view_at' => 'View At',
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
     * Gets query for [[Visitor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisitor()
    {
        return $this->hasOne(User::class, ['id' => 'id_visitor']);
    }
}
