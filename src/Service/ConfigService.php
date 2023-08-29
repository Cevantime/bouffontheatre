<?php

namespace App\Service;

use _PHPStan_7c8075089\Nette\Neon\Exception;
use App\Entity\Config;
use App\Repository\ConfigRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConfigService
{
    const ACCESS_TOKEN = "access_token";
    const REFRESH_TOKEN = "refresh_token";
    const EXPIRES_AT = "expires_at";
    const EVENT_NEXT_SYNC_TOKEN = "event_next_sync_token";
    const EVENT_LAST_SYNC_TIME = "event_last_sync_time";

    private $configs;

    public function __construct(
        private ConfigRepository $configRepository,
        private EntityManagerInterface $manager
    )
    {
    }

    /**
     * @throws Exception
     */
    public function getValue($key)
    {
        return $this->getConfig($key)->getValue();
    }

    public function getConfig($key)
    {
        $this->loadConfig();
        if($this->hasValue($key)){
            return $this->configs[$key];
        }
        throw new Exception("Trying to get a config with non existing key name");
    }

    public function hasValue($key)
    {
        $this->loadConfig();
        return isset($this->configs[$key]);
    }

    private function setConfig($key, $value)
    {
        if($this->hasValue($key)) {
            $config = $this->getConfig($key);
        } else {
            $config = new Config();
            $config->setKeyName($key);
            $this->configs[$key] = $config;
        }
        $config->setValue($value);
        $this->manager->persist($config);
    }

    private function setConfigs(array $configs)
    {
        foreach ($configs as $key => $value) {
            $this->setConfig($key, $value);
        }
    }

    public function saveConfig($key, $value)
    {
        $this->setConfig($key, $value);
        $this->manager->flush();
    }

    public function saveConfigs(array $configs)
    {
        $this->setConfigs($configs);
        $this->manager->flush();
    }

    private function loadConfig()
    {
        if($this->configs === null) {
            $this->configs = [];
            $allConfigs = $this->configRepository->findAll();
            foreach ($allConfigs as $config) {
                $this->configs[$config->getKeyName()] = $config;
            }
        }
    }
}
