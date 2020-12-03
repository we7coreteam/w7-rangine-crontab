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
use W7\Crontab\Event\AfterCronTaskExecutorEvent;
use W7\Crontab\Event\BeforeCronTaskExecutorEvent;
use W7\Crontab\Event\AfterCronTaskDispatchEvent;
use W7\Crontab\Event\BeforeCronTaskDispatchEvent;
use W7\Crontab\Listener\AfterCronTaskDispatchListener;
use W7\Crontab\Listener\AfterCronTaskExecutorListener;
use W7\Crontab\Listener\BeforeCronTaskDispatchListener;
use W7\Crontab\Listener\BeforeCronTaskExecutorListener;
use W7\Crontab\Listener\CloseListener;
use W7\Crontab\Listener\ConnectListener;
use W7\Crontab\Listener\ReceiveListener;
use W7\Crontab\Scheduler\LoopScheduler;
use W7\Crontab\Scheduler\SchedulerAbstract;
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
			/**
			 * @var SchedulerAbstract $scheduler
			 */
			$scheduler = $this->config->get('crontab.setting.scheduler', LoopScheduler::class);
			$scheduler = new $scheduler($this->container->singleton('cron-task-manager'), $this->container->singleton('cron-task-strategy'));
			$scheduler->setEventDispatcher($this->getEventDispatcher());

			return $scheduler;
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
		$this->getEventDispatcher()->listen(BeforeCronTaskExecutorEvent::class, BeforeCronTaskExecutorListener::class);
		$this->getEventDispatcher()->listen(AfterCronTaskExecutorEvent::class, AfterCronTaskExecutorListener::class);
		$this->getEventDispatcher()->listen(BeforeCronTaskDispatchEvent::class, BeforeCronTaskDispatchListener::class);
		$this->getEventDispatcher()->listen(AfterCronTaskDispatchEvent::class, AfterCronTaskDispatchListener::class);
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
