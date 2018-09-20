<?php

namespace Anacreation\CmsEmail\Controllers;

use Anacreation\CmsEmail\Jobs\ProcessWebhookPayload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class WebhooksController extends Controller
{
    public function parse(Request $request) {

        $collection = collect($request->all())->sortBy('timestamp');

        dispatch(new ProcessWebhookPayload($collection));

        return response()->json(['completed' => true]);
    }
}
