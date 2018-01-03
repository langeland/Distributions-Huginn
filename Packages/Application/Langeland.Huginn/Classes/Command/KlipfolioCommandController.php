<?php

namespace Langeland\Huginn\Command;

/*
 * This file is part of the Langeland.Huginn package.
 */

use ExpandOnline\KlipfolioApi\Client;
use ExpandOnline\KlipfolioApi\Connector\User\UserConnector;
use ExpandOnline\KlipfolioApi\Klipfolio;
use Langeland\Huginn\Service\GitService;
use Langeland\Huginn\Service\JiraService;
use Langeland\Huginn\Service\KlipfolioService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class KlipfolioCommandController extends CommandController
{

    /**
     * @var KlipfolioService
     * @Flow\Inject
     */
    protected $klipfolioService;


    /**
     *
     *
     * @see http://apidocs.klipfolio.com/reference
     */
    public function updateAllCommand()
    {

        $this->outputLine('=============================================================================');
        $this->outputLine('====  Generating list of active sprints');
        $this->outputLine('=============================================================================');
        $this->klipfolioService->sprintInfoUpdate();

        $this->outputLine('=============================================================================');
        $this->outputLine('====  Generating list of active boards');
        $this->outputLine('=============================================================================');
        $this->klipfolioService->boardInfoUpdate();

//        $this->outputLine('=============================================================================');
//        $this->outputLine('====  Generating list of all active issues');
//        $this->outputLine('=============================================================================');
//        $this->klipfolioService->issuesUpdate();

        $this->outputLine('=============================================================================');
        $this->outputLine('====  Generating list of pull requests');
        $this->outputLine('=============================================================================');
        $this->klipfolioService->pullRequestsUpdate();

    }

    /**
     *
     *
     * @see http://apidocs.klipfolio.com/reference
     */
    public function sprintInfoCommand()
    {

        $this->outputLine('=============================================================================');
        $this->outputLine('====  Generating list of active sprints');
        $this->outputLine('=============================================================================');

        $this->klipfolioService->sprintInfoUpdate();
    }

    /**
     *
     *
     * @see http://apidocs.klipfolio.com/reference
     */
    public function boardInfoCommand()
    {

        $this->outputLine('=============================================================================');
        $this->outputLine('====  Generating list of active boards');
        $this->outputLine('=============================================================================');

        $this->klipfolioService->boardInfoUpdate();
    }

    /**
     *
     *
     * @see http://apidocs.klipfolio.com/reference
     */
    public function issuesCommand()
    {

        $this->outputLine('=============================================================================');
        $this->outputLine('====  Generating list of all active issues');
        $this->outputLine('=============================================================================');

        $this->klipfolioService->issuesUpdate();
    }

    /**
     *
     *
     * @see http://apidocs.klipfolio.com/reference
     */
    public function pullRequestsCommand()
    {

        $this->outputLine('=============================================================================');
        $this->outputLine('====  Generating list of pull requests');
        $this->outputLine('=============================================================================');

        $this->klipfolioService->pullRequestsUpdate();
    }



}
