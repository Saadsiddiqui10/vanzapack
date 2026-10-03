<?php

namespace App\Http\Requests\Shop;

use App\Enums\PaymentMethod;
use App\Services\Payments\PaymentManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $enabledMethods = array_keys(app(PaymentManager::class)->enabled());

        return [
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:40'],
            'shipping_method_id' => ['required', 'exists:shipping_methods,id'],
            'payment_method' => ['required', new Enum(PaymentMethod::class), Rule::in($enabledMethods)],
            'customer_note' => ['nullable', 'string', 'max:1000'],

            'ship.first_name' => ['required', 'string', 'max:80'],
            'ship.last_name' => ['required', 'string', 'max:80'],
            'ship.phone' => ['required', 'string', 'max:40'],
            'ship.line1' => ['required', 'string', 'max:150'],
            'ship.line2' => ['nullable', 'string', 'max:150'],
            'ship.city' => ['required', 'string', 'max:80'],
            'ship.state' => ['nullable', 'string', 'max:80'],
            'ship.postal_code' => ['nullable', 'string', 'max:20'],
            'ship.country' => ['required', 'string', 'size:2'],

            'billing_same' => ['boolean'],
            'bill' => ['nullable', 'array'],
            'bill.first_name' => ['required_if:billing_same,false', 'nullable', 'string', 'max:80'],
            'bill.last_name' => ['required_if:billing_same,false', 'nullable', 'string', 'max:80'],
            'bill.phone' => ['required_if:billing_same,false', 'nullable', 'string', 'max:40'],
            'bill.line1' => ['required_if:billing_same,false', 'nullable', 'string', 'max:150'],
            'bill.line2' => ['nullable', 'string', 'max:150'],
            'bill.city' => ['required_if:billing_same,false', 'nullable', 'string', 'max:80'],
            'bill.state' => ['nullable', 'string', 'max:80'],
            'bill.postal_code' => ['nullable', 'string', 'max:20'],
            'bill.country' => ['required_if:billing_same,false', 'nullable', 'string', 'size:2'],

            'save_address' => ['boolean'],
        ];
    }

    public function payload(): array
    {
        $ship = $this->input('ship');
        $billingSame = $this->boolean('billing_same', true);

        return [
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'shipping_method_id' => (int) $this->input('shipping_method_id'),
            'payment_method' => $this->input('payment_method'),
            'customer_note' => $this->input('customer_note'),
            'shipping_address' => $ship,
            'billing_address' => $billingSame ? $ship : $this->input('bill'),
            'save_address' => $this->boolean('save_address'),
        ];
    }
}
