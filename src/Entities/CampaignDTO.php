<?php
/**
 * Author: Xavier Au
 * Date: 1/10/2018
 * Time: 2:35 PM
 */

namespace Anacreation\CmsEmail\Entities;


use Anacreation\CmsEmail\Models\Campaign;

class CampaignDTO
{
    private $campaign;

    function __construct(Campaign $campaign) {
        $this->campaign = $campaign;
    }

    public function __get($property) {
        return $this->campaign->{$property};
    }

    public function __call($method, $arguments) {
        return $this->campaign->{$method}(
            $arguments[0] ?? null,
            $arguments[1] ?? null,
            $arguments[2] ?? null
        );
    }

}