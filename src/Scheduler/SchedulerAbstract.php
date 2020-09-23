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
use W7\Crontab\Event\AfterDispatcherEvent;
use W7\Crontab\Event\BeforeDispatcherEvent;
use W7\Crontab\Strategy\StrategyAbstract;
use W7\Crontab\Task\Task;
use W7\Crontab\Task\TaskManager;

abstract class SchedulerAbstract {
	/**
	 * @var TaskManager $taskManager
	 */
	protected $taskManager;
	/**
	 * @var StrategyAbstract $strategy
	 */
	protected $strategy;

	public function __construct(TaskManager $taskManager, StrategyAbstract $strategyAbstract) {
		$this->taskManager = $taskManager;
		$this->strategy = $strategyAbstract;
	}

	protected function scheduleTask(Task $task) {
		try {
			Event::dispatch(new BeforeDispatcherEvent($task));
			if (!$this->strategy->dispatch(App::$server->getServer(), $task->getTaskMessage())) {
				throw new \RuntimeException('dispatch task fail, task: ' . $task->getTaskMessage()->pack());
			}
			Event::dispatch(new AfterDispatcherEvent($task));
		} catch (\Throwable $throwable) {
			Event::dispatch(new AfterDispatcherEvent($task, $throwable));
			Container::singleton(HandlerExceptions::class)->getHandler()->report($throwable);
		}
	}

	abstract public function schedule();
}
