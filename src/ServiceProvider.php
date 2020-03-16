<?php

/**
 * Rangine crontab server
 *
 * (c) We7Team 2019 <https://www.rangine.com>
 *
 * document http://s.w7.cc/index.php?c=wiki&do=view&id=317&list=2284
 *
 * visited https://www.rangine.com for more details
 */

namespace W7\Crontab;

use W7\Core\Log\LogManager;
use W7\Core\Provider\ProviderAbstract;
use W7\Core\Server\ServerEnum;
use W7\Core\Server\ServerEvent;
use W7\Crontab\Server\Server;

class ServiceProvider extends ProviderAbstract {
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerServer('crontab', Server::class);
		/**
		 * @var ServerEvent $event
		 */
		$event = iloader()->get(ServerEvent::class);
		$this->registerServerEvent('crontab', $event->getDefaultEvent()[ServerEnum::TYPE_PROCESS]);

		if ((ENV & DEBUG) != DEBUG) {
			return false;
		}
		$this->registerLog();
		$this->registerEvent();
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
			'level' => ((ENV & DEBUG) === DEBUG) ? 'debug' : 'info'
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
