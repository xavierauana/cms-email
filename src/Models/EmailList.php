<?php

namespace Anacreation\CmsEmail\Models;

use Anacreation\Cms\traits\ContentGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class EmailList extends Model
{

    use ContentGroup;

    protected $table = 'email_lists';

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
}
