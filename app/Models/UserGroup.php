<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserGroup extends Model
{
    protected $table = 'user_groups';

    protected $fillable = ['name', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(CmsUser::class, 'user_group_id');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(GroupPermission::class, 'user_group_id');
    }
}
