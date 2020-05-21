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

namespace W7\Crontab\Process;

use Swoole\Process;
use W7\Core\Exception\HandlerExceptions;
use W7\Core\Process\ProcessAbstract;
use W7\Crontab\Event\AfterDispatcherEvent;
use W7\Crontab\Event\BeforeDispatcherEvent;
use W7\Crontab\Task\Task;
use W7\Crontab\Task\TaskManager;

class DispatcherProcess extends ProcessAbstract {
	/**
	 * @var TaskManager
	 */
	private $taskManager;
	private static $tasks = [];

	protected function init() {
		$this->taskManager = new TaskManager(static::getTasks());
	}

	public function check() {
		return true;
	}

	public static function getTasks() {
		if (!static::$tasks) {
			$tasks = \iconfig()->get('crontab.task', []);
			foreach ($tasks as $name => $task) {
				if (isset($task['enable']) && $task['enable'] === false) {
					continue;
				}
				static::$tasks[$name] = $task;
			}
		}
		return static::$tasks;
	}

	protected function run(Process $process) {
		if ((ENV & DEBUG) === DEBUG) {
			ioutputer()->info('Crontab run at ' . date('Y-m-d H:i:s'));
		}

		itimeTick(1000, function () {
			$tasks = $this->taskManager->getRunTasks();
			/**
			 * @var Task $task
			 */
			foreach ($tasks as $name => $task) {
				try {
					ievent(new BeforeDispatcherEvent($task));
					if (!$this->sendMsg($task->getTaskMessage()->pack())) {
						throw new \RuntimeException('dispatch task fail, task: ' . $task->getTaskMessage()->pack());
					}
					ievent(new AfterDispatcherEvent($task));
				} catch (\Throwable $e) {
					ievent(new AfterDispatcherEvent($task, $e));
					icontainer()->singleton(HandlerExceptions::class)->handle($e, $this->serverType);
				}
			}
		});
	}
}
