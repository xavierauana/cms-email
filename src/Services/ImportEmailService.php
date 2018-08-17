<?php
/**
 * Author: Xavier Au
 * Date: 17/8/2018
 * Time: 3:13 PM
 */

namespace Anacreation\CmsEmail\Services;


use Anacreation\CmsEmail\Models\EmailList;
use Anacreation\CmsEmail\Models\Recipient;
use Illuminate\Support\Facades\Log;

class ImportEmailService
{

    public function createRecord(
        EmailList $list, array $rows, array $headers,
        &$count
    ): void {

        foreach ($rows as $data) {
            $record = array_combine($headers, $data);
            if ($list->recipients()->whereEmail($record['email'])
                     ->exists()) {
                Log::info("cms email import: {$record['email']} is duplicated for list {$list->title}, skip import");
            } else {

                $record['status'] = Recipient::StatusTypes[config("cms_email.manual_recipient_status",
                    'pending')];

                $list->recipients()->create($record);

                $count++;
            }
        }
    }
}