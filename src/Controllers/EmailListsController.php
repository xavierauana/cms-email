<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\Cms\Models\Language;
use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\EmailList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;


class EmailListsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //        $this->authorize('index', $language);

        $lists = EmailList::all();

        return view('cms_email::lists.index', compact('lists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //        $this->authorize('create', $language);

        return view('cms_email::lists.create', compact('templates'));

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
            'title'               => 'required',
            'confirm_opt_in'      => 'required|boolean',
            'has_welcome_message' => 'required|boolean',
            'has_goodbye_message' => 'required|boolean',
        ]);


        $newList = EmailList::create($validateData);

        return redirect()->route('lists.index')
                         ->withStatus("New email list: {$newList->title} is created!");
    }

    /**
     * Display the specified resource.
     *
     * @param  Language $language
     * @return Response
     */
    public function show(Language $language) {
        //
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

        $path = resource_path("views/emails/layouts");

        $templates = [];
        try {
            $templates = scandir($path);
            $templates = sanitizeFileNames(compact("templates"));
        } catch (\Exception $e) {

        }

        return $templates['templates'];
    }

    public function send(Campaign $campaign) {

        $campaign->launch();

        return redirect()->back()->withStatus("{$campaign->title} sent!");
    }
}
