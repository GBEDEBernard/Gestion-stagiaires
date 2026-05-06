<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'etudiant_id', 'permission_type_id',
        'fields_data', 'note', 'status',
        'decided_by', 'decided_at', 'decision_comment',
    ];

    protected $casts = [
        'fields_data' => 'array',
        'decided_at'  => 'datetime',
    ];

    /* ── Relations ─────────────────────────────────── */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function type()
    {
        return $this->belongsTo(PermissionType::class, 'permission_type_id');
    }

    public function recipients()
    {
        return $this->hasMany(PermissionRequestRecipient::class);
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /* ── Helpers ───────────────────────────────────── */

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'   => 'En attente',
            'approved'  => 'Approuvé',
            'rejected'  => 'Refusé',
            'cancelled' => 'Annulé',
            default     => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'   => 'amber',
            'approved'  => 'emerald',
            'rejected'  => 'red',
            'cancelled' => 'slate',
            default     => 'slate',
        };
    }

    /**
     * Human-readable summary of the dynamic fields.
     */
    public function fieldSummary(): string
    {
        $data   = $this->fields_data ?? [];
        $config = $this->type->fields_config ?? [];
        $parts  = [];

        foreach ($config as $field) {
            $key = $field['key'];
            if (isset($data[$key]) && $data[$key] !== '') {
                $label = $field['label'];
                $value = $data[$key];
                if ($field['type'] === 'date') {
                    try { $value = \Carbon\Carbon::parse($value)->format('d/m/Y'); } catch (\Throwable) {}
                }
                if ($field['type'] !== 'textarea') {
                    $parts[] = "$label : $value";
                }
            }
        }

        return implode(' — ', $parts);
    }
}
