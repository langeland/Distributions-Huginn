<?php

namespace Langeland\Huginn\Service;

use chobie\Jira\Api;
use chobie\Jira\Api\Authentication\Basic;
use chobie\Jira\Issues\Walker;
use Langeland\JiraDex\Domain\Model\Allocation;
use Langeland\JiraDex\Domain\Model\Sprint;
use Langeland\JiraDex\Domain\Model\Team;
use Langeland\JiraDex\Domain\Model\TeamMember;
use Langeland\JiraDex\Domain\Repository\AllocationRepository;
use Langeland\JiraDex\Domain\Repository\SprintRepository;
use Neos\Flow\Annotations as Flow;

/**
 * Class SprintService
 * @package Langeland\Huginn\Service
 *
 * @Flow\Scope("singleton")
 */
class SprintService
{

    /**
     * @var JiraService
     * @Flow\Inject
     */
    protected $jiraService;


    public function getActiveSprint($board, $pattern) {



    }




}