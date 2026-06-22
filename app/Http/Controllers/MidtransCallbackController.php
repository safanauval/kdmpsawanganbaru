<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        // 1. Verifikasi Signature Key
        if (!$this->verifySignatureKey($payload)) {
            Log::warning('Midtrans callback: Invalid signature', $payload);
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // 2. Ambil data dari payload
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        if (!$orderId || !$transactionStatus) {
            return response()->json(['error' => 'Missing order_id or transaction_status'], 400);
        }

        // 3. Cari order di database
        $order = Order::where('order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // 4. Update status berdasarkan transaction_status dan fraud_status
        $newStatus = $this->mapTransactionStatus($transactionStatus, $fraudStatus);
        $order->update(['payment_status' => $newStatus]);

        Log::info("Midtrans callback: Order {$orderId} updated to {$newStatus}");

        return response()->json(['ok' => true]);
    }

    /**
     * Verifikasi signature key dari Midtrans
     */
    private function verifySignatureKey(array $payload): bool
    {
        $serverKey = config('services.midtrans.server_key');
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        // Buat string yang akan di-hash
        $input = $orderId . $statusCode . $grossAmount . $serverKey;

        // Hash dengan SHA512
        $generatedSignature = hash('sha512', $input);

        // Bandingkan dengan signature_key dari payload
        return hash_equals($signatureKey, $generatedSignature);
    }

    /**
     * Mapping transaction_status + fraud_status menjadi status internal
     */
    private function mapTransactionStatus(string $transactionStatus, string $fraudStatus): string
    {
        // Jika transaksi capture, settlement, atau pending dengan fraud accept → paid
        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            return 'paid';
        }
        if ($transactionStatus === 'settlement') {
            return 'paid';
        }

        // Jika pending
        if ($transactionStatus === 'pending') {
            return 'pending';
        }

        // Jika deny, expire, cancel, atau capture dengan fraud challenge/fail → failed
        if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            return 'failed';
        }
        if ($transactionStatus === 'capture' && in_array($fraudStatus, ['challenge', 'fail'])) {
            return 'failed';
        }

        // Default fallback
        return 'pending';
    }
}