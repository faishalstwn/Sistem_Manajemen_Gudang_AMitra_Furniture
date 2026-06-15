<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditLogger
{
    /**
     * Log audit trail
     *
     * @param string $action - create, update, delete, view
     * @param string $entity - Product, Order, etc
     * @param int|null $entityId
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param array $metadata - additional data
     * @return AuditLog
     */
    public static function log(
        string $action,
        string $entity,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $metadata = []
    ): AuditLog {
        return AuditLog::create([
            'user_id' => auth()?->id(),
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get audit logs for entity
     *
     * @param string $entity
     * @param int|null $entityId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getEntityLogs(string $entity, ?int $entityId = null)
    {
        $query = AuditLog::where('entity', $entity);

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get user activity logs
     *
     * @param int $userId
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserLogs(int $userId, ?int $limit = null)
    {
        $query = AuditLog::where('user_id', $userId)->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
