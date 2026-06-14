<?php

declare(strict_types=1);

use Deplox\Support\Tests\Fixtures\Animal;
use Deplox\Support\Tests\Fixtures\Cat;
use Deplox\Support\Tests\Fixtures\Dog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    Schema::create('animals', function (Blueprint $table): void {
        $table->ulid('id')->primary();
        $table->string('name');
        $table->string('type');
    });
});

test('newFromBuilder hydrates the correct child class based on type column', function (): void {
    Dog::create(['name' => 'Rex']);
    Cat::create(['name' => 'Whiskers']);

    $animals = Animal::query()->orderBy('name')->get();

    expect($animals->first(fn ($a) => $a->name === 'Rex'))->toBeInstanceOf(Dog::class)
        ->and($animals->first(fn ($a) => $a->name === 'Whiskers'))->toBeInstanceOf(Cat::class);
});

test('classFromAlias resolves alias to fully qualified class name', function (): void {
    $animal = new Animal;

    expect($animal->classFromAlias('dog'))->toBe(Dog::class)
        ->and($animal->classFromAlias('cat'))->toBe(Cat::class);
});

test('classFromAlias returns input unchanged when not in childTypes map', function (): void {
    $animal = new Animal;

    expect($animal->classFromAlias('unknown'))->toBe('unknown');
});

test('classToAlias resolves class name to alias', function (): void {
    $animal = new Animal;

    expect($animal->classToAlias(Dog::class))->toBe('dog')
        ->and($animal->classToAlias(Cat::class))->toBe('cat');
});

test('getInheritanceColumn returns "type" by default', function (): void {
    expect((new Animal)->getInheritanceColumn())->toBe('type');
});

test('getChildTypes returns the configured map', function (): void {
    expect((new Animal)->getChildTypes())->toBe([
        'dog' => Dog::class,
        'cat' => Cat::class,
    ]);
});

test('Dog instances are filtered by global scope when queried via Dog', function (): void {
    Dog::create(['name' => 'Rex']);
    Cat::create(['name' => 'Whiskers']);

    $dogs = Dog::query()->get();

    expect($dogs)->toHaveCount(1)
        ->and($dogs->first()->name)->toBe('Rex');
});

test('children inherit the type column from their alias on creation', function (): void {
    $dog = Dog::create(['name' => 'Rex']);

    expect($dog->fresh()->type)->toBe('dog');
});

test('parentIsBooting returns false after parent has fully booted', function (): void {
    // Force boot of Animal by accessing it.
    Animal::query()->count();

    $reflection = new ReflectionMethod(Animal::class, 'parentIsBooting');

    expect($reflection->invoke(null))->toBeFalse();
});

// HasParent-specific behaviour

test('HasParent: getTable() uses the parent class name', function (): void {
    expect((new Dog)->getTable())->toBe('animals');
});

test('HasParent: getForeignKey() returns snake-case parent class name + primary key', function (): void {
    expect((new Dog)->getForeignKey())->toBe('animal_id');
});

test('HasParent: getMorphClass() delegates to the parent model', function (): void {
    expect((new Dog)->getMorphClass())->toBe(Animal::class);
});

test('HasParent: getClassNameForSerialization() returns the parent class name', function (): void {
    expect((new Dog)->getClassNameForSerialization())->toBe(Animal::class);
});

test('HasParent: hasParent() returns true', function (): void {
    expect((new Dog)->hasParent())->toBeTrue();
});

test('HasParent: joiningTable() returns alphabetically sorted snake_case pair', function (): void {
    expect((new Dog)->joiningTable(Cat::class))->toBe('animal_animal');
});

// HasChildren relationship methods

test('newInstance() returns parent class instance when inheritance column is absent', function (): void {
    $instance = (new Animal)->newInstance(['name' => 'Generic'], false);

    expect($instance)->toBeInstanceOf(Animal::class);
});

test('belongsTo() auto-detects parent FK when related model uses HasParent', function (): void {
    $relation = (new Animal)->belongsTo(Dog::class);

    expect($relation->getForeignKeyName())->toBe('animal_id');
});

test('hasMany() passes through to the base Eloquent relationship', function (): void {
    $relation = (new Animal)->hasMany(Dog::class);

    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('belongsToMany() auto-detects joining table when related model uses HasParent', function (): void {
    $relation = (new Animal)->belongsToMany(Cat::class);

    expect($relation->getTable())->toBe('animal_animal');
});

test('getInheritanceColumn() returns custom childColumn when declared on the model', function (): void {
    $model = new class extends Animal {
        protected string $childColumn = 'entity_type';
    };

    expect($model->getInheritanceColumn())->toBe('entity_type');
});

test('classFromAlias() resolves a BackedEnum value to the mapped class name', function (): void {
    enum AnimalType: string {
        case Dog = 'dog';
        case Cat = 'cat';
    }

    $animal = new Animal;

    expect($animal->classFromAlias(AnimalType::Dog))->toBe(Dog::class)
        ->and($animal->classFromAlias(AnimalType::Cat))->toBe(Cat::class);
});

test('classToAlias() returns input unchanged when class not in map', function (): void {
    expect((new Animal)->classToAlias('App\\Models\\Bird'))->toBe('App\\Models\\Bird');
});

test('parentHasHasChildrenTrait() returns false when model does not use HasChildren', function (): void {
    $model = new class extends \Illuminate\Database\Eloquent\Model {
        use \Deplox\Support\Database\Eloquent\Concerns\HasParent;
    };

    expect($model->parentHasHasChildrenTrait())->toBeFalse();
});

test('getClassNameForRelationships() on HasParent delegates to parent class basename', function (): void {
    expect((new Dog)->getClassNameForRelationships())->toBe('Animal');
});
