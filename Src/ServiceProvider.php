<?php

namespace W7\Crontab;

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

		ServerEnum::registerServer('crontab', Server::class);
		/**
		 * @var SwooleEvent $event
		 */
		$event = iloader()->get(SwooleEvent::class);
		$event->addServerEvents('crontab', $event->getDefaultEvent()[ServerEnum::TYPE_PROCESS]);
	}

	private function registerLog() {
		$config = iconfig()->getUserConfig('log');
		$config['channel']['crontab'] = [
			'driver' => 'stream',
			'path' => RUNTIME_PATH . DS. 'logs'. DS. 'crontab.log',
			'level' => ienv('LOG_CHANNEL_CRONTAB_LEVEL', 'debug'),
		];
		iconfig()->setUserConfig('log', $config);
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		//需要提前初始化，如果在进程中再初始化，会清空日志
		ilogger();
	}
}
