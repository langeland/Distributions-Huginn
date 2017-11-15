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

    /**
     * @var \DateTime
     */
    protected $received;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     */
    protected $delivery;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     */
    protected $event;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     */
    protected $signature;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $payload;

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return GithubEvent
     */
    public function setIdentifier(string $identifier): GithubEvent
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReceived(): \DateTime
    {
        return $this->received;
    }

    /**
     * @param \DateTime $received
     * @return GithubEvent
     */
    public function setReceived(\DateTime $received): GithubEvent
    {
        $this->received = $received;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param null|string $delivery
     * @return GithubEvent
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param null|string $event
     * @return GithubEvent
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param null|string $signature
     * @return GithubEvent
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
        return $this;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     * @return GithubEvent
     */
    public function setPayload(array $payload): GithubEvent
    {
        $this->payload = $payload;
        return $this;
    }

}
