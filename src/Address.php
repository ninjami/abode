<?php

namespace Ninjami\Abode;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class Address implements Arrayable
{
    use Macroable;

    public ?string $street;
    public ?string $streetNumber;
    public ?string $zip;
    public ?string $city;
    public ?string $country;

    protected array $arrayableAttributes = [
        'street', 'streetNumber', 'zip', 'city', 'country'
    ];

    public function __construct(
        string $street = null,
        string $streetNumber = null,
        string $zip = null,
        string $city = null,
        string $country = null
    ) {
        $this->street = $street;
        $this->streetNumber = $streetNumber;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country ?? config('abode.country');
    }

    /**
     * Get the full address.
     *
     * @return string
     */
    public function full(): string
    {
        $values = Arr::only(
            $this->toArray(),
            ['street', 'streetNumber', 'zip', 'city']
        );

        return self::format(...array_values($values));
    }

    /**
     * Get the full address in a slug form.
     *
     * @param string $separator
     * @return string
     */
    public function slug($separator = '-'): string
    {
        return Str::slug($this->full(), $separator);
    }

    /**
     * Compare the address to the given value.
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
     * Compare the address to the given value.
     *
     * @param mixed $value
     * @return bool
     */
    public function isNot($value): bool
    {
        return !$this->is($value);
    }

    /**
     * Get items in an array
     *
     * @return array
     */
    public function toArray()
    {
        return collect($this->arrayableAttributes)
            ->mapWithKeys(function ($key) {
                $value = method_exists($this, $key)
                    ? (string) $this->{$key}()
                    : $this->$key;

                return [$key => $value ?: null];
            })
            ->toArray();
    }

    /**
     * Return the street as an AddressPart instance.
     *
     * @return AddressPart
     */
    public function street(): AddressPart
    {
        return new AddressPart('street', $this->street);
    }

    /**
     * Return the street as an AddressPart instance.
     *
     * @return AddressPart
     */
    public function streetNumber(): AddressPart
    {
        return new AddressPart('streetNumber', $this->street);
    }

    /**
     * Return the zip as an AddressPart instance.
     *
     * @return AddressPart
     */
    public function zip(): AddressPart
    {
        return new AddressPart('zip', $this->zip);
    }

    /**
     * Return the city as an AddressPart instance.
     *
     * @return AddressPart
     */
    public function city(): AddressPart
    {
        return new AddressPart('city', $this->city);
    }

    /**
     * Return the country as an AddressPart instance.
     *
     * @return AddressPart
     */
    public function country(): AddressPart
    {
        return new AddressPart('country', $this->country);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function __toString()
    {
        return $this->full();
    }

    /**
     * Format address to one string.
     *
     * @param string|null $street
     * @param string|null $streetNumber
     * @param string|null $zip
     * @param string|null $city
     * @return string
     */
    public static function format(
        string $street = null,
        string $streetNumber = null,
        string $zip = null,
        string $city = null
    ): string 
    {
        foreach (['street', 'streetNumber', 'zip', 'city'] as $attr) {
            $$attr = (string) Str::of($$attr)->lower()->title();
        }

        if ($street && $streetNumber) {
            $street = implode(' ', [$street, $streetNumber]);
        }

        if ($zip && $city) {
            $rest = implode(' ', [$zip, $city]);
        } else {
            $rest = $zip ?: $city ?: null;
        }

        if ($street && !empty($rest)) {
            return implode(', ', [$street, $rest]);
        } else if ($street && empty($rest)) {
            return $street;
        } else if (!empty($rest)) {
            return $rest;
        }

        return '';
    }
}
