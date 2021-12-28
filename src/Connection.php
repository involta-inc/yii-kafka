<?php

namespace Involta\Yii\Kafka;

use \RdKafka\Conf;

class Connection
{
    private Conf $conf;

    public function __construct(array $params = [])
    {
        $this->conf = new Conf();
        foreach ($params as $paramName => $paramValue) {
            if(trim($paramValue) !== '') {
                $this->conf->set($paramName, $paramValue);
            }
        }
    }

    public function getConfig(): Conf
    {
        return $this->conf;
    }
}
