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

use W7\App;
use W7\Core\Facades\Container;
use W7\Core\Facades\Output;
use W7\Core\Listener\ListenerAbstract;
use W7\Crontab\Server\Server;

class AfterWorkerStartListener extends ListenerAbstract {
	public function run(...$params) {
		$workerId = $params[1];

		//如果当前进程是当前server的0号进程，执行派发任务
		if ($workerId == Server::getDispatcherWorkerId()) {
			\isetProcessTitle(App::$server->getPname() . 'crontab dispatcher process');

			if ((ENV & DEBUG) === DEBUG) {
				Output::info('Crontab run at ' . date('Y-m-d H:i:s'));
			}

			Container::get('task-scheduler')->schedule();
		}
	}
}
