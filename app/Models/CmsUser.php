<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class CmsUser extends Model
{
    protected $table = 'cms_users';

    protected $hidden = ['password'];

    protected $fillable = [
        'username', 'password', 'nickname', 'user_group_id',
        'is_root', 'is_active',
    ];

    protected $casts = [
        'is_root' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }

    public function hasPermission(string $tableName, string $action): bool
    {
        if ($this->is_root) {
            return true;
        }
        $perm = $this->userGroup?->permissions()
            ->where('table_name', $tableName)
            ->first();
        return $perm && $perm->{"can_{$action}"};
    }

    public function canAccessTable(string $tableName, string $action): bool
    {
        if ($this->is_root) {
            return true;
        }
        $perm = $this->userGroup?->permissions()
            ->where('table_name', $tableName)
            ->first();
        return $perm && $perm->{"can_{$action}"};
    }
}
