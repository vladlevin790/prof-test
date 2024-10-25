<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FIles extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'name',
        'user_id',
        'file_path',
    ];

    protected function user() {
        return $this->belongsTo(User::class);
    }
}
