<?php

namespace Involta\Yii\Kafka;

use \RdKafka\Producer as KafkaProducer;
use Yiisoft\Json\Json;

class Producer
{
    private const MAX_ATTEMPTS = 10;
    private const TIMEOUT = 10000;
    private const DEFAULT_PARTITION = 0;

    private KafkaProducer $producer;
    private $topic;

    public function __construct(Connection $connection)
    {
        $this->producer = new KafkaProducer($connection->getConfig());
    }

    public function withTopic(string $topic): Producer
    {
        $instance = clone $this;
        $instance->topic = $instance->producer->newTopic($topic);
        return $instance;
    }

    public function produce(array $message)
    {
        $this->topic->produce(RD_KAFKA_PARTITION_UA, self::DEFAULT_PARTITION, Json::encode($message));
    }

    public function flush()
    {
        for ($flushRetries = 0; $flushRetries < self::MAX_ATTEMPTS; $flushRetries++) {
            $result = $this->producer->flush(self::TIMEOUT);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }
    }
}
