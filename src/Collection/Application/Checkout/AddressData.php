<?php

namespace Numista\Collection\Application\Checkout;

readonly class AddressData
{
    public function __construct(
        public string $label,
        public string $recipient_name,
        public string $street_address,
        public string $city,
        public string $postal_code,
        public string $country_code,
        public ?string $state,
        public ?string $phone,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'],
            recipient_name: $data['recipient_name'],
            street_address: $data['street_address'],
            city: $data['city'],
            postal_code: $data['postal_code'],
            country_code: $data['country_code'],
            state: $data['state'] ?? null,
            phone: $data['phone'] ?? null,
        );
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
