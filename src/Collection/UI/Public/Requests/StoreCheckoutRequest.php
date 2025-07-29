<?php

namespace Numista\Collection\UI\Public\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address_option' => 'required|string|in:existing,new',
            'selected_address_id' => [
                'nullable',
                'required_if:address_option,existing',
                Rule::exists('addresses', 'id')->where('customer_id', $this->user()->customer->id),
            ],
            'shipping_address.label' => 'required_if:address_option,new|string|max:255',
            'shipping_address.recipient_name' => 'required_if:address_option,new|string|max:255',
            'shipping_address.street_address' => 'required_if:address_option,new|string|max:255',
            'shipping_address.city' => 'required_if:address_option,new|string|max:255',
            'shipping_address.postal_code' => 'required_if:address_option,new|string|max:20',
            'shipping_address.country_code' => 'required_if:address_option,new|string|size:2',
            'shipping_address.state' => 'nullable|string|max:255',
            'shipping_address.phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'shipping_address.label' => trans('validation.attributes.shipping_address.label'),
            'shipping_address.recipient_name' => trans('validation.attributes.shipping_address.recipient_name'),
            'shipping_address.street_address' => trans('validation.attributes.shipping_address.street_address'),
            'shipping_address.city' => trans('validation.attributes.shipping_address.city'),
            'shipping_address.postal_code' => trans('validation.attributes.shipping_address.postal_code'),
            'shipping_address.country_code' => trans('validation.attributes.shipping_address.country_code'),
        ];
    }
}
