<?php

namespace Langeland\Huginn\Command;

/*
 * This file is part of the Langeland.Huginn package.
 */

use function Clue\StreamFilter\fun;
use Langeland\Huginn\Service\GitService;
use Langeland\Huginn\Service\JiraService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class StatsCommandController extends CommandController
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
     */
    public function boardsCommand()
    {
        $boards = $this->jiraService->getBoards();

        $rows = array();
        foreach ($boards as $board) {
            $rows[] = array($board['id'], $board['name'], $board['type']);
        }

        $this->outputLine('Board overview');
        $this->output->outputTable($rows, array('ID', 'Name', 'Type'));
    }


    /**
     * List all sprints from a board, for a given board Id. This only includes sprints that the user has permission to view.
     *
     * @param integer $boardId
     */
    public function sprintsCommand($boardId)
    {
        $sprints = $this->jiraService->getSprints($boardId);
        $rows = array();
        foreach ($sprints as $sprint) {
            $rows[] = array($sprint['id'], $sprint['name'], $sprint['state'], '-', '-');
        }
        $this->outputLine('Sprint overview for board: ' . $boardId);
        $this->output->outputTable($rows, array('ID', 'Name', 'State', 'startDate', 'endDate'));
    }


    /**
     *
     */
    public function gitCommand()
    {

        foreach ($this->teamsConfiguration as $team) {
            $this->outputLine('=============================================================================');
            $this->outputLine('====  ' . $team['name']);
            $this->outputLine('=============================================================================');

            /**
             * List of team members
             */
//            $members = $this->gitService->getMembersByTeams($team['GitHub']['teams']);
//            $rows = array();
//            foreach ($members as $member) {
//                $rows[] = array('Name' => $member['name']);
//            }
//            $this->outputTable($rows, array_keys($rows[0]), 'Team members');


            /**
             * List of team repositories
             */
//            $repositories = $this->gitService->getReposByTeam($team['GitHub']['teams'][0]);
//            $rows = array();
//            foreach ($repositories as $repository) {
//                $rows[] = array(
//                    'Name' => $repository['full_name'],
//                    '' => $repository['description']
//
//                );
//            }
//            $this->outputTable($rows, array_keys($rows[0]), 'Team repositories');

            /**
             * List of pull requests
             */
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
            $this->outputTable($rows, array_keys($rows[0]), 'Open pull requests');


            /**
             * List of commits
             */
//            $commits = $this->gitService->getCommitsByTeam($team['GitHub']['teams'][0]);
//
//            $rows = array();
//            foreach ($commits as $commit) {
//                $rows[] = array(
//                    'Message' => strtok($commit['commit']['message'], "\n"),
//                    'Author' => $commit['commit']['author']['name']
//                );
//            }
//            $this->output->outputTable($rows, array_keys($rows[0]));

            $this->outputLine(PHP_EOL . PHP_EOL);
        }


    }


    /**
     * An example command
     *
     * The comment of this command method is also used for TYPO3 Flow's help screens. The first line should give a very short
     * summary about what the command does. Then, after an empty line, you should explain in more detail what the command
     * does. You might also give some usage example.
     *
     * It is important to document the parameters with param tags, because that information will also appear in the help
     * screen.
     *
     * @return void
     */
    public function jiraCommand()
    {
        foreach ($this->teamsConfiguration as $team) {
            $this->outputLine('=============================================================================');
            $this->outputLine('====  ' . $team['name']);
            $this->outputLine('=============================================================================');

            $boardConfiguration = $this->jiraService->getBoardConfiguration($team['Jira']['board']);
            $activeSprint = $this->jiraService->getActiveSprint($team['Jira']['board'], $team['Jira']['sprintMatch']);

            $columns = [];
            foreach ($boardConfiguration['columnConfig']['columns'] as $column) {
                $columns[$column['name']] = [];
            }

            $this->outputLine('<info>Active sprint: %s (ID:%s)</info>', [$activeSprint['name'], $activeSprint['id']]);
            $this->outputFormatted($activeSprint['goal'], [], 2);

            $issues = $this->jiraService->getIssuesForSprint($activeSprint['id']);

            /** @var \chobie\Jira\Issue $issue */
            foreach ($issues as $issue) {
                if (array_key_exists($issue->getStatus()['name'], $columns)) {
                    $columns[$issue->getStatus()['name']][] = $issue->getKey();
                }
            }

            $rows = array();
            foreach ($columns as $item) {
                $rows[] = count($item);
            }
            $this->outputTable(array($rows), array_keys($columns), 'Board overview');
            $this->outputLine(PHP_EOL . PHP_EOL);
        }
    }

    /**
     * An example command
     *
     * The comment of this command method is also used for TYPO3 Flow's help screens. The first line should give a very short
     * summary about what the command does. Then, after an empty line, you should explain in more detail what the command
     * does. You might also give some usage example.
     *
     * It is important to document the parameters with param tags, because that information will also appear in the help
     * screen.
     *
     * @return void
     */
    public function overviewCommand()
    {
        foreach ($this->teamsConfiguration as $team) {
            $this->outputLine('=============================================================================');
            $this->outputLine('====  ' . $team['name']);
            $this->outputLine('=============================================================================');

            $boardConfiguration = $this->jiraService->getBoardConfiguration($team['Jira']['board']);
            $activeSprint = $this->jiraService->getActiveSprint($team['Jira']['board'], $team['Jira']['sprintMatch']);

            $columns = [];
            foreach ($boardConfiguration['columnConfig']['columns'] as $column) {
                $columns[$column['name']] = [];
            }

            $this->outputLine('<info>Active sprint: %s (ID:%s)</info>', [$activeSprint['name'], $activeSprint['id']]);
            $this->outputFormatted($activeSprint['goal'], [], 2);

            $issues = $this->jiraService->getIssuesForSprint($activeSprint['id']);

            /**
             * Board overview
             */
            /** @var \chobie\Jira\Issue $issue */
            foreach ($issues as $issue) {
                if (array_key_exists($issue->getStatus()['name'], $columns)) {
                    $columns[$issue->getStatus()['name']][] = $issue->getKey();
                }
            }

            $rows = array();
            foreach ($columns as $item) {
                $rows[] = count($item);
            }
            $this->outputTable(array($rows), array_keys($columns), 'Board overview');


            /**
             * List of pull requests
             */
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
            $this->outputTable($rows, array_keys($rows[0]), 'Open pull requests');

            $this->outputLine(PHP_EOL . PHP_EOL);
        }
    }


    protected function outputTable($rows, $headers = null, $title = null)
    {

        if ($title !== null) {
            if ($headers !== null) {
                array_unshift($rows, $headers, new \Symfony\Component\Console\Helper\TableSeparator());
                $headers = null;
            }

            $hr = new \Symfony\Component\Console\Helper\TableCell(sprintf('<info>%s</info>', $title), array('colspan' => count($rows[0])));
            array_unshift($rows, array($hr), new \Symfony\Component\Console\Helper\TableSeparator());

        }

        $this->output->outputTable($rows, $headers);
    }


}
