<?php

namespace W7\Crontab;

use W7\Core\Provider\ProviderAbstract;
use W7\Core\Server\ServerEnum;
use W7\Crontab\Server\Server;

class ServiceProvider extends ProviderAbstract{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		ServerEnum::$ALL_SERVER['crontab'] = Server::class;
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {

	}
}
