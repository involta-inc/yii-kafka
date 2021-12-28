<?php

declare(strict_types=1);

use Involta\Yii\Kafka\BrokerList;
use Involta\Yii\Kafka\Connection;
use Involta\Yii\Kafka\Consumer;
use Involta\Yii\Kafka\Producer;

return [
    Producer::class => Producer::class,
    Consumer::class => Consumer::class,
    Connection::class => [
        '__construct()' => $params['involta-inc/kafka']['connection']
    ],
];
