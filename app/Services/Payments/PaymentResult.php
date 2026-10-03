<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;

class PaymentResult
{
    public function __construct(
        public PaymentStatus $status,
        public ?string $redirectUrl = null,
        public ?string $reference = null,
        public array $meta = [],
        public ?string $customerMessage = null,
    ) {}

    public static function pending(?string $message = null): self
    {
        return new self(PaymentStatus::Pending, customerMessage: $message);
    }

    public static function awaitingConfirmation(?string $message = null): self
    {
        return new self(PaymentStatus::AwaitingConfirmation, customerMessage: $message);
    }

    public static function paid(?string $reference = null): self
    {
        return new self(PaymentStatus::Paid, reference: $reference);
    }

    public static function redirect(string $url): self
    {
        return new self(PaymentStatus::Pending, redirectUrl: $url);
    }
}
