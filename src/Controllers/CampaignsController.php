<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\CmsEmail\Jobs\SendEmail;
use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\CampaignStatus;
use Anacreation\CmsEmail\Models\EmailList;
use Anacreation\CmsEmail\Models\Recipient;
use Anacreation\CmsEmail\Services\CampaignService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //        $this->authorize('index', $language);

        $campaigns = Campaign::all();

        return view('cms_email::campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Anacreation\CmsEmail\Models\Campaign $campaign
     * @return Response
     */
    public function create(Campaign $campaign) {
        //        $this->authorize('create', $language);


        $templates = $campaign->getEmailTemplates();

        $recipientStatus = Recipient::StatusTypes;


        return view('cms_email::campaigns.create',
            compact('templates', 'recipientStatus'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request                                       $request
     * @param \Anacreation\CmsEmail\Services\CampaignService $service
     * @return Response
     */
    public function store(Request $request, CampaignService $service) {
        //        $this->authorize('store', $language);

        $newCampaign = $service->createCampaign($request);

        return redirect()->route('campaigns.index')
                         ->withStatus("New email campaign: {$newCampaign->title} is created!");
    }

    /**
     * Display the specified resource.
     *
     * @param \Anacreation\CmsEmail\Models\Campaign $campaign
     * @param \Illuminate\Http\Request              $request
     * @return Response
     */
    public function show(Campaign $campaign, Request $request) {

        $recipient = null;

        if ($token = $request->query('token')) {

            $data = decrypt($token);

            if (isset($data['list_id']) and isset($data['email'])) {
                if ($list = EmailList::find($data['list_id'])) {
                    $recipient = $list->recipients()->whereEmail($data['email'])
                                      ->first();
                }
            }
        }

        $data = [
            'name'      => "",
            'campaign'  => $campaign,
            'recipient' => $recipient,
            'user'      => null,
        ];


        return view(config('cms_email.template_folder') . ".{$campaign->template}",
            $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Anacreation\CmsEmail\Models\Campaign $campaign
     * @return Response
     */
    public function edit(Campaign $campaign) {
        //        $this->authorize('edit', $language);


        $templates = $campaign->getEmailTemplates();

        $recipientStatus = Recipient::StatusTypes;

        return view('cms_email::campaigns.edit',
            compact('campaign', 'templates', 'recipientStatus'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request                                       $request
     * @param \Anacreation\CmsEmail\Models\Campaign          $campaign
     * @param \Anacreation\CmsEmail\Services\CampaignService $service
     * @return Response
     */
    public function update(
        Request $request, Campaign $campaign, CampaignService $service
    ) {
        //        $this->authorize('update', $language);

        $campaign = $service->updateCampaign($request, $campaign);


        return redirect()->route('campaigns.index')
                         ->withStatus("Campaign: {$campaign->title} is updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Language $language
     * @return Response
     * @throws \Exception
     */
    public function destroy(Campaign $campaign) {
        //        $this->authorize('delete', $language);

        $campaign->delete();

        return response()->json(['status' => 'completed']);
    }


    public function send(Campaign $campaign) {

        $campaign->launch();

        return redirect()->back()->withStatus("{$campaign->title} is sending!");
    }

    public function activities(Campaign $campaign, Request $request) {

        if ($request->ajax()) {
            list($totalRecipients, $numberToProvider, $numberNotSend, $delivered, $bounced, $dropped) = $this->getNumbers($campaign);

            return response()->json([
                "totalRecipients" => $totalRecipients,
                "totalToProvider" => $numberToProvider,
                "notSent"         => $numberNotSend,
                "totalDelivered"  => $delivered,
                "totalBounce"     => $bounced,
                "totalDropped"    => $dropped,
            ]);
        }


        return view('cms_email::campaigns.activities',
            compact('campaign'));
    }

    public function details(Campaign $campaign, string $status) {
        $recipientStatuses = null;
        if ($status === CampaignStatus::Status['to_provider']) {
            $recipientStatuses = CampaignStatus::whereNotIn('status', ['none'])
                                               ->with('recipient')
                                               ->paginate(100);
        } else {
            if (in_array($status, array_keys(CampaignStatus::Status))) {
                $recipientStatuses = CampaignStatus::whereStatus($status)
                                                   ->with('recipient')
                                                   ->paginate(100);
            } else {
                throw new \InvalidArgumentException("Campaign status not recognised!");
            }
        }

        return view('cms_email::campaigns.details',
            compact('campaign', 'recipientStatuses', 'status'));
    }

    /**
     * @param \Anacreation\CmsEmail\Models\Campaign $campaign
     * @return array
     */
    private function getNumbers(Campaign $campaign): array {

        // Total campaign recipients
        $totalRecipients = $campaign->list->recipients()
                                          ->whereIn('status',
                                              $campaign->to_status)->count();


        // Successfully send to provider
        $numberToProvider = CampaignStatus::select([
            'campaign_id',
            'recipient_id',
            'status'
        ])->distinct()->whereNotIn("status", ['none'])->count();


        // Try to send to provider but fails or not even try to sent
        $numberNotSend = $totalRecipients;
        if ($campaign->has_sent) {
            $numberNotSend1 = $campaign->list->recipients()
                                             ->whereIn('id',
                                                 function ($subQuery) use (
                                                     $campaign
                                                 ) {
                                                     $subQuery->select(['recipient_id'])
                                                              ->from('campaign_status')
                                                              ->where([
                                                                  [
                                                                      'campaign_id',
                                                                      "=",
                                                                      $campaign->id
                                                                  ],
                                                                  [
                                                                      'status',
                                                                      "=",
                                                                      CampaignStatus::Status['none']
                                                                  ],
                                                              ]);
                                                 })->count();


            $numberNotSend3 = CampaignStatus::select([
                'id',
                'campaign_id',
                'recipient_id'
            ])
                                            ->whereCampaignId($campaign->id)
                                            ->get()->groupBy('recipient_id')->count();
            $numberNotSend2 = $campaign->list->recipients()
                                             ->whereNotIn('id',
                                                 function ($subQuery) use (
                                                     $campaign
                                                 ) {
                                                     $subQuery->select('recipient_id')
                                                              ->from('campaign_status')
                                                              ->where('campaign_id',
                                                                  $campaign->id);
                                                 })
                                             ->count();
//            dd($numberNotSend1, $numberNotSend2, $numberNotSend3);

            $numberNotSend = $numberNotSend1 + $numberNotSend2;
        }

        // Successfully deliver to recipient mailbox (form webhook)
        $delivered = CampaignStatus::select([
            'campaign_id',
            'recipient_id',
            'status'
        ])->groupBY([
            'campaign_id',
            'recipient_id',
            'status'
        ])->whereStatus('delivered')->count();

        // Try to deliver but bounce from recipient mailbox (form webhook)
        $bounced = CampaignStatus::select([
            'campaign_id',
            'recipient_id',
            'status'
        ])->distinct()->whereStatus(CampaignStatus::Status['bounce'])->count();

        // Provider not deliver (form webhook)
        $dropped = CampaignStatus::select([
            'campaign_id',
            'recipient_id',
            'status'
        ])->distinct()->whereStatus(CampaignStatus::Status['dropped'])->count();

        return array(
            $totalRecipients,
            $numberToProvider,
            $numberNotSend,
            $delivered,
            $bounced,
            $dropped
        );
    }

    public function resendAll(Campaign $campaign) {

        $recipients = $campaign->list->recipients()
                                     ->whereIn('id', function ($query) {
                                         $query->select('recipient_id')
                                               ->from("campaign_status")
                                               ->whereStatus(CampaignStatus::Status['none']);
                                     })->get();

        $recipients->each(function (Recipient $recipient) use ($campaign) {

            $job = new SendEmail($campaign, $recipient);

            dispatch($job);
        });

        return redirect()->back()
                         ->withStatus("Resent to {$recipients->count()} unsent recipients!");

    }
}
