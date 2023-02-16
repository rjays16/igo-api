<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use HasFactory;
    protected $primaryKey = 'state';
    public $incrementing = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable=['state','name','description'];
}
