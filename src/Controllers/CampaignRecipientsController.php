<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\Cms\Models\Language;
use Anacreation\CmsEmail\Models\Campaign;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;


class CampaignRecipientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Campaign $campaign) {
        //        $this->authorize('index', $language);

        $recipients = $campaign->recipients()->paginate(50);

        return view('cms_email::campaigns.recipients.index',
            compact('campaign', 'recipients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Campaign $campaign, Request $request) {
        //        $this->authorize('create', $language);


        if ($keyword = $request->query('search')) {

            $users = User::where('name', 'like', "%{$keyword}%")
                         ->orWhere('email', 'like', "%{$keyword}%")
                         ->get();

            return view('cms_email::campaigns.recipients.search',
                compact("users"));

        }

        return view('cms_email::campaigns.recipients.create',
            compact("campaign"));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request, Campaign $campaign) {
        //        $this->authorize('store', $language);


        $validateData = $this->validate($request, [
            'user_id' => 'nullable|exists:users,id',
            'name'    => 'nullable|required_without:user_id',
            'email'   => 'nullable|email|required_without:user_id',
        ]);

        if ($validateData['user_id']) {
            $user = User::findOrFail($validateData['user_id']);
            $data = [
                'name'    => $user->name,
                'email'   => $user->email,
                'user_id' => $user->id,
            ];
        } else {
            $data = [
                'name'    => $validateData['name'],
                'email'   => $validateData['email'],
                'user_id' => null,
            ];
        }

        $recipient = $campaign->recipients()->create($data);

        return redirect()->route('campaigns.recipients.index', $campaign)
                         ->withStatus("New recipient added: {$recipient->name} is created!");
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
    public function edit(Language $language) {
        $this->authorize('edit', $language);
        $languages = Language::where('id', "<>", $language->id)
                             ->pluck('label', 'id')->toArray();
        $languages = array_merge(['0' => 'To Default'], $languages);


        return view('cms::admin.languages.edit',
            compact("language", 'languages'));
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
    public function destroy(Language $language) {
        $this->authorize('delete', $language);

        $language->delete();

        return response()->json(['status' => 'completed']);
    }

    private function getEmailTemplates(): array {

        $path = resource_path("views/emails");

        $templates = [];
        try {
            $templates = scandir($path);
            $templates = sanitizeFileNames(compact("templates"));
        } catch (\Exception $e) {

        }

        return $templates['templates'];
    }
}
