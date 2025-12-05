<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'is_active',
        'sort_order',
        'image',
        'image_key',
    ];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')
            ->orderBy('sort_order', 'asc')
            ->with('children');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }
}
