<?php

namespace Roxielfr\WpPodsImport\Trait;

use Roxielfr\WpPodsImport\Config\Config;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

trait HasLoggerTrait
{
    /** @var Logger|null */
    protected $logger;

    public function setLogger($name): void
    {
        $this->logger = new Logger($name);
        $this->logger->pushHandler(new StreamHandler(Config::$upload_dir.'/'.$name.'.log', Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    protected function log(string $level, string $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}