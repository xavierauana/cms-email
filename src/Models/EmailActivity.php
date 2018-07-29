<?php

namespace Anacreation\CmsEmail\Models;

use Anacreation\Cms\Contracts\ContentGroupInterface;
use Anacreation\Cms\traits\ContentGroup;
use Anacreation\CmsEmail\Mail\SimpleMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Mail;

class EmailActivity extends Model implements ContentGroupInterface
{

    use ContentGroup;

    protected $table = 'email_activities';

    protected $fillable = [
        'title',
        'confirm_opt_in',
        'has_welcome_message',
        'has_goodbye_message'
    ];

    protected $casts = [
        'confirm_opt_in'      => 'Boolean',
        'has_welcome_message' => 'Boolean',
        'has_goodbye_message' => 'Boolean'
    ];

    // Relation
    public function recipients(): Relation {
        return $this->hasMany(Recipient::class);
    }

    public function getContentCacheKey(
        string $langCode, string $contentIdentifier
    ): string {
        return "email_campaign_" . $this->id;
    }

    public function launch() {
        $recipients = $this->recipients;

        foreach ($recipients as $recipient) {
            Mail::send(new SimpleMail($this, $recipient));
        }

        $this->has_sent = true;
        $this->save();
    }

}
