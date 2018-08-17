<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\Cms\Models\Language;
use Anacreation\CmsEmail\Exports\ListRecipientExport;
use Anacreation\CmsEmail\Models\EmailList;
use Anacreation\CmsEmail\Models\Recipient;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;


class EmailListRecipientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param EmailList $list
     * @return Response
     */
    public function index(EmailList $list) {
        //        $this->authorize('index', $language);

        $recipients = $list->recipients()->paginate(50);

        return view('cms_email::lists.recipients.index',
            compact('list', 'recipients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param EmailList $list
     * @param Request   $request
     * @return Response
     */
    public function create(EmailList $list, Request $request) {
        //        $this->authorize('create', $language);


        if ($keyword = $request->query('search')) {

            $users = User::where('name', 'like', "%{$keyword}%")
                         ->orWhere('email', 'like', "%{$keyword}%")
                         ->get();

            return view('cms_email::lists.recipients.search',
                compact("users"));

        }

        return view('cms_email::lists.recipients.create',
            compact("list"));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param EmailList $list
     * @return Response
     */
    public function store(Request $request, EmailList $list) {
        //        $this->authorize('store', $language);

        $validateData = $this->validate($request, [
            'name'  => 'required|',
            'email' => 'required|email',
        ]);

        $data = [
            'name'    => $validateData['name'],
            'email'   => $validateData['email'],
            'user_id' => null,
            'status'  => Recipient::StatusTypes['pending']
        ];

        $recipient = $list->recipients()->create($data);

        return redirect()->route('lists.recipients.index', $list)
                         ->withStatus("New recipient added: {$recipient->name} is created!");
    }

    /**
     * Display the specified resource.
     *
     * @param  Language $language
     * @return void
     */
    public function show(Language $language) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Language $language
     * @return Response
     * @throws AuthorizationException
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
     * @throws AuthorizationException
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
    public function destroy(EmailList $list, Recipient $recipient) {
        //        $this->authorize('delete', $language);

        if ($list->recipients()->find($recipient->id)->first()) {
            $recipient->delete();

            return response()->json(['status' => 'completed']);
        }

        return response()->json(['status' => 'cannot find recipient in list'],
            403);

    }

    public function import(Request $request, EmailList $list) {
        $this->validate($request, [
            'file' => 'required|file'
        ]);

        $path = $request->file('file')->getRealPath();

        list($headers, $rows) = $this->parseCSV($path);

        $count = $this->createRecord($list, $rows, $headers);

        return redirect()->route('lists.recipients.index', $list->id)
                         ->withStatus("Total {$count} email imported");
    }

    public function showImport(EmailList $list) {
        return view('cms_email::lists.recipients.import', compact("list"));

    }

    /**
     * @param $path
     * @return array
     */
    private function parseCSV($path): array {
        $headers = [];
        $data = [];
        $counter = 0;
        $handler = fopen($path, 'r');

        while (($csv = fgetcsv($handler)) !== false) {

            if ($counter === 0) {
                $headers = array_map('trim', array_map('strtolower', $csv));
            } else {
                $data[] = $csv;
            }

            $counter++;
        }

        fclose($handler);

        return array($headers, $data);
    }

    /**
     * @param EmailList                              $list
     * @param                                        $rows
     * @param                                        $headers
     * @return int
     */
    private function createRecord(EmailList $list, $rows, $headers): int {
        $count = 0;
        foreach ($rows as $data) {
            $record = array_combine($headers, $data);
            if ($list->recipients()->whereEmail($record['email'])->exists()) {
                Log::info("{$record['email']} is duplicated for list {$list->title}, skip import");
            } else {
                $list->recipients()->create($record);
                $count++;
            }
        }

        return $count;
    }

    public function unsubscribe(EmailList $list, Request $request) {
        if ($token = $request->query('token')) {

            if ($list->updateRecipientStateWithToken($token,
                Recipient::StatusTypes['unsubscribed'])) {

                $uri = config('cms_email.unsbuscribe_redirect_url', "/");

                return redirect($uri)->with('email_notice',
                    'You have subscribe from our email list.');

            }
        }

        return redirect('/');
    }

    public function confirm(EmailList $list, Request $request) {
        if ($token = $request->query('token')) {
            if ($list->updateRecipientStateWithToken($token,
                Recipient::StatusTypes['confirmed'])) {
                $uri = config('cms_email.confirmed_redirect_url', "/");

                return redirect($uri)->with('email_notice',
                    'Thank you for confirm the email');
            }
        }

        return redirect('/');
    }

    public function export(EmailList $list)  {
        return Excel::download(new ListRecipientExport($list), 'recipients.xlsx');
    }
}
