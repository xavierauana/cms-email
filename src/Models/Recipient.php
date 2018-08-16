<?php

namespace Anacreation\CmsEmail\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        return $this->belongsTo(EmailList::class);
    }

    public function user(): Relation {
        return $this->belongsTo(User::class);
    }

    // Mutator

    public function setTokenAttribute() {
        $data = [
            'list_id' => $this->email_list_id,
            'email'   => $this->email,
        ];
        $this->attributes['token'] = encrypt($data);
    }
}
