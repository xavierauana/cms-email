<?php

namespace Anacreation\CmsEmail\Jobs;

use Anacreation\CmsEmail\Models\EmailList;
use Anacreation\CmsEmail\Services\ImportEmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var \Anacreation\CmsEmail\Models\EmailList
     */
    private $list;
    /**
     * @var array
     */
    private $headers;
    /**
     * @var array
     */
    private $rows;

    /**
     * Create a new job instance.
     *
     * @param EmailList                              $list
     * @param                                        $rows
     */
    public function __construct(EmailList $list, array $headers, array $rows) {
        //
        $this->list = $list;
        $this->headers = $headers;
        $this->rows = $rows;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $service = new ImportEmailService();

        $count = 0;
        $service->createRecord($this->list, $this->rows, $this->headers,
            $count);

    }
}
