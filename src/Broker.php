<?php

namespace Involta\Yii\Kafka;

class Broker
{
    private string $host;
    private int $port;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function getConnection(): string
    {
        return $this->host . ':' . $this->port;
    }
}
