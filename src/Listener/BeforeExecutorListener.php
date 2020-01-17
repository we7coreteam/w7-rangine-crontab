<?php

namespace W7\Crontab\Listener;

use W7\Core\Listener\ListenerAbstract;
use W7\Crontab\Event\BeforeExecutorEvent;

class BeforeExecutorListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var BeforeExecutorEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	private function log(BeforeExecutorEvent $event) {
		ilogger()->channel('crontab')->debug('exec crontab task ' . $event->taskMessage->params['name'] . ' with data ' . $event->taskMessage->pack());
	}
}