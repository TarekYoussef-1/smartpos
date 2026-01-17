<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use ArPHP\I18N\Arabic;
use Illuminate\Support\Facades\Log;

class ArabicReceiptPrinter
{
    protected $printerName;

    public function __construct($printerName = 'XP-80C')
    {
        $this->printerName = $printerName;
    }

    public function print($order)
    {
        try {
            $connector = new WindowsPrintConnector($this->printerName);
            $printer = new Printer($connector);

            // ==== تحويل العربي لأشكال متصلة ====
            $arabic = new Arabic();

            // ===== Header =====
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text($arabic->utf8Glyphs("DAGAGOO\n"));
            $printer->text($arabic->utf8Glyphs("فاتورة مبيعات\n"));
            $printer->setEmphasis(false);
            $printer->text(str_repeat("-", 32) . "\n");

            // ===== معلومات الطلب =====
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text($arabic->utf8Glyphs("رقم الفاتورة: {$order->daily_serial}\n"));
            $printer->text($arabic->utf8Glyphs("التاريخ: " . $order->created_at->format('d-m-Y H:i') . "\n"));
            $printer->text($arabic->utf8Glyphs("النوع: " . ($order->type == 'dine_in' ? 'صالة' : ($order->type == 'take_away' ? 'تيك أواي' : 'دليفري')) . "\n"));
            $printer->text($arabic->utf8Glyphs("الكاشير: " . ($order->cashier->name ?? '---') . "\n"));
            $printer->text(str_repeat("-", 32) . "\n");

            // ===== الأصناف =====
            foreach ($order->items as $item) {
                $printer->text($arabic->utf8Glyphs($item->product->name ?? $item->name) . "\n");
                $printer->text($arabic->utf8Glyphs(" {$item->quantity} x {$item->price} = " . ($item->quantity * $item->price)) . "\n");
            }

            $printer->text(str_repeat("-", 32) . "\n");

            // ===== الإجمالي =====
            $printer->setEmphasis(true);
            $printer->text($arabic->utf8Glyphs("الإجمالي: " . number_format($order->total, 2) . " ج.م\n"));
            $printer->setEmphasis(false);

            // ===== Footer =====
            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($arabic->utf8Glyphs("شكراً لزيارتكم\n"));
            $printer->feed(2);
            $printer->cut();
            $printer->close();

            return true;

        } catch (\Throwable $e) {
            Log::warning('Cashier printer failed', [
                'order_id' => $order->id,
                'printer' => $this->printerName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
