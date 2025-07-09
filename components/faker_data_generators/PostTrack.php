<?php

namespace app\components\faker_data_generators;

use Generator;

class PostTrack extends BaseGenerator
{
    public function __invoke(int $amountPerBatch = 1000, array $options = []): Generator
    {
        $data = [];
        for ($i = $options['postIdFrom']; $i <= $options['postIdTo']; $i++) {
            $userId = rand($options['userIdFrom'], $options['userIdTo'] - 20);
            for ($k = $userId; $k < $userId + rand(10, 20); $k++) {
                $data[] = [
                    'id_post' => $i,
                    'id_user' => $k,
                    'track_at' => date('Y-m-d H:i:s', time()),
                ];
                if (count($data) === $amountPerBatch) {
                    yield $data;
                    $data = [];
                }
            }
        }
        if (!empty($data)) {
            yield $data;
        }
    }
}
