<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\Cms\Services\ContentService;
use Anacreation\Cms\Services\LanguageService;
use Anacreation\CmsEmail\Models\Campaign;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CampaignContentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Campaign $campaign
     * @return Response
     */
    public function index(Campaign $campaign, LanguageService $langService) {
        //        $this->authorize('index', $language);

        $contents = $campaign->loadContents(resource_path('views/emails'),
            $campaign->template);
        $languages = $langService->activeLanguages;


        return view('cms_email::campaigns.content.index',
            compact('campaign', 'contents', 'languages'));
    }

    public function update(
        Request $request, Campaign $campaign, ContentService $service
    ) {
        $validatedData = $this->validate($request,
            $service->getUpdateValidationRules());

        $service->updateOrCreateContentIndex($campaign,
            $service->createContentObject($validatedData),
            $request->file('content'));

        return response()->json("done");
    }

}
