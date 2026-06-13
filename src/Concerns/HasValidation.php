<?php

declare(strict_types=1);

namespace Deplox\Support\Concerns;

use Illuminate\Contracts\Validation\Factory as ValidationFactoryContract;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

trait HasValidation
{
    protected ?ValidatorContract $validator = null;

    /**
     * Run the validator's rules against its data.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $data): array
    {
        $this->validator = $this->makeValidator($data);

        return $this->validator->validate();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    public function messages(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    public function attributes(): array
    {
        return [];
    }

    /** @param array<string, mixed> $data */
    protected function makeValidator(array $data): ValidatorContract
    {
        return $this->getValidationFactory()->make($data, $this->rules(), $this->messages(), $this->attributes());
    }

    protected function getValidationFactory(): ValidationFactoryContract
    {
        return app(ValidationFactoryContract::class);
    }
}
