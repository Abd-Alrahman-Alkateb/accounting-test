<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    use HasFactory;
    protected $table = 'accountings';

    protected $fillable = ['name', 'parent_id', 'account', 'code'];
}
