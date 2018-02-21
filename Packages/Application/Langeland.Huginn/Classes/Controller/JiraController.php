<?php

namespace Langeland\Huginn\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Langeland\Huginn\Service\JiraService;


class JiraController extends ActionController
{

    /**
     * @var JiraService
     * @Flow\Inject
     */
    protected $jiraService;

    /**
     * Gets an issue and return 200 if found 404 if not found
     *
     * @param string $branchName
     * @return string
     */
    public function checkBranchAction($branchName)
    {

        return 'OK: ' . $branchName;
//
//        if ($this->jiraService->getIssueExists($issueKey)) {
//            $this->response
//                ->setStatus(200);
//        } else {
//            $this->response
//                ->setStatus(404);
//        }
//
//        return '';
    }

}