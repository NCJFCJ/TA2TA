<?php

namespace IAWPSCOPED\Illuminate\Database\Eloquent\Relations;

use IAWPSCOPED\Illuminate\Database\Eloquent\Model;
use IAWPSCOPED\Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
/** @internal */
class Pivot extends Model
{
    use AsPivot;
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = \false;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
