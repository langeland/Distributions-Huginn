<?php

namespace Langeland\Huginn\Command;

/*
 * This file is part of the Langeland.Huginn package.
 */

use ExpandOnline\KlipfolioApi\Client;
use ExpandOnline\KlipfolioApi\Klipfolio;
use Langeland\Huginn\Service\GitService;
use Langeland\Huginn\Service\JiraService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class KlipfolioCommandController extends CommandController
{

    /**
     * @var JiraService
     * @Flow\Inject
     */
    protected $jiraService;

    /**
     * @var GitService
     * @Flow\Inject
     */
    protected $gitService;

    /**
     * @var array
     * @Flow\InjectConfiguration(path="Teams")
     */
    protected $teamsConfiguration = [];






    /**
     * List all boards in Jira. This only includes boards that the user has permission to view.
     * @param bool $create Create / Update dataset
     */
    public function testCommand()
    {

        $client = new Client('https://app.klipfolio.com/api', '775f9849e79497572657f4ef976149fa413a42e3', new \Http\Adapter\Guzzle6\Client());
        $klipfolio = new Klipfolio($client);



    }


//184756-92443500-a524-0135-8805-22000ae1c15b


}
