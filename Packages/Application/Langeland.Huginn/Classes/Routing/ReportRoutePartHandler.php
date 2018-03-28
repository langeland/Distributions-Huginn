<?php

namespace Langeland\Huginn\Routing;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Routing\DynamicRoutePart;


/**
 * Reporting Route Part
 *
 * @api
 */
class ReportRoutePartHandler extends DynamicRoutePart
{

    /**
     * Extracts the node path from the request path.
     *
     * @param string $requestPath The request path to be matched
     * @return string value to match, or an empty string if $requestPath is empty or split string was not found
     */
    protected function findValueToMatch($requestPath)
    {
        return $requestPath;
    }

    /**
     * Checks whether the current URI section matches the configured RegEx pattern.
     *
     * @param string $requestPath value to match, the string to be checked
     * @return boolean TRUE if value could be matched successfully, otherwise FALSE.
     */
    protected function matchValue($requestPath)
    {
        $this->value = $requestPath;
        return TRUE;
    }

    /**
     * Checks whether the route part matches the configured RegEx pattern.
     *
     * @param Query $value
     * @return boolean TRUE if value could be resolved successfully, otherwise FALSE.
     */
    protected function resolveValue($value)
    {
        die('resolveValue '.$value);

//        if (!($value instanceof Query)) {
//            return FALSE;
//        }
//
//        $this->value = implode('/', $value->getRoutingParameters());

        return TRUE;
    }
}