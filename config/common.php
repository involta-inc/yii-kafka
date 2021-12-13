<?php

declare(strict_types=1);

use Involta\Yii\Kafka\BrokerList;
use Involta\Yii\Kafka\Connection;
use Involta\Yii\Kafka\Consumer;
use Involta\Yii\Kafka\Producer;

return [
    BrokerList::class => [
        '__construct()' => [
            'connections' => $params['involta-inc/kafka']['connections']
        ]
    ],
    Producer::class => Producer::class,
    Consumer::class => Consumer::class,
    Connection::class => Connection::class,
];
