<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermRelationship extends Model
{
    use HasFactory;

    protected $table = 'wp_term_relationships';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    const COL_OBJECT_ID = 'object_id';
    const COL_TERM_TAXONOMY_ID = 'term_taxonomy_id';
    const COL_TERM_ORDER = 'term_order';

    protected $fillable = [
        self::COL_OBJECT_ID,
        self::COL_TERM_TAXONOMY_ID,
        self::COL_TERM_ORDER
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, self::COL_OBJECT_ID, Post::COL_ID);
    }

    public function termTaxonomy()
    {
        return $this->belongsTo(TermTaxonomy::class, self::COL_TERM_TAXONOMY_ID, TermTaxonomy::COL_TERM_TAXONOMY_ID);
    }
}
