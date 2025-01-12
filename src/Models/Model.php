<?php
namespace RA\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;

class Model extends LaravelModel
{
    protected $guarded = [
        'id', 'created_at', 'updated_at',
    ];
}
