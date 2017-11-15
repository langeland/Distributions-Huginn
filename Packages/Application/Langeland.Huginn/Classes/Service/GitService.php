<?php

namespace Langeland\Huginn\Service;


use Github\HttpClient\Message\ResponseMediator;
use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\Annotations as Flow;

/**
 * Class GitService
 * @package Langeland\Huginn\Service
 **
 * @Flow\Scope("singleton")
 */
class GitService
{

    /**
     * @var \Github\Client
     */
    protected $github;

    /**
     * @var \Github\ResultPager
     */
    protected $paginator;

    /**
     * @var VariableFrontend
     * @Flow\Inject
     */
    protected $apiCache;

    /**
     * @var string
     * @Flow\InjectConfiguration(path="GitHub.Api.Authentication.authToken")
     */
    protected $authToken;

    /**
     *
     */
    public function initializeObject()
    {
        $this->github = new \Github\Client();
        $this->paginator = new \Github\ResultPager($this->github);

        if (is_string($this->authToken)) {
            $this->github->authenticate($this->authToken, null, \Github\Client::AUTH_HTTP_TOKEN);
        }
    }

    public function getReposByTeam($teamSlug)
    {
        $callIdentifier = 'getReposByTeam' . sha1(json_encode(func_get_args()));
        if (!$this->apiCache->has($callIdentifier)) {
            $teams = $this->github->organization()->teams()->all('drdk');
            foreach ($teams as $team) {
                if ($team['slug'] == $teamSlug) {
                    $foundTeam = $team;
                    break;
                }
            }

            $teamsApi = $this->github->organizations()->teams();
            $repositories = $this->paginator->fetchAll($teamsApi, 'repositories', [$foundTeam['id']]);

            $this->apiCache->set($callIdentifier, $repositories);
        } else {
            $repositories = $this->apiCache->get($callIdentifier);
        }
        return $repositories;
    }

    public function getReposByTeams(array $teamSlugs)
    {
        $repositories = array();
        foreach ($teamSlugs as $teamSlug) {
            $repositories = array_merge($repositories, $this->getReposByTeam($teamSlug));
        }

        return $repositories;
    }


    public function getMembersByTeam($teamSlug)
    {

        $callIdentifier = 'getMembersByTeam' . sha1(json_encode(func_get_args()));

        if (!$this->apiCache->has($callIdentifier)) {
            $teams = $this->github->organization()->teams()->all('drdk');
            foreach ($teams as $team) {
                if ($team['slug'] == $teamSlug) {
                    $foundTeam = $team;
                    break;
                }
            }
            $members = $this->github->organization()->teams()->members($foundTeam['id']);

            $membersExtended = [];
            foreach ($members as $member) {
                $membersExtended[] = $this->github->user()->show($member['login']);
            }


            $this->apiCache->set($callIdentifier, $membersExtended);
        } else {
            $membersExtended = $this->apiCache->get($callIdentifier);
        }

        return $membersExtended;
    }

    public function getMembersByTeams(array $teamSlugs)
    {
        $membersExtended = array();
        foreach ($teamSlugs as $teamSlug) {
            $membersExtended = array_merge($membersExtended, $this->getMembersByTeam($teamSlug));
        }

        return $membersExtended;
    }

    public function getPullsByTeam($teamSlug)
    {
        $callIdentifier = 'getPullsByTeam' . sha1(json_encode(func_get_args()));

        if (!$this->apiCache->has($callIdentifier)) {
            $repositories = $this->getReposByTeam($teamSlug);
            $pullRequests = array();
            foreach ($repositories as $repository) {
                $repositoryPullRequests = $this->github->pullRequests()->all('drdk', $repository['name']);
                if ($repositoryPullRequests !== array()) {
                    $pullRequests = array_merge($pullRequests, $repositoryPullRequests);
                }
            }
            $this->apiCache->set($callIdentifier, $pullRequests);
        } else {
            $pullRequests = $this->apiCache->get($callIdentifier);
        }

        return $pullRequests;
    }

    public function getPullsByTeams(array $teamSlugs)
    {
        $pullRequests = array();
        foreach ($teamSlugs as $teamSlug) {
            $pullRequests = array_merge($pullRequests, $this->getPullsByTeam($teamSlug));
        }

        return $pullRequests;
    }

    public function getCommitsByTeam($teamSlug)
    {
        $memberLonins = array_column($this->getMembersByTeam($teamSlug), 'login');

//        \Neos\Flow\var_dump($memberLonins);die();

        $repositories = $this->getReposByTeam($teamSlug);

        $commits = array();
        foreach ($repositories as $repository) {
            $commits = array_merge($commits, $this->github->repository()->commits()->all('drdk', $repository['name'], []));
        }

        \Neos\Flow\var_dump(count($commits), 'all commits');

        $commits = array_filter($commits, function ($commit) use ($memberLonins) {
            return (array_key_exists($commit['author']['login'], $memberLonins));
        });

        \Neos\Flow\var_dump(count($commits), 'filters commits');


        return $commits;
    }


    public function getByPath($path)
    {
        $response = $this->github->getHttpClient()->get($path);
        $content = ResponseMediator::getContent($response);
        return $content;
    }

}