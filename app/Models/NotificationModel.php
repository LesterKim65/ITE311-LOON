<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    /**
     * Get the count of unread notifications for a user.
     */
    public function getUnreadCount($userId)
    {
        if (!$this->db->tableExists('notifications')) {
            return 0;
        }
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Get the latest notifications for a user, limited to 5.
     */
    public function getNotificationsForUser($userId)
    {
        if (!$this->db->tableExists('notifications')) {
            return [];
        }
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->findAll();
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($notificationId)
    {
        if (!$this->db->tableExists('notifications')) {
            return false;
        }
        return $this->update($notificationId, ['is_read' => 1]);
    }
}
