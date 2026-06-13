<?php

declare(strict_types=1);

namespace Deplox\Support\Tests\Fixtures;

use Deplox\Support\Concerns\HasValidation;

final class SimpleFormRequest
{
    use HasValidation;

    /** @return array<string, string> */
    public function rules(): array
    {
        return ['name' => 'required|string'];
    }
}
