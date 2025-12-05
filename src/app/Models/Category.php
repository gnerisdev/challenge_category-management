<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            if (!$category->isForceDeleting()) {
                $category->children()->delete();
            }
        });

        static::forceDeleting(function ($category) {
            $category->children()->withTrashed()->forceDelete();
        });

        static::restoring(function ($category) {
            $category->children()->withTrashed()->restore();
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }
}
