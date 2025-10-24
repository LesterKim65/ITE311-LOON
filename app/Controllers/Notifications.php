<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    /**
     * Get unread count and list of notifications for the current user.
     */
    public function get()
    {
        if (!session()->has('id')) {
            return $this->response->setJSON(['error' => 'Not logged in']);
        }

        $notificationModel = new NotificationModel();
        $userId = session('id');

        $unreadCount = $notificationModel->getUnreadCount($userId);
        $notifications = $notificationModel->getNotificationsForUser($userId);

        return $this->response->setJSON([
            'unreadCount' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function mark_as_read($id)
    {
        if (!session()->has('id')) {
            return $this->response->setJSON(['error' => 'Not logged in']);
        }

        $notificationModel = new NotificationModel();
        $userId = session('id');

        // Check if the notification belongs to the user
        $notification = $notificationModel->find($id);
        if (!$notification || $notification['user_id'] != $userId) {
            return $this->response->setJSON(['error' => 'Notification not found']);
        }

        $result = $notificationModel->markAsRead($id);

        if ($result) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['error' => 'Failed to mark as read']);
        }
    }
}
