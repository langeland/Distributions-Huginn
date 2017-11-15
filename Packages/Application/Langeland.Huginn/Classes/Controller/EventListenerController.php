<?php

namespace Langeland\Huginn\Controller;

/*
 * This file is part of the Langeland.Huginn package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

class EventListenerController extends ActionController
{

    /**
     * @var string
     */
    protected $viewFormatToObjectNameMap = array(
        'json' => 'Neos\Flow\Mvc\View\JsonView'
    );

    /**
     *
     * https://developer.github.com/v3/activity/events/types/
     * @return void
     */
    public function githubAction()
    {
        $event = $this->request->getHttpRequest()->getHeader('X-Github-Event');
        $delivery = $this->request->getHttpRequest()->getHeader('X-Github-Delivery');
        $payload = $this->request->getArguments();

        \Neos\Flow\var_dump($event, 'X-Github-Event');
        \Neos\Flow\var_dump($delivery, 'X-Github-Delivery');
        \Neos\Flow\var_dump($payload, 'Payload');
        return 'this is github';
    }

}
