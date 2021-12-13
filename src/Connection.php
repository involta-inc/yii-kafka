<?php

namespace Involta\Yii\Kafka;

use \RdKafka\Conf;

class Connection
{
    private Conf $conf;

    public function __construct(BrokerList $list)
    {
        $this->conf = new Conf();
        $this->conf->set('metadata.broker.list', $list);
    }

    public function getConfig(): Conf
    {
        return $this->conf;
    }
}
