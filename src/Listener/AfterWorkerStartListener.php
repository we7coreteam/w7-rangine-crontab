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

namespace W7\Crontab\Listener;

use Swoole\Server as SwooleServer;
use W7\App;
use W7\Core\Exception\HandlerExceptions;
use W7\Core\Facades\Output;
use W7\Core\Listener\ListenerAbstract;
use W7\Crontab\Event\AfterDispatcherEvent;
use W7\Crontab\Event\BeforeDispatcherEvent;
use W7\Crontab\Server\Server;
use W7\Crontab\Strategy\StrategyAbstract;
use W7\Crontab\Strategy\WorkerStrategy;
use W7\Crontab\Task\Task;
use W7\Crontab\Task\TaskManager;

class AfterWorkerStartListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var SwooleServer $server
		 */
		$server = $params[0];
		$workerId = $params[1];

		//如果当前进程是当前server的0号进程，执行派发任务
		if ($workerId == Server::getDispatcherWorkerId()) {
			\isetProcessTitle(App::$server->getPname() . 'crontab dispatcher process');

			if ((ENV & DEBUG) === DEBUG) {
				Output::info('Crontab run at ' . date('Y-m-d H:i:s'));
			}

			$taskManager = new TaskManager($this->getEnableTasks());

			itimeTick(1000, function () use ($server, $taskManager) {
				$tasks = $taskManager->getRunTasks();
				/**
				 * @var Task $task
				 */
				foreach ($tasks as $name => $task) {
					try {
						ievent(new BeforeDispatcherEvent($task));
						if (!$this->getStrategy()->dispatch($server, $task->getTaskMessage())) {
							throw new \RuntimeException('dispatch task fail, task: ' . $task->getTaskMessage()->pack());
						}
						ievent(new AfterDispatcherEvent($task));
					} catch (\Throwable $throwable) {
						ievent(new AfterDispatcherEvent($task, $throwable));
						icontainer()->singleton(HandlerExceptions::class)->getHandler()->report($throwable);
					}
				}
			});
		}
	}

	public function getEnableTasks() {
		$enableTasks = [];
		$tasks = \iconfig()->get('crontab.task', []);
		foreach ($tasks as $name => $task) {
			if (isset($task['enable']) && $task['enable'] === false) {
				continue;
			}
			$enableTasks[$name] = $task;
		}
		return $enableTasks;
	}

	/**
	 * 任务派发方式，可自定义
	 * @return StrategyAbstract
	 */
	public function getStrategy() : StrategyAbstract {
		$strategy = iconfig()->get('crontab.setting.strategy', WorkerStrategy::class);
		$strategy = icontainer()->singleton($strategy, [Server::getDispatcherWorkerId()]);

		return $strategy;
	}
}
