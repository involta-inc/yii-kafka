<?php

namespace Involta\Yii\Kafka;

use Generator;
use \RdKafka\Consumer as KafkaConsumer;
use \RdKafka\TopicConf;
use Yiisoft\Json\Json;

class Consumer
{
    private const TIMEOUT = 10000;
    private const DEFAULT_PARTITION = 0;

    private KafkaConsumer $consumer;
    private $topic;
    private ?int $offset = null;

    public function __construct(Connection $connection, string $groupID)
    {
        $config = $connection->getConfig();

        $config->set('group.id', $groupID);
        $config->set('auto.offset.reset', 'smallest');
        $config->set('enable.auto.commit', 'false');

        $config->setRebalanceCb(function (KafkaConsumer $kafka, $err, ?array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    $kafka->assign(null);
                    break;

                default:
                    throw new \Exception($err);
            }
        });

        $topicConf = new TopicConf();
        $topicConf->set('auto.offset.reset', 'smallest');
        $config->setDefaultTopicConf($topicConf);

        $this->consumer = new KafkaConsumer($config);
    }

    public function subscribe(string $topic): Consumer
    {
        $instance = clone $this;
        $instance->topic = $instance->consumer->newTopic($topic);
        $instance->topic->consumeStart(self::DEFAULT_PARTITION, RD_KAFKA_OFFSET_STORED);
        return $instance;
    }

    public function consume(int $count): Generator
    {
        while ($count > 0) {
            $message = $this->topic->consume(self::DEFAULT_PARTITION, self::TIMEOUT);

            if (is_null($message)) {
                break;
            }

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    yield Json::decode($message->payload, true);
                    $this->offset = $message->offset;
                    $count--;
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $count = 0;
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
            }
        }
    }

    public function commit(): void
    {
        if (is_null($this->offset)) {
            return;
        }

        $this->topic->offsetStore(self::DEFAULT_PARTITION, $this->offset);
    }
}
