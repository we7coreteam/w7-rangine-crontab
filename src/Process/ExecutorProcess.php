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
use W7\Core\Dispatcher\TaskDispatcher;
use W7\Core\Exception\HandlerExceptions;
use W7\Core\Process\ProcessAbstract;
use W7\Crontab\Event\AfterExecutorEvent;
use W7\Crontab\Event\BeforeExecutorEvent;

class ExecutorProcess extends ProcessAbstract {
	public function check() {
		return true;
	}

	protected function run(Process $process) {
		itimeTick(1000, function () {
			if ($data = $this->readMsg()) {
				/**
				 * @var TaskDispatcher $taskDispatcher
				 */
				ievent(new BeforeExecutorEvent($data));
				$taskDispatcher = iloader()->get(TaskDispatcher::class);
				try {
					$result = $taskDispatcher->dispatch($this->process, -1, $this->process->pid, $data);
					if ($result === false) {
						return false;
					}
					ievent(new AfterExecutorEvent($data));
				} catch (\Throwable $throwable) {
					ievent(new AfterExecutorEvent($data, $throwable));
					iloader()->get(HandlerExceptions::class)->handle($throwable, $this->serverType);
				}
			}
		});
	}
}
