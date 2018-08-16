<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\CmsEmail\Models\Campaign;
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
     * @return Response
     */
    public function show(Campaign $campaign) {

        $data = [
            'name'      => "",
            'campaign'  => $campaign,
            'recipient' => null,
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
}
