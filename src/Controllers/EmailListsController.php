<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\Cms\Models\Language;
use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\EmailList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


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

        return view('cms_email::lists.create', compact('templates'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request) {

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
     * @param \Anacreation\CmsEmail\Models\EmailList $list
     * @return Response
     */
    public function edit(EmailList $list) {

        return view('cms_email::lists.edit',
            compact('list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Language $language
     * @return Response
     */
    public function update(Request $request, EmailList $list) {

        $validateData = $this->validate($request, [
            'title'               => 'required',
            'confirm_opt_in'      => 'required|boolean',
            'has_welcome_message' => 'required|boolean',
            'has_goodbye_message' => 'required|boolean',
        ]);


        $list->update($validateData);

        return redirect()->route('lists.index')->with('notice',
            "Email list: {$list->title} is updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Anacreation\CmsEmail\Models\EmailList $list
     * @return Response
     * @throws \Exception
     */
    public function destroy(EmailList $list) {

        $list->delete();

        return response()->json(['status' => 'completed']);
    }
}
