<?php

namespace Involta\Yii\Kafka;

class BrokerList
{
    private array $brokers = [];

    public function __construct(array $connections)
    {
        foreach ($connections as $connection) {
            $broker = new Broker($connection['host'], $connection['port']);
            $this->add($broker);
        }
    }

    public function add(Broker $broker): void
    {
        $this->brokers[] = $broker;
    }

    public function __toString()
    {
        $result = [];
        foreach ($this->brokers as $broker) {
            $result[] = $broker->getConnection();
        }
        return implode(',', $result);
    }
}
