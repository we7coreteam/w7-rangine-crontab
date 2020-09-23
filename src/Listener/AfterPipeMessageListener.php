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

use Swoole\Server;
use W7\Core\Exception\HandlerExceptions;
use W7\Core\Facades\Container;
use W7\Core\Facades\Context;
use W7\Core\Facades\Event;
use W7\Core\Listener\ListenerAbstract;
use W7\Core\Task\TaskDispatcher;
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

		//这里判断是不是crontab类型的message
		if ($message->messageType == CrontabMessage::CRONTAB_MESSAGE) {
			/**
			 * @var TaskDispatcher $taskDispatcher
			 */
			Event::dispatch(new BeforeExecutorEvent($data));
			$taskDispatcher = Container::singleton(TaskDispatcher::class);
			try {
				$result = $taskDispatcher->dispatchNow($data, $server, Context::getCoroutineId(), $params[1]);
				if ($result === false) {
					return false;
				}
				Event::dispatch(new AfterExecutorEvent($data));
			} catch (\Throwable $throwable) {
				Event::dispatch(new AfterExecutorEvent($data, $throwable));
				Container::singleton(HandlerExceptions::class)->getHandler()->report($throwable);
			}
		}
	}
}
