<?php

declare(strict_types=1);

namespace Deplox\Support\Database\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;
use ReflectionClass;

/**
 * Inspired by https://github.com/tighten/parental
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasParent
{
    public static function bootHasParent(): void
    {
        static::creating(function ($model): void {
            if ($model->parentHasHasChildrenTrait()) {
                $model->forceFill(
                    [$model->getInheritanceColumn() => $model->classToAlias($model::class)]
                );
            }
        });

        static::addGlobalScope(function ($query): void {
            $instance = new static;

            if ($instance->parentHasHasChildrenTrait()) {
                $query->where($query->getModel()->getTable().'.'.$instance->getInheritanceColumn(), $instance->classToAlias($instance::class));
            }
        });
    }

    /**
     * Indicates the model uses the HasParent trait.
     *
     * Implemented as a method (rather than a property) to avoid PHP 8.4 trait property
     * conflicts with final classes that may redefine the same name.
     */
    public function hasParent(): bool
    {
        return true;
    }

    public function parentHasHasChildrenTrait(): bool
    {
        return $this->hasChildren ?? false;
    }

    public function getTable(): string
    {
        if (! isset($this->table)) {
            return str_replace('\\', '', Str::snake(Str::plural(class_basename($this->getParentClass()))));
        }

        return $this->table;
    }

    public function getForeignKey(): string
    {
        return Str::snake(class_basename($this->getParentClass())).'_'.$this->primaryKey;
    }

    /**
     * @param  string  $related
     * @param  null|Model  $instance
     */
    public function joiningTable($related, $instance = null): string
    {
        $relatedInstance = new $related;
        $relatedClassName = method_exists($relatedInstance, 'getClassNameForRelationships')
            ? $relatedInstance->getClassNameForRelationships()
            : class_basename($related);

        $models = [
            Str::snake($relatedClassName),
            Str::snake($this->getClassNameForRelationships()),
        ];

        sort($models);

        return mb_strtolower(implode('_', $models));
    }

    public function getClassNameForRelationships(): string
    {
        return class_basename($this->getParentClass());
    }

    /**
     * Get the class name for polymorphic relations.
     */
    public function getMorphClass(): string
    {
        $parentClass = $this->getParentClass();

        return (new $parentClass)->getMorphClass();
    }

    /**
     * Get the class name for poly-type collections.
     */
    public function getClassNameForSerialization(): string
    {
        return $this->getParentClass();
    }

    /**
     * Get the parent class name. Per-class static cache (PHP 8.1+ trait statics are bound per using-class).
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected function getParentClass(): string
    {
        static $parentClassName;

        if ($parentClassName !== null) {
            return $parentClassName;
        }

        $parent = (new ReflectionClass($this))->getParentClass();
        $name = $parent !== false ? $parent->getName() : static::class;

        if (! is_a($name, Model::class, true)) {
            throw new LogicException(static::class.' parent class must extend '.Model::class.'.');
        }

        return $parentClassName = $name;
    }
}
