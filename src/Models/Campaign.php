<?php

namespace Anacreation\CmsEmail\Models;

use Anacreation\Cms\Contracts\ContentGroupInterface;
use Anacreation\Cms\Models\Role;
use Anacreation\Cms\traits\ContentGroup;
use Anacreation\CmsEmail\Jobs\SendEmail;
use Anacreation\Notification\Provider\Contracts\EmailSender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Campaign extends Model implements ContentGroupInterface
{

    use ContentGroup, SoftDeletes;

    protected $table = 'email_campaigns';

    protected $dispatchesEvents = [

    ];

    protected $fillable = [
        'title',
        'template',
        'subject',
        'from_address',
        'from_name',
        'reply_address',
        'is_scheduled',
        'role_id',
        'email_list_id',
        'schedule',
        'has_sent',
    ];

    protected $casts = [
        'has_sent'     => 'Boolean',
        'is_scheduled' => 'Boolean'
    ];

    // Relation
    public function list(): Relation {
        return $this->belongsTo(EmailList::class, 'email_list_id');
    }

    public function role(): Relation {
        return $this->belongsTo(Role::class);
    }

    public function getContentCacheKey(
        string $langCode, string $contentIdentifier
    ): string {
        return "email_campaign_" . $this->id;
    }

    // Access
    public function getRecipientsAttribute(): Collection {

        return optional($this->list)->recipients ??
               optional($this->role)->users ??
               new Collection();
    }

    // Helper 
    public function launch() {

        $recipients = $this->recipients;


        foreach ($recipients as $recipient) {


            $htmlContent = view(config('cms_email.template_folder') . "/" . $this->template)
                ->with([
                    'name'      => $recipient->name,
                    'campaign'  => $this,
                    'recipient' => $recipient,
                    'user'      => $recipient->user,
                ])->render();
            $emailProvider = app()->makeWith(EmailSender::class,
                [
                    'username' => config("cms_email.username"),
                    'password' => config("cms_email.password"),
                ]);


            $emailProvider->from($this->from_name, $this->from_address)
                          ->to($recipient->name, $recipient->email)
                          ->subject($this->subject)
                          ->htmlContent($htmlContent);

            SendEmail::dispatch($emailProvider, $this);
        }

        $this->has_sent = true;
        $this->save();
    }
}
