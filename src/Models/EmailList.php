<?php

namespace Anacreation\CmsEmail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailList extends Model
{

    //    use ContentGroup;

    use SoftDeletes;

    protected $table = 'email_lists';

    protected $fillable = [
        'title',
        'confirm_opt_in',
        'has_welcome_message',
        'has_goodbye_message',
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

    public function updateRecipientStateWithToken(string $token, string $status
    ): ?Recipient {

        $data = decrypt($token);

        if (isset($data['list_id']) and $data['email']) {
            if ((int)$data['list_id'] === $this->id) {


                if ($recipient = $this->recipients()
                                      ->whereEmail($data['email'])
                                      ->first()) {
                    $recipient->update([
                        'status' => $status
                    ]);
                }

                return $recipient;
            }
        }


        return null;
    }
}
