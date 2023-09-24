<?php

namespace App\Models;

use App\Models\Cost;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';

    protected $fillable = [
        'title',
        // other fillable attributes
    ];

    public function costs()
    {
        return $this->hasMany(Cost::class);
    }

}
