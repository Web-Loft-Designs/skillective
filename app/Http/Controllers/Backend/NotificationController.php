<?php

namespace App\Http\Controllers\Backend;

use App\Models\CustomNotification;
use App\Http\Requests\UpdateNotification;
use App\Services\CustomNotificationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /**
     * @var \App\Services\CustomNotificationService
     */
    private $notificationService;

    /**
     * NotificationController constructor.
     *
     * @param \App\Services\CustomNotificationService $notificationService
     */
    public function __construct(CustomNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
		parent::__construct();
    }
    public function index()
    {
        $collection = CustomNotification::query()->paginate(100);
        $page_title = 'Notifications';

        return view('backend.notification.list', compact('collection', 'page_title'));
    }

    public function edit(CustomNotification $notification)
    {
        $resource = $notification;
        $page_title = 'Edit Notification';
        $tagsWithoutSms = CustomNotification::$tagsWithoutSms;
        return view('backend.notification.edit', compact('resource', 'page_title', 'tagsWithoutSms'));
    }

    public function update(UpdateNotification $request, CustomNotification $notification)
    {
        $this->notificationService->update($notification, $request->validated());

        session()->flash('alert-success', 'Notification was updated!');

        return redirect()->route('backend.notifications.index');
    }
}
