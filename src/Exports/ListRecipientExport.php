<?php

namespace Anacreation\CmsEmail\Exports;

use Anacreation\CmsEmail\Models\EmailList;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ListRecipientExport implements FromCollection, WithHeadings
{
    /**
     * @var \Anacreation\CmsEmail\Models\EmailList
     */
    private $list;

    /**
     * ListRecipientExport constructor.
     * @param \Anacreation\CmsEmail\Models\EmailList $list
     */
    public function __construct(EmailList $list) {
        $this->list = $list;
    }

    public function headings(): array {
        return [
            'id',
            'Name',
            'Email',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection {
        return $this->list->recipients->map(function ($recipient) {
            return [
                'id'         => $recipient->id,
                'Name'       => $recipient->name,
                'Email'      => $recipient->email,
                'Status'     => $recipient->status,
                'Created At' => $recipient->created_at,
                'Updated At' => $recipient->updated_at,
            ];
        });
    }
}
