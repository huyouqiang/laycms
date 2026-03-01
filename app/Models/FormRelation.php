<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormRelation extends Model
{
    protected $table = 'cms_form_relations';

    protected $fillable = ['form_id', 'form_field_id', 'related_form_id', 'related_field_name'];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }

    public function relatedForm(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'related_form_id');
    }
}
