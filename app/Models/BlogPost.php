<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = ['user_id', 'category_id', 'title', 'slug', 'thumbnail', 'content', 'excerpt', 'status', 'published_at'];
}
