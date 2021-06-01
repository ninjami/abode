<?php

namespace Ninjami\Abode;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Macroable;

class AddressPart
{
    use Macroable;

    protected string $type;
    protected ?string $value;

    /**
     * The AddressPart constructor.
     *
     * @param string $type
     * @param string|null $value
     */
    public function __construct(string $type, ?string $value = null)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * Get the type of the address part.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the value of the address part.
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Return the address part as a Stringable instance.
     *
     * @return \Illuminate\Support\Stringable
     */
    public function str(): Stringable
    {
        return Str::of($this->value);
    }

    /**
     * Get the address part in slug form.
     *
     * @param string $separator
     * @return string
     */
    public function slug($separator = '-'): string
    {
        return $this->str()->slug($separator);
    }

    /**
     * Compare the address part to given value.
     *
     * @param mixed $value
     * @return bool
     */
    public function is($value): bool
    {
        $value = $value instanceof self
            ? $value->slug()
            : Str::slug($value);

        return Str::is($this->slug(), $value);
    }

    /**
     * Compare the address part to given value.
     *
     * @param mixed $value
     * @return bool
     */
    public function isNot($value): bool
    {
        return !$this->is($value);
    }

    public function __toString()
    {
        return $this->value ?: '';
    }    
}