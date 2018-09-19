<?php

namespace Anacreation\CmsEmail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class CampaignStatus extends Model
{
    const Status = [
        'none'        => 'none',
        'to_provider' => 'to_provider',
        'processed'   => 'processed',
        'dropped'     => 'dropped',
        'delivered'   => 'delivered',
        'deferred'    => 'deferred',
        'bounce'      => 'bounce',
    ];

    protected $table = "campaign_status";

    protected $fillable = [
        'recipient_id',
        'campaign_id',
        'message_id',
        'status',
        'reason',
    ];

    // Relation
    public function recipient(): Relation {
        return $this->belongsTo(Recipient::class);
    }

    public function campaign(): Relation {
        return $this->belongsTo(Campaign::class);
    }

}
