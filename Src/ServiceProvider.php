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
		ServerEnum::registerServer('crontab', Server::class);
		/**
		 * @var SwooleEvent $event
		 */
		$event = iloader()->get(SwooleEvent::class);
		$event->addServerEvents('crontab', $event->getDefaultEvent()[ServerEnum::TYPE_PROCESS]);
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {

	}
}
