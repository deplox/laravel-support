<?php

declare(strict_types=1);

use Deplox\Support\Concerns\HasValidation;
use Illuminate\Validation\ValidationException;

final class OrderValidator
{
    use HasValidation;

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
            'email' => ['required', 'email'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'e-mail address',
        ];
    }
}

test('validate() returns validated data when all rules pass', function (): void {
    $validator = new OrderValidator();

    $result = $validator->validate(['quantity' => 5, 'email' => 'test@example.com']);

    expect($result)->toBe(['quantity' => 5, 'email' => 'test@example.com']);
});

test('validate() throws ValidationException when rules fail', function (): void {
    $validator = new OrderValidator();

    expect(fn () => $validator->validate(['quantity' => 0, 'email' => 'not-an-email']))
        ->toThrow(ValidationException::class);
});

test('custom messages are included in the exception', function (): void {
    $validator = new OrderValidator();

    try {
        $validator->validate(['quantity' => 0, 'email' => 'a@b.com']);
    } catch (ValidationException $e) {
        expect($e->errors()['quantity'])->toContain('Quantity must be at least 1.');
    }
});

test('custom attribute name is used in error messages', function (): void {
    $validator = new OrderValidator();

    try {
        $validator->validate(['quantity' => 1, 'email' => 'bad']);
    } catch (ValidationException $e) {
        expect($e->errors()['email'][0])->toContain('e-mail address');
    }
});

test('rules() defaults to empty array', function (): void {
    $validator = new class {
        use HasValidation;
    };

    expect($validator->rules())->toBe([]);
});

test('messages() defaults to empty array', function (): void {
    $validator = new class {
        use HasValidation;
    };

    expect($validator->messages())->toBe([]);
});

test('attributes() defaults to empty array', function (): void {
    $validator = new class {
        use HasValidation;
    };

    expect($validator->attributes())->toBe([]);
});
