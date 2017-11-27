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
     *
     *
     * @see http://apidocs.klipfolio.com/reference
     */
    public function sprintInfoCommand()
    {

        $this->outputLine('=============================================================================');
        $this->outputLine('====  Generating list of active sprints');
        $this->outputLine('=============================================================================');

        $rows = array();
        foreach ($this->teamsConfiguration as $teamSlug => $team) {
            $this->outputLine('===  ' . $team['name']);

            $activeSprint = $this->jiraService->getActiveSprint($team['Jira']['board'], $team['Jira']['sprintMatch']);
            $activeSprint['goal'] = nl2br($activeSprint['goal']);
            $activeSprint['_teams'] = $teamSlug;
            $rows[$teamSlug] = $activeSprint;
        }

        \Neos\Flow\var_dump($rows);
        $this->updateJsonDatasourceInstances('6c01cb211d5c68d9981d78cf11973ce0', $rows);
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

        $rows = array();
        foreach ($this->teamsConfiguration as $teamSlug => $team) {
            $this->outputLine('===  ' . $team['name']);


            $boardConfiguration = $this->jiraService->getBoardConfiguration($team['Jira']['board']);
            $activeSprint = $this->jiraService->getActiveSprint($team['Jira']['board'], $team['Jira']['sprintMatch']);

            foreach ($boardConfiguration['columnConfig']['columns'] as $column) {
                $rows[$teamSlug][ str_replace(' ', '', $column['name'] )] = [
                    'label' => $column['name'],
                    'issues' => [],
                    'issue_count' => 0,
                    'story_points' => 0
                ];
            }

            $issues = $this->jiraService->getIssuesForSprint($activeSprint['id']);

            /** @var \chobie\Jira\Issue $issue */
            foreach ($issues as $issue) {
                if (array_key_exists(str_replace(' ', '', $issue->getStatus()['name'] ), $rows[$teamSlug])) {
                    $rows[$teamSlug][str_replace(' ', '', $issue->getStatus()['name'] )]['issues'][] = $issue->getKey();
                    $rows[$teamSlug][str_replace(' ', '', $issue->getStatus()['name'] )]['story_points'] += $issue->get('Story Points');
                }
            }

            foreach ($rows[$teamSlug] as $columnName => $column) {
                $rows[$teamSlug][$columnName]['issue_count'] = count($column['issues']);
            }
        }
        \Neos\Flow\var_dump($rows);
        $this->updateJsonDatasourceInstances('ce4b62c973387f8338d47156d3cee5c2', $rows);

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

        $rows = array();
        foreach ($this->teamsConfiguration as $teamSlug => $team) {
            $this->outputLine('===  ' . $team['name']);

            $activeSprint = $this->jiraService->getActiveSprint($team['Jira']['board'], $team['Jira']['sprintMatch']);

            $issues = $this->jiraService->getIssuesForSprint($activeSprint['id']);

            /** @var \chobie\Jira\Issue $issue */
            foreach ($issues as $issue) {
                $fields = $issue->getFields();
                $fields['_teams'] = $teamSlug;
                $rows[] = $fields;
            }
        }

        \Neos\Flow\var_dump($rows);

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

        $rows = array();
        foreach ($this->teamsConfiguration as $teamSlug => $team) {
            $this->outputLine('===  ' . $team['name']);

            $pullRequests = $this->gitService->getPullsByTeam($team['GitHub']['teams'][0]);
            foreach ($pullRequests as $pullRequest) {
                $this->outputLine('====  Adding: ' . $pullRequest['html_url']);

                if (array_key_exists($pullRequest['id'], $rows)) {
                    $rows[$pullRequest['id']]['_teams'] .= ',' . $teamSlug;
                    continue;
                }

                $commitStatus = $this->gitService->getByPath(
                    sprintf(
                        '/repos/drdk/%s/commits/%s/status',
                        $pullRequest['head']['repo']['name'],
                        $pullRequest['head']['sha']
                    )
                );

                $rows[$pullRequest['id']] = array(
                    'Nr' => $pullRequest['number'],
                    'Title' => $pullRequest['title'],
                    'State' => $pullRequest['state'],
                    'Repo' => $pullRequest['base']['repo']['full_name'],
                    'Created by' => $pullRequest['user']['login'],
                    'Updated at' => $pullRequest['updated_at'],

                    'mergeable' => $pullRequest['mergeable'],
                    'rebaseable' => $pullRequest['rebaseable'],
                    'mergeable_state' => $pullRequest['mergeable_state'],
                    'review_comments' => $pullRequest['review_comments'],
                    'commit_state' => $commitStatus['state'],

                    '_teams' => $teamSlug
                );


            }
        }
        \Neos\Flow\var_dump($rows);
        $this->updateJsonDatasourceInstances('7359e2edcd8c8a1aab7daa27427ac02b', $rows);
    }


    //184756-92443500-a524-0135-8805-22000ae1c15b


    protected function updateJsonDatasourceInstances($datasourceInstances, $rows)
    {
        $apiKey = '775f9849e79497572657f4ef976149fa413a42e3';
        $tmpfname = tempnam("/tmp", "FOO");

        file_put_contents($tmpfname, \GuzzleHttp\json_encode($rows));


        $command = sprintf(
            'curl https://app.klipfolio.com/api/1/datasource-instances/%s/data -X PUT --upload-file %s --header "kf-api-key:%s"',
            $datasourceInstances,
            $tmpfname,
            $apiKey
        );
        exec($command);
        unlink($tmpfname);
    }


    protected function updateDatasourceInstances($datasourceInstances, $rows)
    {
        $apiKey = '775f9849e79497572657f4ef976149fa413a42e3';
        $tmpfname = tempnam("/tmp", "FOO");

        $fh = fopen($tmpfname, 'w');
        fputcsv($fh, array_keys(current($rows)));
        foreach ($rows as $row) {
            fputcsv($fh, $row);
        }
        fclose($fh);

        $command = sprintf(
            'curl https://app.klipfolio.com/api/1/datasource-instances/%s/data -X PUT --upload-file %s --header "kf-api-key:%s"',
            $datasourceInstances,
            $tmpfname,
            $apiKey
        );
        exec($command);
        unlink($tmpfname);
    }


}
