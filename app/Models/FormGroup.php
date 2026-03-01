<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormGroup extends Model
{
    protected $table = 'cms_form_groups';

    protected $fillable = ['name', 'sort_order'];

    public function forms(): HasMany
    {
        return $this->hasMany(Form::class, 'form_group_id')->orderBy('sort_order');
    }
}
