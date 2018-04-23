<?php

namespace Langeland\Huginn\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Langeland\Huginn\Service\JiraService;


class ValidationController extends ActionController
{

    /**
     * @var string
     */
    protected $defaultViewObjectName = \Neos\Flow\Mvc\View\JsonView::class;

    /**
     * @var JiraService
     * @Flow\Inject
     */
    protected $jiraService;

    /**
     * Checks branch naming and if jira issue exists
     * @param string $branchName
     * @return void
     */
    public function isValidBranchAction(string $branchName)
    {

        $branchParts = explode('/', $branchName, 3);

        if (count($branchParts) <= 1) {
            $this->view->assign('value', [
                'status' => 'ERROR',
                'description' => 'Branch name must contain a minimum of two parts',
                'branchName' => $branchName
            ]);
            return;
        }

        if (in_array($branchParts[0], ['feature', 'task', 'bugfix', 'hotfix', 'experimental']) === false) {
            $this->response->setStatus(400);
            $this->view->assign('value', [
                'status' => 'ERROR',
                'description' => 'Invalid branch: ' . $branchParts[0] . '. Branch must begin with feature/, task/, bugfix/, hotfix/ or experimental and be all lowercase',
                'branchName' => $branchName
            ]);
            return;
        }

        if ($branchParts[0] === 'experimental') {
            $this->response->setStatus(200);
            $this->view->assign('value', ['status' => 'OK', 'branchName' => $branchName]);
            return;
        }

        if ($this->jiraService->hasIssue($branchParts[1]) === false) {

            $this->response->setStatus(404);
            $this->view->assign('value', [
                'status' => 'ERROR',
                'description' => 'Jira issue not found with key: ' . $branchParts[1] . '. Branch must end with a issue key corresponding to an existing issue in Jira',
                'branchName' => $branchName
            ]);
            return;

        }

        $this->response->setStatus(200);
        $this->view->assign('value', ['status' => 'OK']);

    }

}