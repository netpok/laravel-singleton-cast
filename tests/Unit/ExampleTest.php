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
        $user                = new UnsavedModel();
        $now                 = (string) now();
        $user->not_singleton = $now;

        $this->assertEquals($now, (string) $user->not_singleton);

        $notSingleton = $user->not_singleton;
        $notSingleton->addDays(7);

        $this->assertEquals($now, (string) $user->not_singleton);
    }
}

class UnsavedModel extends Model
{
    protected $casts = [
        'not_singleton' => DontSingleton::class,
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
