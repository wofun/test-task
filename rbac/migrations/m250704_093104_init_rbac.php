<?php

use app\rbac\PostAuthorRule;
use dektrium\rbac\migrations\Migration;

class m250704_093104_init_rbac extends Migration
{
    public function safeUp()
    {
        $createPost = $this->createPermission('createPost', 'Ability to create a post');
        $updatePost = $this->createPermission('updatePost', 'Ability to update a post');
        $deletePost = $this->createPermission('deletePost', 'Ability to delete a post');

        $managePost = $this->createPermission('managePost', 'Ability to view post information');

        // Post owner rule and permissions
        $postAuthorRule = $this->createRule('postAuthor', PostAuthorRule::class);
        $updateOwnPost = $this->createPermission('updateOwnPost', 'User ability to update own post', $postAuthorRule->name);
        $deleteOwnPost = $this->createPermission('deleteOwnPost', 'User ability to delete own post', $postAuthorRule->name);

        $this->addChild($updateOwnPost, $updatePost);
        $this->addChild($deleteOwnPost, $deletePost);

        $user = $this->createRole('user', 'User');
        $this->addChild($user, $createPost);
        $this->addChild($user, $updateOwnPost);
        $this->addChild($user, $deleteOwnPost);

        $admin = $this->createRole('admin', 'Administrator');
        $this->addChild($admin, $updatePost);
        $this->addChild($admin, $deletePost);
        $this->addChild($admin, $managePost);

        $this->addChild($admin, $user);
    }

    public function safeDown()
    {
        // Remove the rule
        \Yii::$app
            ->db
            ->createCommand()
            ->delete('auth_rule', ['name' => 'PostAuthor'])
            ->execute();

        $this->removeItem('createPost');
        $this->removeItem('updatePost');
        $this->removeItem('deletePost');
        $this->removeItem('managePost');
        $this->removeItem('updateOwnPost');
        $this->removeItem('deleteOwnPost');
        $this->removeItem('user');
        $this->removeItem('admin');
    }
}
