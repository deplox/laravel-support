<?php

declare(strict_types=1);

namespace Deplox\Support\Tests\Fixtures;

use Deplox\Support\Database\Eloquent\Concerns\HasSearch;
use Illuminate\Database\Eloquent\Model;

final class SearchablePost extends Model
{
    use HasSearch;

    public $timestamps = false;

    protected $guarded = [];
}
