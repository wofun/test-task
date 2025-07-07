<?php

namespace app\components\faker_data_generators;

use Faker\Factory;
use Generator;
use Yii;

abstract class BaseGenerator
{
    protected $faker;
    protected $totalAmount;

    public function __construct(?int $totalAmount = null)
    {
        $this->totalAmount = $totalAmount;
        $this->faker = Factory::create(str_replace('-', '_', Yii::$app->language));
    }

    abstract public function __invoke(int $amountPerBatch = 1000, array $options = []): Generator;
}
