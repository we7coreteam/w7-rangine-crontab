<?php

/**
 * This file is part of Rangine
 *
 * (c) We7Team 2019 <https://www.rangine.com>
 *
 * document http://s.w7.cc/index.php?c=wiki&do=view&id=317&list=2284
 *
 * visited https://www.rangine.com/ for more details
 */

namespace W7\Crontab\Process;

use Swoole\Timer;
use Swoole\Process;
use W7\Core\Dispatcher\TaskDispatcher;
use W7\Core\Process\ProcessAbstract;

class ExecutorProcess extends ProcessAbstract {
	public function check(){
		return true;
	}

	protected function run(Process $process) {
		Timer::tick(1000, function () {
			if ($data = $this->readMsg()) {
				/**
				 * @var TaskDispatcher $taskDispatcher
				 */
				ilogger()->debug('pop crontab task ' .$data . ' at ' . $this->process->pid);
				$taskDispatcher = iloader()->get(TaskDispatcher::class);
				try {
					$result = $taskDispatcher->dispatch($this->process, -1, $this->process->pid, $data);
					if ($result === false) {
						return false;
					}
					ilogger()->debug('complete crontab task ' . $result->task . ' with data ' .$data . ' at ' . $this->process->pid);
				} catch (\Throwable $e) {
					ilogger()->debug('exec crontab task fail with data ' .$data . ' at ' . $this->process->pid . ' with error ' . $e->getMessage());
				}
			}
		});
	}
}
