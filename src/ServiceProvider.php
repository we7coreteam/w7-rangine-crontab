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

use W7\Console\Application;
use W7\Core\Provider\ProviderAbstract;
use W7\Core\Server\ServerEvent;
use W7\Core\Task\Event\AfterTaskExecutorEvent;
use W7\Core\Task\Event\BeforeTaskExecutorEvent;
use W7\Crontab\Event\AfterTaskDispatcherEvent;
use W7\Crontab\Event\BeforeTaskDispatcherEvent;
use W7\Crontab\Listener\AfterTaskDispatcherListener;
use W7\Crontab\Listener\AfterTaskExecutorListener;
use W7\Crontab\Listener\BeforeTaskDispatcherListener;
use W7\Crontab\Listener\BeforeTaskExecutorListener;
use W7\Crontab\Listener\CloseListener;
use W7\Crontab\Listener\ConnectListener;
use W7\Crontab\Listener\ReceiveListener;
use W7\Crontab\Scheduler\LoopScheduler;
use W7\Crontab\Server\Server;
use W7\Crontab\Strategy\WorkerStrategy;
use W7\Crontab\Task\CronTask;
use W7\Crontab\Task\CronTaskManager;

class ServiceProvider extends ProviderAbstract {
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerCrontabServer();
		$this->registerScheduler();
		$this->registerStrategy();
		$this->registerTaskManager();

		if ((ENV & DEBUG) != DEBUG) {
			return false;
		}
		$this->registerLog();
		$this->registerEvents();
	}

	private function registerCrontabServer() {
		$this->registerServer('crontab', Server::class);
		$this->registerServerEvent('crontab', [
			ServerEvent::ON_RECEIVE => ReceiveListener::class,
			ServerEvent::ON_CONNECT => ConnectListener::class,
			ServerEvent::ON_CLOSE => CloseListener::class
		]);
	}

	private function registerScheduler() {
		$this->container->set('cron-task-scheduler', function () {
			$scheduler = $this->config->get('crontab.setting.scheduler', LoopScheduler::class);
			return new $scheduler($this->container->get('cron-task-manager'), $this->container->get('cron-task-strategy'));
		});
	}

	private function registerStrategy() {
		$this->container->set('cron-task-strategy', function () {
			$strategy = $this->config->get('crontab.setting.strategy', WorkerStrategy::class);
			return new $strategy(Server::getDispatcherWorkerId());
		});
	}

	private function registerTaskManager() {
		$this->container->set('cron-task-manager', function () {
			$cronTaskManager = new CronTaskManager();

			$tasksConfig = $this->config->get('crontab.task', []);
			foreach ($tasksConfig as $name => $taskConfig) {
				if (isset($taskConfig['enable']) && $taskConfig['enable'] === false) {
					continue;
				}
				$cronTaskManager->add(new CronTask($name, $taskConfig));
			}

			return $cronTaskManager;
		});
	}

	private function registerLog() {
		if (!empty($this->config->get('log.channel.crontab'))) {
			return false;
		}

		$this->registerLogger('crontab', [
			'driver' => $this->config->get('handler.log.daily'),
			'path' => RUNTIME_PATH . '/logs/crontab.log',
			'level' => 'debug',
			'days' => 1
		]);
	}

	private function registerEvents() {
		$this->registerEvent(BeforeTaskExecutorEvent::class, BeforeTaskExecutorListener::class);
		$this->registerEvent(AfterTaskExecutorEvent::class, AfterTaskExecutorListener::class);
		$this->registerEvent(BeforeTaskDispatcherEvent::class, BeforeTaskDispatcherListener::class);
		$this->registerEvent(AfterTaskDispatcherEvent::class, AfterTaskDispatcherListener::class);
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
	}

	public function providers(): array {
		return [Application::class];
	}
}
