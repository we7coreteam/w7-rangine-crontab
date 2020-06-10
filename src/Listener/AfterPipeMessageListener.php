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

use Swoole\Coroutine;
use Swoole\Server;
use W7\App;
use W7\Core\Dispatcher\TaskDispatcher;
use W7\Core\Exception\HandlerExceptions;
use W7\Core\Listener\ListenerAbstract;
use W7\Crontab\Event\AfterExecutorEvent;
use W7\Crontab\Event\BeforeExecutorEvent;
use W7\Crontab\Message\CrontabMessage;

class AfterPipeMessageListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var Server $server
		 */
		$server = $params[0];
		/**
		 * @var CrontabMessage $message
		 */
		$message = $params[2];
		$data = $params[3];

		if ($message->messageType == CrontabMessage::CRONTAB_MESSAGE) {
			$context = App::getApp()->getContext();
			$context->setContextDataByKey('workid', $server->worker_id);
			$context->setContextDataByKey('coid', Coroutine::getuid());
			/**
			 * @var TaskDispatcher $taskDispatcher
			 */
			ievent(new BeforeExecutorEvent($data));
			$taskDispatcher = icontainer()->singleton(TaskDispatcher::class);
			try {
				$result = $taskDispatcher->dispatch($server, -1, $params[1], $data);
				if ($result === false) {
					return false;
				}
				ievent(new AfterExecutorEvent($data));
			} catch (\Throwable $throwable) {
				ievent(new AfterExecutorEvent($data, $throwable));
				icontainer()->singleton(HandlerExceptions::class)->getHandler()->report($throwable);
			}
		}
	}
}
