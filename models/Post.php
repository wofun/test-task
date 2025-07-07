<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "posts".
 *
 * @property int $id
 * @property string $name
 * @property string $text
 * @property string|null $fields
 * @property int $created_by
 * @property string $created_at
 *
 * @property User $createdBy
 * @property PostsTrack[] $postsTracks
 * @property PostsVisitors[] $postsVisitors
 * @property User[] $users
 * @property User[] $visitors
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fields'], 'default', 'value' => null],
            [['name', 'text', 'created_by', 'created_at'], 'required'],
            [['text'], 'string'],
            [['fields', 'created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'text' => 'Text',
            'fields' => 'Fields',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[PostsTracks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostTracksAssn()
    {
        return $this->hasMany(PostTrack::class, ['id_post' => 'id']);
    }

    /**
     * Gets query for [[PostsVisitors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostVisitorsAssn()
    {
        return $this->hasMany(PostVisitor::class, ['id_post' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscribers()
    {
        return $this->hasMany(User::class, ['id' => 'id_user'])->viaTable('posts_track', ['id_post' => 'id']);
    }

    /**
     * Gets query for [[Visitors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisitors()
    {
        return $this->hasMany(User::class, ['id' => 'id_visitor'])->viaTable('posts_visitors', ['id_post' => 'id']);
    }
}
