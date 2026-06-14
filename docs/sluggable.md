# HasSlugs

Auto-generates URL-friendly slug columns whenever a model is saved.

## Basic usage

The default mapping is `name → slug`. No configuration needed for the common case:

```php
use Deplox\Support\Database\Eloquent\Concerns\HasSlugs;

final class Post extends Model
{
    use HasSlugs;
}

Post::create(['name' => 'Hello World'])->slug; // 'hello-world'
```

## Customising the mapping

Override `getSluggable()` to return an `array<string, string>` of `source → target` pairs:

```php
public function getSluggable(): array
{
    return ['title' => 'slug'];
}
```

Multiple slug columns from different sources:

```php
public function getSluggable(): array
{
    return [
        'title'    => 'slug',
        'subtitle' => 'sub_slug',
    ];
}
```

## Custom slugification

Override the static `slugify()` method to apply your own logic (e.g. transliteration):

```php
public static function slugify(string $value): string
{
    return Str::slug(transliterate($value));
}
```

## Notes

- The trait hooks into the `saving` event, so slugs are regenerated on every save — including updates.
- `getSluggable()` is a method (not a property) to avoid PHP 8.4 trait property conflicts with `final` classes.
- You can still set `protected $sluggable = ['title' => 'slug']` and the default `getSluggable()` will pick it up via `$this->sluggable ?? ['name' => 'slug']`.
