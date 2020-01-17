<?php

namespace W7\Crontab\Listener;

use W7\Core\Listener\ListenerAbstract;
use W7\Crontab\Event\AfterExecutorEvent;

class AfterExecutorListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var AfterExecutorEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	public function log(AfterExecutorEvent $event) {
		if (!$event->throwable) {
			ilogger()->channel('crontab')->debug('complete crontab task ' . $event->taskMessage->params['name'] . ' with data ' . $event->taskMessage->pack());
		} else {
			ilogger()->channel('crontab')->debug('complete crontab task ' . $event->taskMessage->params['name'] . ' with data ' . $event->taskMessage->pack() . ' with error ' . $event->throwable->getMessage());
		}
	}
}