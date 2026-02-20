<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    use HasFactory;

    protected $table = 'wp_term_taxonomy';
    protected $primaryKey = 'term_taxonomy_id';
    public $timestamps = false;

    const COL_TERM_TAXONOMY_ID = 'term_taxonomy_id';
    const COL_TERM_ID = 'term_id';
    const COL_TAXONOMY = 'taxonomy';
    const COL_DESCRIPTION = 'description';
    const COL_PARENT = 'parent';
    const COL_COUNT = 'count';

    protected $fillable = [
        self::COL_TERM_ID,
        self::COL_TAXONOMY,
        self::COL_DESCRIPTION,
        self::COL_PARENT,
        self::COL_COUNT
    ];

    public function term()
    {
        return $this->belongsTo(Term::class, self::COL_TERM_ID, Term::COL_TERM_ID);
    }

    public function parent()
    {
        return $this->belongsTo(TermTaxonomy::class, self::COL_PARENT, self::COL_TERM_TAXONOMY_ID);
    }

    public function children()
    {
        return $this->hasMany(TermTaxonomy::class, self::COL_PARENT, self::COL_TERM_TAXONOMY_ID);
    }
}
