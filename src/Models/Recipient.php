<?php

namespace Anacreation\CmsEmail\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;

class Recipient extends Model
{
    protected $table = 'email_list_recipients';

    protected $fillable = [
        'name',
        'email',
        'status',
        'user_id',
        'email_list_id',
    ];

    public const StatusTypes = [
        'pending'      => 'pending',
        'unsubscribed' => 'unsubscribed',
        'confirmed'    => 'confirmed',
    ];

    // Relation
    public function list(): Relation {
        return $this->belongsTo(EmailList::class, 'email_list_id');
    }

    public function user(): Relation {
        return $this->belongsTo(User::class);
    }

    public function campaignStatus(): Relation {
        return $this->hasMany(CampaignStatus::class);
    }

    public function activities(): Relation {
        return $this->hasMany(EmailActivity::class);
    }

    // Mutator

    public function setTokenAttribute() {
        $data = [
            'list_id' => $this->email_list_id,
            'email'   => $this->email,
        ];
        $this->attributes['token'] = encrypt($data);
    }

    // helper

    public static function getStoreValidationRules(int $list_id): array {
        return [
            'name'  => 'nullable',
            'email' => [
                'required',
                'email',
                Rule::unique('email_list_recipients')->where(function ($query
                ) use ($list_id) {
                    return $query->where('email_list_id', $list_id);
                })
            ],
        ];
    }

}
