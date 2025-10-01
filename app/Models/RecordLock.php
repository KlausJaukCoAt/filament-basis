<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordLock extends Model
{
        protected $fillable = ['lockable_type', 'lockable_id', 'user_id', 'locked_at'];


    public function lockable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
