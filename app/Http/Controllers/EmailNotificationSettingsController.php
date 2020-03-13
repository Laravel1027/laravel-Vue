<?php

namespace App\Http\Controllers;

use App\Contracts\IEmailNotificationSettingsService;
use App\Http\Requests\EmailNotificationSettingsRequest;
use Illuminate\Http\Request;

class EmailNotificationSettingsController extends Controller
{
    protected $emailSettingsService;

    public function __construct(IEmailNotificationSettingsService $emailSettingsService)
    {
        $this->emailSettingsService = $emailSettingsService;
    }

    /**
     * @param $data
     */
    public function store($data)
    {
        //
    }

    /**
     * Update Email Notifications Settings
     * @param EmailNotificationSettingsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EmailNotificationSettingsRequest $request)
    {
        $settings = $request->except(['busy', 'successful', 'errors']);
        $user = $request->user();
        $result = $this->emailSettingsService->update($user, $settings);

        //success
        if ($result) {
            return response()->json([
                'info' => 'Your settings has been updated successfully.',
                'status' => true
            ], 200);
        }

        //fail
        return response()->json([
            'info' => 'Something went wrong, please try again later.',
            'status' => false
        ]);
    }
}
