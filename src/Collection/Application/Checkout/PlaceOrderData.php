<?php

namespace Numista\Collection\Application\Checkout;

readonly class PlaceOrderData
{
    public function __construct(
        public string $addressOption,
        public ?int $selectedAddressId,
        public ?AddressData $newAddress,
    ) {}

    public static function fromRequest(array $validatedData): self
    {
        return new self(
            addressOption: $validatedData['address_option'],
            selectedAddressId: $validatedData['selected_address_id'] ? (int) $validatedData['selected_address_id'] : null,
            newAddress: isset($validatedData['shipping_address']) ? AddressData::fromArray($validatedData['shipping_address']) : null,
        );
    }
}
