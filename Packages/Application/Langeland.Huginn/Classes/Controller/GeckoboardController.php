<?php

namespace Langeland\Huginn\Controller;

/*
 * This file is part of the Langeland.Huginn package.
 */

use Langeland\Huginn\Service\GitService;
use Langeland\Huginn\Service\JiraService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;


/**
 * Class KlipfolioController
 *
 * Todo:
 *   - Active sprint
 *   - Board overview
 *   - Pull requests
 *
 * @package Langeland\Huginn\Controller
 */
class GeckoboardController extends ActionController
{

    /**
     * @var string
     */
    protected $defaultViewObjectName = \Neos\Flow\Mvc\View\JsonView::class;

    /**
     * @var string
     */
    protected $viewFormatToObjectNameMap = array(
        'html' => \Neos\FluidAdaptor\View\TemplateView::class,
        'json' => \Neos\Flow\Mvc\View\JsonView::class
    );

    /**
     * A list of IANA media types which are supported by this controller
     *
     * @var array
     */
    protected $supportedMediaTypes = array('application/json', 'text/html');


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
     * @return void
     */
    public function indexAction()
    {
        $this->view->assign('foos', array(
            'bar', 'baz'
        ));
    }

    /**
     * http://localhost/langeland.huginn/klipfolio/sprintInfo?teamSlug=webapi
     *
     * @param string $teamSlug
     */
    public function sprintInfoAction(string $teamSlug)
    {
        $team = $this->teamsConfiguration[0];
        $activeSprint = $this->jiraService->getActiveSprint($team['Jira']['board'], $team['Jira']['sprintMatch']);
        $sprintInfo = [
            'item' => [
                [
                    'text' => sprintf(
                        ' %s (ID:%s)',
                        $activeSprint['name'], $activeSprint['id']),
                    'type' => 0
                ]
            ]
        ];

        $this->view->assign('value', $sprintInfo);
    }

    /**
     * @param string $teamSlug
     */
    public function boardOverviewAction(string $teamSlug)
    {




        $team = $this->teamsConfiguration[0];

        $boardConfiguration = $this->jiraService->getBoardConfiguration($team['Jira']['board']);
        $activeSprint = $this->jiraService->getActiveSprint($team['Jira']['board'], $team['Jira']['sprintMatch']);

        $columns = [];
        foreach ($boardConfiguration['columnConfig']['columns'] as $column) {
            $columns[$column['name']] = [];
        }

        $issues = $this->jiraService->getIssuesForSprint($activeSprint['id']);

        /** @var \chobie\Jira\Issue $issue */
        foreach ($issues as $issue) {
            if (array_key_exists($issue->getStatus()['name'], $columns)) {
                $columns[$issue->getStatus()['name']][] = $issue->getKey();
            }
        }

        $rows = array();
        foreach ($columns as $column => $items) {
            $rows[$column]['issues'] = count($items);
        }

        $this->view->assign('value', $rows);


    }

    /**
     * @param string $teamSlug
     */
    public function pullRequestsAction(string $teamSlug)
    {

        $team = $this->teamsConfiguration[0];

        $pullRequests = $this->gitService->getPullsByTeam($team['GitHub']['teams'][0]);
        $rows = array();
        foreach ($pullRequests as $pullRequest) {

            $statuses = $this->gitService->getByPath($pullRequest['statuses_url']);


            $statusStates = array_map(function ($status) {

                switch ($status['state']) {
                    case 'pending':
                        $out = '<comment>o</comment>';
                        break;
                    case 'success':
                        $out = '<info>+</info>';
                        break;
                    case 'failure':
                        $out = '<error>-</error>';
                        break;
                    default:
                        $out = '<question>' . $status['state'] . '</question>';
                }
                return $out;
            }, $statuses);

            $rows[] = array(
                'Nr' => $pullRequest['number'],
                'Title' => $pullRequest['title'],
                'State' => $pullRequest['state'],
                'Repo' => $pullRequest['base']['repo']['full_name'],
                'Created by' => $pullRequest['user']['login'],
                'Updated at' => $pullRequest['updated_at'],
                'Status' => implode(',', $statusStates)
            );
        }
        $this->view->assign('value', $rows);
    }


}
