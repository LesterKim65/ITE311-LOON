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

        try {
            $notificationModel = new NotificationModel();
            $userId = session('id');

            $unreadCount = $notificationModel->getUnreadCount($userId);
            $notifications = $notificationModel->getNotificationsForUser($userId);

            // Ensure notifications is always an array
            if (!is_array($notifications)) {
                $notifications = [];
            }

            return $this->response->setJSON([
                'unreadCount' => (int)$unreadCount,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Failed to fetch notifications: ' . $e->getMessage(),
                'unreadCount' => 0,
                'notifications' => []
            ]);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function mark_as_read($id)
    {
        if (!session()->has('id')) {
            return $this->response->setJSON(['error' => 'Not logged in']);
        }

        if (!$id || !is_numeric($id)) {
            return $this->response->setJSON(['error' => 'Invalid notification ID']);
        }

        try {
            $notificationModel = new NotificationModel();
            $userId = session('id');

            // Check if the notification belongs to the user
            $notification = $notificationModel->find($id);
            if (!$notification) {
                return $this->response->setJSON(['error' => 'Notification not found']);
            }

            if ($notification['user_id'] != $userId) {
                return $this->response->setJSON(['error' => 'You do not have permission to mark this notification as read']);
            }

            // Check if already read
            if ($notification['is_read'] == 1) {
                return $this->response->setJSON(['success' => true, 'message' => 'Notification already marked as read']);
            }

            $result = $notificationModel->markAsRead($id);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Failed to mark as read']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}
