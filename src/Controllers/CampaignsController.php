<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\CmsEmail\Models\Campaign;
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
     * @return Response
     */
    public function create(Campaign $campaign) {
        //        $this->authorize('create', $language);


        $templates = $campaign->getEmailTemplates();


        return view('cms_email::campaigns.create', compact('templates'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request, Campaign $repo) {
        //        $this->authorize('store', $language);

        $validateData = $this->validate($request,
            $repo->getFormValidationRules());


        if (!$validateData['is_scheduled']) {
            $validateData['schedule'] = null;
        }

        $newCampaign = $repo->create($validateData);

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
        return view(config('cms_email.template_folder') . ".{$campaign->template}",
            compact('campaign'));
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

        return view('cms_email::campaigns.edit',
            compact('campaign', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request                              $request
     * @param \Anacreation\CmsEmail\Models\Campaign $campaign
     * @return Response
     */
    public function update(Request $request, Campaign $campaign) {
        //        $this->authorize('update', $language);

        $validateData = $this->validate($request,
            $campaign->getFormValidationRules());


        if (!$validateData['is_scheduled']) {
            $validateData['schedule'] = null;
        }

        $campaign->update($validateData);

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
