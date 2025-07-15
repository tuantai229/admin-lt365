<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'status' => 0,
        'sort_order' => 9999,
    ];

    // Relationships
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function centerSubjects()
    {
        return $this->hasMany(CenterSubjectLink::class);
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubjectLink::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }
}