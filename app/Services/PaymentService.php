<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Quote;
use App\Models\User;
use App\Notifications\PaymentRejectedNotification;
use App\Notifications\PaymentSubmittedNotification;
use App\Notifications\PaymentVerifiedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function submitPayment(
        Quote $quote,
        User $customer,
        ?string $referenceNo,
        ?UploadedFile $proof,
        ?string $notes,
    ): Payment {
        if ($quote->status !== 'accepted') {
            throw new \InvalidArgumentException('Quote must be accepted before payment.');
        }

        if ($quote->customer_id !== $customer->id) {
            throw new \InvalidArgumentException('You can only pay your own quotes.');
        }

        if ($quote->isFullyPaid()) {
            throw new \InvalidArgumentException('This quote is already fully paid.');
        }

        if ($quote->hasPendingPayment()) {
            throw new \InvalidArgumentException('A payment is already awaiting verification for this quote.');
        }

        $proofPath = null;
        if ($proof) {
            $proofPath = $proof->store('payment-proofs', 'public');
        }

        return DB::transaction(function () use ($quote, $customer, $referenceNo, $proofPath, $notes) {
            $payment = Payment::create([
                'quote_id' => $quote->id,
                'service_job_id' => $quote->service_job_id,
                'customer_id' => $customer->id,
                'method' => 'gcash',
                'amount' => $quote->total,
                'reference_no' => $referenceNo,
                'proof_path' => $proofPath,
                'status' => Payment::STATUS_PENDING,
                'notes' => $notes,
            ]);

            User::role('owner')->get()
                ->each(fn (User $owner) => $owner->notify(new PaymentSubmittedNotification($payment)));

            return $payment;
        });
    }

    public function verify(Payment $payment, User $owner): Payment
    {
        if (! $owner->hasRole('owner')) {
            throw new \InvalidArgumentException('Only owners can verify payments.');
        }

        if ($payment->status !== Payment::STATUS_PENDING) {
            throw new \InvalidArgumentException('Only pending payments can be verified.');
        }

        return DB::transaction(function () use ($payment, $owner) {
            $payment->update([
                'status' => Payment::STATUS_VERIFIED,
                'verified_by' => $owner->id,
                'verified_at' => now(),
            ]);

            $payment->quote->update(['payment_status' => 'paid']);

            $payment->customer->notify(new PaymentVerifiedNotification($payment));

            return $payment->fresh();
        });
    }

    public function reject(Payment $payment, User $owner, string $reason): Payment
    {
        if (! $owner->hasRole('owner')) {
            throw new \InvalidArgumentException('Only owners can reject payments.');
        }

        if ($payment->status !== Payment::STATUS_PENDING) {
            throw new \InvalidArgumentException('Only pending payments can be rejected.');
        }

        return DB::transaction(function () use ($payment, $owner, $reason) {
            $payment->update([
                'status' => Payment::STATUS_REJECTED,
                'verified_by' => $owner->id,
                'verified_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $payment->customer->notify(new PaymentRejectedNotification($payment));

            return $payment->fresh();
        });
    }

    public function deleteProof(Payment $payment): void
    {
        if ($payment->proof_path) {
            Storage::disk('public')->delete($payment->proof_path);
        }
    }
}
