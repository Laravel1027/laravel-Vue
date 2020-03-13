<?php

namespace App\Http\Controllers;

use App\Contracts\INotificationService;
use Illuminate\Http\Request;
use Laravel\Spark\Contracts\Repositories\AnnouncementRepository;

class NotificationController extends Controller
{
    protected $announcements;
    protected $notifications;

    /**
     * NotificationController constructor.
     * @param AnnouncementRepository $announcements
     * @param INotificationService $notifications
     */
    public function __construct(AnnouncementRepository $announcements, INotificationService $notifications)
    {
        $this->announcements = $announcements;
        $this->notifications = $notifications;

        $this->middleware('auth');
    }

    /**
     * Get the recent notifications and announcements for the user.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recent(Request $request)
    {
        return response()->json([
            'announcements' => $this->announcements->recent()->toArray(),
            'notifications' => $this->notifications->recent($request->user())->toArray(),
        ]);
    }
}