<?php

namespace app\components\faker_data_generators;

use Generator;
use Yii;

class User extends BaseGenerator
{
    public function __invoke(int $amountPerBatch = 1000, array $options = []): Generator
    {
        $data = [];
        $passwordHash = Yii::$app->getSecurity()->generatePasswordHash('password');

        for ($i = 0; $i < $this->totalAmount; $i++) {
            if (count($data) === $amountPerBatch) {
                yield $data;
                $data = [];
            }
            $data[] = [
                'username' => $this->faker->firstName . ' ' . $this->faker->lastName  . ' ' . $i,
                'email' => $i . $this->faker->email,
                'password_hash' =>  $passwordHash,
                'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
                'confirmed_at' => time(),
                'created_at' => time(),
                'updated_at' => time(),
                'flags' => 0,
            ];
        }
        yield $data;
    }
}
