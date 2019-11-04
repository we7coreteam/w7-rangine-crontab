<?php

namespace W7\Crontab;

use W7\Core\Log\LogManager;
use W7\Core\Provider\ProviderAbstract;
use W7\Core\Server\ServerEnum;
use W7\Core\Server\SwooleEvent;
use W7\Crontab\Server\Server;

class ServiceProvider extends ProviderAbstract{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerLog();

		$this->registerServer('crontab', Server::class);
		/**
		 * @var SwooleEvent $event
		 */
		$event = iloader()->get(SwooleEvent::class);
		$this->registerServerEvent('crontab', $event->getDefaultEvent()[ServerEnum::TYPE_PROCESS]);
	}

	private function registerLog() {
		if (!empty($this->config->getUserConfig('log')['channel']['crontab'])) {
			return false;
		}
		/**
		 * @var LogManager $logManager
		 */
		$logManager = iloader()->get(LogManager::class);
		$logManager->addChannel('crontab', 'stream', [
			'path' => RUNTIME_PATH . '/logs/crontab.log',
			'level' => ienv('LOG_CHANNEL_CRONTAB_LEVEL', 'debug'),
		]);
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
	}
}
