<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermMeta extends Model
{
    use HasFactory;

     protected $table = 'wp_termmeta';
    protected $primaryKey = 'meta_id';
    public $timestamps = false;

    const COL_META_ID = 'meta_id';
    const COL_TERM_ID = 'term_id';
    const COL_META_KEY = 'meta_key';
    const COL_META_VALUE = 'meta_value';

    protected $fillable = [
        self::COL_TERM_ID,
        self::COL_META_KEY,
        self::COL_META_VALUE
    ];

    public function term()
    {
        return $this->belongsTo(Term::class, self::COL_TERM_ID, Term::COL_TERM_ID);
    }
}
