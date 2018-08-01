<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\Cms\Models\Role;
use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\EmailList;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;


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
    public function create() {
        //        $this->authorize('create', $language);


        $templates = $this->getEmailTemplates();


        return view('cms_email::campaigns.create', compact('templates'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request) {
        //        $this->authorize('store', $language);

        $validateData = $this->validate($request, [
            'title'         => 'required',
            'subject'       => 'required',
            'from_name'     => 'required',
            'from_address'  => 'required|email',
            'reply_address' => 'required|email',
            'template'      => [
                'required',
                Rule::in($this->getEmailTemplates())
            ],
            'is_scheduled'  => 'required|boolean',
            'email_list_id' => [
                'required_without:role_id',
                'nullable',
                Rule::in(EmailList::pluck('id')->toArray())
            ],
            'role_id'       => 'required_without:email_list_id|nullable|in:' . implode(", ",
                    Role::pluck('id')->toArray()),
            'schedule'      => 'required_if:is_scheduled,1|nullable|date|after_or_equal:' . Carbon::now()
                                                                                                  ->toDateTimeString(),
        ]);


        if (!$validateData['is_scheduled']) {
            $validateData['schedule'] = null;
        }

        $newCampaign = Campaign::create($validateData);

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
     * @param  Language $language
     * @return Response
     */
    public function edit(Campaign $campaign) {
        //        $this->authorize('edit', $language);


        $templates = $this->getEmailTemplates();

        return view('cms_email::campaigns.edit',
            compact('campaign', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Language $language
     * @return Response
     */
    public function update(Request $request, Language $language) {
        $this->authorize('update', $language);

        $validateData = $this->validate($request, [
            'label'                => 'required',
            'code'                 => [
                'required',
                Rule::unique('languages')->ignore($language->id, 'id')
            ],
            'is_active'            => 'required|boolean',
            'is_default'           => 'required|boolean',
            'fallback_language_id' => 'required|in:0,' . implode(',',
                    Language::pluck('id')->toArray()),
        ]);


        if ($validateData['is_default'] == "1") {
            Language::where('id', '<>', $language->id)->get()->each(function (
                Language $language
            ) {
                $language->is_default = false;
                $language->save();
            });
        }

        $language->update($validateData);

        return redirect()->route('languages.index');
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

    private function getEmailTemplates(): array {

        $path = resource_path("views/" . config("cms_email.template_folder"));

        $templates = [];
        try {
            $templates = scandir($path);
            $templates = sanitizeFileNames(compact("templates"));
        } catch (\Exception $e) {

        }

        return $templates['templates'] ?? [];
    }

    public function send(Campaign $campaign) {

        $campaign->launch();

        return redirect()->back()->withStatus("{$campaign->title} is sending!");
    }
}
