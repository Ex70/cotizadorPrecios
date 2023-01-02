<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute_Category extends Model
{
    use HasFactory;

    protected $table = 'attribute_category';
    protected $fillable = [
        'category_id',
        'attribute_id'
    ];
}
