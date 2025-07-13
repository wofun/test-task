<?php

namespace app\components\faker_data_generators;

use Generator;

class Post extends BaseGenerator
{
    public function __invoke(int $amountPerBatch = 1000, array $options = []): Generator
    {
        $data = [];
        for ($i = 0; $i < $this->totalAmount; $i++) {
            if (count($data) === $amountPerBatch) {
                yield $data;
                $data = [];
            }
            $data[] = [
                'name' => $this->faker->sentence(),
                'text' => $this->faker->text,
                'created_by' => rand($options['userIdFrom'], $options['userIdTo']),
                'created_at' => date('Y-m-d H:i:s', time()),
                'visitors_count' => rand(100, 150),
                'subscribers_count' => rand(10, 20),
                // 'created_at' => $this->faker->dateTimeBetween('-3 year'),
            ];
        }
        yield $data;
    }
}
