<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guardian extends Model
{
    use HasFactory;

    const RELATION_TYPES = ['father', 'mother', 'sister', 'brother', 'husband', 'wife', 'ancle', 'aunt'];

    protected $fillable = ['name', 'contact_number', 'relation_type'];



    public function student(): BelongsToMany{
        return $this->belongsToMany(Student::class);
    }
}
