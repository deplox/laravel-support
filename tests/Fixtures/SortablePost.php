<?php

declare(strict_types=1);

namespace Deplox\Support\Tests\Fixtures;

use Deplox\Support\Database\Eloquent\Concerns\HasSorting;
use Illuminate\Database\Eloquent\Model;

final class SortablePost extends Model
{
    use HasSorting;

    public $timestamps = false;

    protected $guarded = [];
}
