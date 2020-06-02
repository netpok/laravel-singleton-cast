<?php

namespace Tests\Unit;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testCastIsNotSingleton()
    {
        $user = new UnsavedModel();
        $now = (string) now();
        $user->not_singleton = $now;
        $user->save();

        $this->assertEquals($now, (string) $user->not_singleton);

        $notSingleton = $user->not_singleton;
        $notSingleton->addDays(7);

        $this->assertEquals($now, (string) $user->not_singleton);
    }

    public function testArrayIsNotSingleton()
    {
        $user = new UnsavedModel();
        $user->array = ['a' => 'foo'];

        $this->assertEquals(['a' => 'foo'], $user->array);

        $notSingleton = $user->array;
        $notSingleton['a'] = 'bar';

        $this->assertEquals(['a' => 'foo'], $user->array);
    }

    public function testCollectionIsNotSingleton()
    {
        $user = new UnsavedModel();
        $user->collection = ['a' => 'foo'];

        $this->assertEquals('foo', $user->collection->get('a'));

        $notSingleton = $user->collection;
        $notSingleton->put('a', 'bar');

        $this->assertEquals('foo', $user->collection->get('a'));
        $this->assertEquals('bar', $notSingleton->get('a'));
    }

    public function testCreatedAtIsNotSingleton()
    {
        $user = new UnsavedModel();
        $now = (string) now();
        $user->created_at = $now;

        $this->assertEquals($now, (string) $user->created_at);

        $notSingleton = $user->created_at;
        $notSingleton->addDays(7);

        $this->assertEquals($now, (string) $user->created_at);
    }
}

class UnsavedModel extends Model
{
    protected $dateFormat = Carbon::ISO8601;

    protected $casts = [
        'not_singleton' => DontSingleton::class,
        'array' => 'array',
        'collection' => 'collection',
    ];
}

class DontSingleton implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return Carbon::parse($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return (string) $value;
    }
}
