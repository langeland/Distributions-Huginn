<?php

namespace Langeland\Huginn\Command;

/*
 * This file is part of the Langeland.Huginn package.
 */

use CarlosIO\Geckoboard\Client;
use CarlosIO\Geckoboard\Data\ItemList\Label;
use CarlosIO\Geckoboard\Data\ItemList\Title;
use CarlosIO\Geckoboard\Widgets\ItemList;
use CarlosIO\Geckoboard\Widgets\LineChart;
use Langeland\Huginn\Service\GitService;
use Langeland\Huginn\Service\JiraService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class GeckoboardCommandController extends CommandController
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
    public function pullRequestsCommand($create = false)
    {

        if ($create === true) {
            $schema =
                [
                    ['name' => 'Timestamp', 'type' => 'datetime', 'optional' => true],
                    ['name' => 'Team', 'type' => 'string', 'optional' => false],
                    ['name' => 'Number', 'type' => 'number', 'optional' => true],
                    ['name' => 'Title', 'type' => 'string', 'optional' => true],
                    ['name' => 'Status', 'type' => 'string', 'optional' => true],
                    ['name' => 'Repo', 'type' => 'string', 'optional' => true,]
                ];


            // Create / Update "mydataset" and set "MyDate" as unique field
            \Stefanvinding\Geckoboard\Dataset\Helper::factory(['key' => '7663592038754c615df529492ad9894a'])->createDataset('git_pull_requests', $schema, 'Timestamp');


        }


        foreach ($this->teamsConfiguration as $team) {
            $pullRequests = $this->gitService->getPullsByTeam($team['GitHub']['teams'][0]);


            $records = array();
            foreach ($pullRequests as $pullRequest) {
                $records[] = array(
                    ['name' => 'Timestamp', 'type' => 'datetime', 'value' => date('Y-m-d\TH:i:s\Z')],
                    ['name' => 'Team', 'type' => 'string', 'value' => $team['name']],
                    ['name' => 'Number', 'type' => 'string', 'value' => $pullRequest['number']],
                    ['name' => 'Title', 'type' => 'string', 'value' => $pullRequest['title']],
                    ['name' => 'Status', 'type' => 'string', 'value' => $pullRequest['state']],
                    ['name' => 'Repo', 'type' => 'string', 'value' => $pullRequest['base']['repo']['full_name']]
                );
            }

//            $records =
//                [
//                    [
//                        ['name' => 'Team', 'type' => 'string', 'value' => $team['name']],
//                        ['name' => 'Number', 'type' => 'string', 'value' => $pullRequest['number']],
//                        ['name' => 'Title', 'type' => 'string', 'value' => $pullRequest['title']],
//                        ['name' => 'Status', 'type' => 'string', 'value' => $pullRequest['state']],
//                        ['name' => 'Repo', 'type' => 'string', 'value' => $pullRequest['base']['repo']['full_name']]
//                    ],
//                ];

            // Unique by "MyDate" field
            \Stefanvinding\Geckoboard\Dataset\Helper::factory(['key' => '7663592038754c615df529492ad9894a'])->appendData('git_pull_requests', $records, ['Timestamp']);


            $this->outputLine(PHP_EOL . PHP_EOL);
        }


    }


    /**
     * List all boards in Jira. This only includes boards that the user has permission to view.
     * @param bool $create Create / Update dataset
     */
    public function testCommand()
    {


        $geckoboardClient = new Client();
        $geckoboardClient->setApiKey('7663592038754c615df529492ad9894a');


        $pullRequests = $this->gitService->getPullsByTeam($this->teamsConfiguration[0]['GitHub']['teams'][0]);

//        $widget = new ItemList();
//        $widget->setId('184756-92443500-a524-0135-8805-22000ae1c15b');
//
//        foreach ($pullRequests as $pullRequest) {
//
//            $title = new Title();
//            $title->setText(sprintf('PR# %s: %s', $pullRequest['number'], $pullRequest['title']));
//            $title->setHighlight(true);
//
//            $label = new Label();
//            $label->setName(sprintf('Status: %s', $pullRequest['state']));
//            $label->setColor("green");
//
//            $widget->addItem($title, $label, $pullRequest['base']['repo']['full_name']);
//
//        }
//
//
//        $geckoboardClient->push($widget);






        $widget = new LineChart();
        $widget->setId('184756-f06c2850-a527-0135-6cce-22000be3cb2f');
        $widget->setItems(array(1, 1.23));
        $widget->setColour("ff0000");
        $widget->setAxis(LineChart::DIMENSION_X, array("min", "max"));
        $widget->setAxis(LineChart::DIMENSION_Y, array("bottom", "top"));

        $geckoboardClient->push($widget);


    }


//184756-92443500-a524-0135-8805-22000ae1c15b


}
