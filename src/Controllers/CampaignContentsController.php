<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\Cms\Models\ContentIndex;
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

    /**
     * @param \Illuminate\Http\Request                 $request
     * @param \Anacreation\CmsEmail\Models\Campaign    $campaign
     * @param \Anacreation\Cms\Models\ContentIndex     $contentIndex
     * @param \Anacreation\Cms\Services\ContentService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \Anacreation\Cms\Exceptions\IncorrectContentTypeException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(
        Request $request, Campaign $campaign, ContentIndex $contentIndex,
        ContentService $service
    ) {
        $this->authorize('update', $contentIndex);

        $this->validate($request,
            $service->getUpdateValidationRules());

        $service->updateOrCreateContentIndex($campaign, $request);

        return response()->json("done");
    }

}
