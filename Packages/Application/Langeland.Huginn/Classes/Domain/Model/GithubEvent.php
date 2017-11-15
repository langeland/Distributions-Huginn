<?php
namespace Langeland\Huginn\Domain\Model;

/*
 * This file is part of the Langeland.Huginn package.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class GithubEvent
{

    /**
     * @var string
     *
     * @Flow\Identity
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $identifier;



}
