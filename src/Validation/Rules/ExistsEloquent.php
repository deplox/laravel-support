<?php

declare(strict_types=1);

namespace Deplox\Support\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Inspired by https://github.com/korridor/laravel-model-validation-rules
 */
final class ExistsEloquent implements ValidationRule
{
    private ?Closure $builderClosure = null;

    private ?string $customMessage = null;

    private ?string $customMessageTranslationKey = null;

    /**
     * @param class-string<\Illuminate\Database\Eloquent\Model> $model
     */
    public function __construct(
        private readonly string $model,
        private readonly ?string $key = null,
        ?Closure $builderClosure = null,
    ) {
        $this->builderClosure = $builderClosure;
    }

    public function withMessage(string $message): self
    {
        $this->customMessage = $message;

        return $this;
    }

    public function withCustomTranslation(string $translationKey): self
    {
        $this->customMessageTranslationKey = $translationKey;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $builder = new $this->model();
        $modelKeyName = $builder->getKeyName();
        $builder = $builder->where($this->key ?? $modelKeyName, $value);

        if ($this->builderClosure !== null) {
            $builder = ($this->builderClosure)($builder);
        }

        if ($builder->doesntExist()) {
            if ($this->customMessage !== null) {
                $fail($this->customMessage);
            } else {
                $fail($this->customMessageTranslationKey ?? 'support::validation.exists_model')->translate([
                    'attribute' => $attribute,
                    'model' => mb_strtolower(class_basename($this->model)),
                    'value' => $value,
                ]);
            }
        }
    }

    public function setBuilderClosure(?Closure $builderClosure): void
    {
        $this->builderClosure = $builderClosure;
    }

    public function query(Closure $builderClosure): self
    {
        $this->builderClosure = $builderClosure;

        return $this;
    }
}
