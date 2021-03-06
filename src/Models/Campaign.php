<?php

namespace Anacreation\CmsEmail\Models;

use Anacreation\Cms\Contracts\ContentGroupInterface;
use Anacreation\Cms\Models\Role;
use Anacreation\Cms\traits\ContentGroup;
use Anacreation\CmsEmail\Entities\CampaignDTO;
use Anacreation\CmsEmail\Jobs\PrepareJobs;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class Campaign extends Model implements ContentGroupInterface
{

    use ContentGroup, SoftDeletes;

    protected $table = 'email_campaigns';

    protected $dispatchesEvents = [

    ];

    protected $fillable = [
        'id',
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
        'to_status'
    ];

    protected $casts = [
        'has_sent'     => 'Boolean',
        'is_scheduled' => 'Boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
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
        return "email_campaign_{$this->id}_{$langCode}_{$contentIdentifier}";
    }

    // Access

    public function getToStatusAttribute($value): array {
        if ($data = unserialize($value)) {
            return array_map('trim', $data);
        } else {
            return [];
        }

    }

    public function getRecipientsAttribute(): Collection {

        if ($this->list) {

            return Recipient::whereEmailListId($this->email_list_id)
                            ->whereIn('status', $this->to_status)->get();

        } elseif ($this->role) {
            return $this->role->users;
        } else {
            return new Collection();
        }
    }

    // Mutator
    public function setRoleIdAttribute($value): void {

        $this->attributes['role_id'] = strlen($value) === 0 ? null : $value;
    }

    public function setEmailListIdAttribute($value): void {

        $this->attributes['email_list_id'] = strlen($value) === 0 ? null : $value;
    }

    public function setToStatusAttribute($value): void {
        $this->attributes['to_status'] = serialize(array_map('trim', $value));
    }

    // Helper
    public function launch() {

        Log::info("number of recipients {$this->recipients->count()}");

        $job = new PrepareJobs($this);

        dispatch($job);

        $this->has_sent = true;

        $this->save();
    }

    public function getFormValidationRules(): array {
        return [
            'title'         => 'required',
            'subject'       => 'required',
            'from_name'     => 'required',
            'from_address'  => 'required|email',
            'reply_address' => 'required|email',
            'template'      => [
                'required',
                Rule::in($this->getEmailTemplates())
            ],
            'is_scheduled'  => 'required|boolean',
            'email_list_id' => [
                'required_without:role_id',
                'nullable',
                Rule::in(EmailList::pluck('id')->toArray())
            ],
            'role_id'       => [
                'required_without:email_list_id',
                'nullable',
                Rule::in(Role::pluck('id')->toArray())
            ],
            'schedule'      => 'required_if:is_scheduled,1|nullable|date|after_or_equal:' . Carbon::now()
                                                                                                  ->toDateTimeString(),
        ];
    }

    public function getEmailTemplates(): array {

        $path = resource_path("views/" . config("cms_email.template_folder"));

        $templates = [];
        try {
            $templates = scandir($path);
            $templates = sanitizeFileNames(compact("templates"));
        } catch (\Exception $e) {

        }

        return $templates['templates'] ?? [];
    }

    public function unsubscribeLink(Recipient $recipient = null): string {
        if ($recipient === null) {
            return "";
        }

        $token = $this->createToken($recipient);

        return route('lists.unsubscribe',
            [$this->email_list_id, 'token' => $token]);

    }

    public function confirmLink(Recipient $recipient = null): string {
        if ($recipient === null) {
            return "";
        }
        $token = $this->createToken($recipient);

        return route('lists.confirm',
            [$this->email_list_id, 'token' => $token]);
    }

    public function getCampaignWebLink(Recipient $recipient = null): string {

        if ($recipient) {

            $token = $this->createToken($recipient);

            return route('campaign.web', [$this, 'token' => $token]);
        }

        return route('campaign.web', $this);
    }

    /**
     * @param \Anacreation\CmsEmail\Models\Recipient $recipient
     * @return string
     */
    private function createToken(Recipient $recipient): string {
        $data = [
            'list_id' => $this->email_list_id ?? 0,
            'email'   => $recipient->email
        ];

        $token = encrypt($data);

        return $token;
    }

}
