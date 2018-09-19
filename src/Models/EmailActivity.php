<?php

namespace Anacreation\CmsEmail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class EmailActivity extends Model
{
    const Activities = [
        'open'       => 'open',
        'click'      => 'click',
        'spamreport' => 'spamreport',
    ];

    protected $fillable = [
        'ip',
        'url',
        'activity',
        'timestamp',
        'user_agent',
        'message_id',
        'campaign_id',
        'recipient_id',
    ];

    // Relation
    public function campaign(): Relation {
        return $this->belongsTo(Campaign::class);
    }

    public function recipient(): Relation {
        return $this->belongsTo(Recipient::class);
    }
}
