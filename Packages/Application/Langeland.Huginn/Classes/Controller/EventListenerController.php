<?php

namespace Langeland\Huginn\Controller;

/*
 * This file is part of the Langeland.Huginn package.
 */

use Langeland\Huginn\Domain\Model\GithubEvent;
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
        $signature = $this->request->getHttpRequest()->getHeader('X-Hub-Signature');
        $delivery = $this->request->getHttpRequest()->getHeader('X-Github-Delivery');
        $payload = $this->request->getArguments();

//        \Neos\Flow\var_dump($event, 'X-Github-Event');
//        \Neos\Flow\var_dump($delivery, 'X-Github-Delivery');
//        \Neos\Flow\var_dump($payload, 'Payload');
//        return 'this is github';

        $gitHubEvent = new GithubEvent();
        $gitHubEvent
            ->setReceived(new \DateTime())
            ->setDelivery($delivery)
            ->setEvent($event)
            ->setSignature($signature)
            ->setPayload($payload);

        $this->persistenceManager->add($gitHubEvent);

        $this->response
            ->setStatus(200)
            ->setHeader('Content-Type', 'application/json;charset=utf-8')
            ->setHeader('Request-Id', $gitHubEvent->getIdentifier())
            ->setHeader('Strict-Transport-Security', 'max-age=31536000')
            ->setHeader('Vary', 'Accept-Encoding')
            ->setHeader('X-Content-Type-Options', 'nosniff')
        ;

        return '';

    }

}
