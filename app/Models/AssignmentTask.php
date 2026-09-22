<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentTask extends Model
{
    protected $guarded = ['id'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function group()
    {
        return $this->belongsTo(AssignmentGroup::class, 'selected_by_group_id');
    }
}
