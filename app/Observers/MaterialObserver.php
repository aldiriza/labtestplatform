<?php

namespace App\Observers;

use App\Models\Material;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class MaterialObserver
{
    /**
     * Handle the Material "created" event.
     */
    public function created(Material $material): void
    {
        $this->log($material, 'created', null, $material->toArray());
    }

    /**
     * Handle the Material "updated" event.
     */
    public function updated(Material $material): void
    {
        // specific logic to detect overrides?
        // Just log all updates for now or focus on status changes.
        // User requested: "Especially important for Admin overrides"
        
        $original = $material->getOriginal();
        $changes = $material->getChanges();

        if (count($changes) > 0) {
            $this->log($material, 'updated', $original, $changes);
        }
    }

    /**
     * Handle the Material "deleted" event.
     */
    public function deleted(Material $material): void
    {
        $this->log($material, 'deleted', $material->toArray(), null);
    }

    protected function log(Material $material, string $action, $oldValue = null, $newValue = null)
    {
        if (!Auth::check()) {
            return; // System actions or seeders might not have user
        }

        $user = Auth::user();
        
        AuditLog::create([
            'user_id' => $user->id,
            'role' => $user->role ?? 'unknown', // Assuming role is on user
            'action' => $action,
            'table_name' => 'materials',
            'record_id' => $material->id,
            'old_value' => $oldValue ? json_encode($oldValue) : null,
            'new_value' => $newValue ? json_encode($newValue) : null,
        ]);
    }
}
