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

namespace W7\Crontab\Scheduler;

use W7\App;
use W7\Core\Exception\HandlerExceptions;
use W7\Core\Facades\Container;
use W7\Core\Facades\Event;
use W7\Crontab\Event\AfterTaskDispatcherEvent;
use W7\Crontab\Event\BeforeTaskDispatcherEvent;
use W7\Crontab\Strategy\StrategyAbstract;
use W7\Crontab\Task\CronTask;
use W7\Crontab\Task\CronTaskManager;

abstract class SchedulerAbstract {
	/**
	 * @var CronTaskManager
	 */
	protected $cronTaskManager;
	/**
	 * @var StrategyAbstract $strategy
	 */
	protected $strategy;

	public function __construct(CronTaskManager $cronTaskManager, StrategyAbstract $strategyAbstract) {
		$this->cronTaskManager = $cronTaskManager;
		$this->strategy = $strategyAbstract;
	}

	protected function scheduleTask(CronTask $cronTask) {
		try {
			Event::dispatch(new BeforeTaskDispatcherEvent($cronTask));
			if (!$this->strategy->dispatch(App::$server->getServer(), $cronTask->getTaskMessage())) {
				throw new \RuntimeException('dispatch task fail, task: ' . $cronTask->getTaskMessage()->pack());
			}
			Event::dispatch(new AfterTaskDispatcherEvent($cronTask));
		} catch (\Throwable $throwable) {
			Event::dispatch(new AfterTaskDispatcherEvent($cronTask, $throwable));
			Container::singleton(HandlerExceptions::class)->getHandler()->report($throwable);
		}
	}

	abstract public function schedule();
}
