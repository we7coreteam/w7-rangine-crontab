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

use Psr\EventDispatcher\EventDispatcherInterface;
use W7\App;
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

	/**
	 * @var EventDispatcherInterface
	 */
	protected $eventDispatcher;

	public function __construct(CronTaskManager $cronTaskManager, StrategyAbstract $strategyAbstract) {
		$this->cronTaskManager = $cronTaskManager;
		$this->strategy = $strategyAbstract;
	}

	public function setEventDispatcher(EventDispatcherInterface $eventDispatcher) {
		$this->eventDispatcher = $eventDispatcher;
	}

	protected function scheduleTask(CronTask $task) {
		try {
			$this->eventDispatcher && $this->eventDispatcher->dispatch(new BeforeTaskDispatcherEvent($task));
			if (!$this->strategy->dispatch(App::$server->getServer(), $task->getTaskMessage())) {
				throw new \RuntimeException('dispatch task fail, task: ' . $task->getTaskMessage()->pack());
			}
			$this->eventDispatcher && $this->eventDispatcher->dispatch(new AfterTaskDispatcherEvent($task));
		} catch (\Throwable $throwable) {
			$this->eventDispatcher && $this->eventDispatcher->dispatch(new AfterTaskDispatcherEvent($task, $throwable));
		}
	}

	abstract public function schedule();
}
