<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    protected $table = 'cms_form_fields';

    protected $fillable = [
        'form_id', 'field_name', 'label', 'form_control',
        'options', 'attributes', 'validation_rules',
        'sort_order', 'is_required', 'is_list_visible',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_list_visible' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function relation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FormRelation::class, 'form_field_id');
    }

    public function getOptionsArray(): array
    {
        if (empty($this->options)) {
            return [];
        }
        $decoded = json_decode($this->options, true);
        return is_array($decoded) ? $decoded : [];
    }
}
