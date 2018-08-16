<?php
/**
 * Author: Xavier Au
 * Date: 16/8/2018
 * Time: 10:46 AM
 */

namespace Anacreation\CmsEmail\Services;


use Anacreation\Cms\Models\Role;
use Anacreation\CmsEmail\Models\Campaign;
use Anacreation\CmsEmail\Models\EmailList;
use Anacreation\CmsEmail\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CampaignService
{
    private $validatedData = null;

    /**
     * CampaignService constructor.
     * @param \Anacreation\CmsEmail\Models\Campaign $campaign
     */
    public function __construct(Campaign $campaign) {
        $this->campaign = $campaign;
    }

    public function getStoreValidationRules(): array {
        return [
            'title'         => 'required',
            'subject'       => 'required',
            'from_name'     => 'required',
            'from_address'  => 'required|email',
            'reply_address' => 'required|email',
            'file'          => 'required_without:template|nullable|file',
            'template'      => [
                'required_without:file',
                'nullable',
                Rule::in($this->campaign->getEmailTemplates())
            ],
            'is_scheduled'  => 'required|boolean',
            'email_list_id' => [
                'required_without:role_id',
                'nullable',
                Rule::in(EmailList::pluck('id')->toArray())
            ],
            'role_id'       => [
                'required_without:email_list_id',
                'nullable',
                Rule::in(Role::pluck('id')->toArray())
            ],
            'schedule'      => 'required_if:is_scheduled,1|nullable|date|after_or_equal:' . Carbon::now()
                                                                                                  ->toDateTimeString(),
            'to_status'     => 'required',
            'to_status.*'   => 'in:' . implode(",", Recipient::StatusTypes),
        ];
    }

    public function validateRequest(Request $request, $action = "store"
    ) {
        $rules = $this->getStoreValidationRules();

        app(Factory::class)
            ->make($request->all(), $rules, [], [])
            ->validate();

        $this->validatedData = $this->extractInputFromRules($request, $rules);

    }

    public function createCampaign(Request $request): Campaign {

        $this->validateRequest($request);

        if (!$this->validatedData['is_scheduled']) {
            $this->validatedData['schedule'] = null;
        }

        if (isset($this->validatedData['file']) and $file = $this->validatedData['file']) {

            $fileName = $file->getClientOriginalName();
            $path = resource_path('views/' . config('cms_email.template_folder'));
            $file->move($path, $fileName);

            $templateName = str_replace(".blade.php", "", $fileName);

            $this->validatedData['template'] = $templateName;

        }

        return $this->campaign->create($this->validatedData);

    }

    public function updateCampaign(Request $request, Campaign $campaign
    ): Campaign {

        $this->validateRequest($request, 'update');

        if (!$this->validatedData['is_scheduled']) {
            $this->validatedData['schedule'] = null;
        }

        if (isset($this->validatedData['file']) and $file = $this->validatedData['file']) {

            $fileName = $file->getClientOriginalName();
            $path = resource_path('views/' . config('cms_email.template_folder'));
            $file->move($path, $fileName);

            $templateName = str_replace(".blade.php", "", $fileName);

            $this->validatedData['template'] = $templateName;

        }

        $campaign->update($this->validatedData);

        return $campaign;
    }

    protected function extractInputFromRules(Request $request, array $rules) {
        return $request->only(collect($rules)->keys()->map(function ($rule) {
            return Str::contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray());
    }
}