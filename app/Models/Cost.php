<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    use HasFactory;
    protected $table = 'costs';

    protected $fillable = [
        'title',
        'post_id'
        // other fillable attributes
    ];

    public function post()
    {
        return $this->belongsTo(Post::class,'post_id');
    }
}
