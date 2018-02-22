<?php

namespace Langeland\Huginn\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Langeland\Huginn\Service\JiraService;


class ValidationController extends ActionController
{

    /**
     * @var JiraService
     * @Flow\Inject
     */
    protected $jiraService;

    /**
     * Checks branch naming and if jira issue exists
     * @return string
     */
    public function isValidBranchAction()
    {
        $branchName = $this->request->getArgument('branchName');
        $issueKey = $this->request->getArgument('issueKey');
        if(preg_match('/^(experimental)$/', $branchName)){
            $this->response
                ->setStatus(200);
            return "";
        }
        elseif (!preg_match('/^(feature|task|bugfix|hotfix)$/', $branchName)) {

            $this->response
                ->setStatus(400);
            return "ERROR: Invalid branch: '$branchName' \nBranch must begin with 'feature/', 'task/', 'bugfix/', 'hotfix/' or 'experimental' and be all lowercase";

        } elseif (empty($issueKey) || !$this->jiraService->hasIssue($issueKey)) {

            $this->response
                ->setStatus(404);
            return "ERROR: Jira issue not found with key: '$issueKey' \nBranch must end with a issue key corresponding to an existing issue in Jira";

        }

        $this->response
            ->setStatus(200);

        return "";

    }


}