<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Form extends Model
{
    protected $table = 'cms_forms';

    protected $fillable = ['name', 'table_name', 'description', 'sort_order', 'form_group_id'];

    public function formGroup(): BelongsTo
    {
        return $this->belongsTo(FormGroup::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class, 'form_id')->orderBy('sort_order');
    }

    public function relations(): HasMany
    {
        return $this->hasMany(FormRelation::class, 'form_id');
    }

    public function tableExists(): bool
    {
        return Schema::hasTable($this->table_name);
    }
}
