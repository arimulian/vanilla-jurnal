<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class SalesService
{
     public function calculateFinalAmount(int $totalAmount, int $discount, int $tax): float
     {
          $discountAmount = ($totalAmount * $discount) / 100;
          $taxAmount = ($totalAmount * $tax) / 100;
          return $totalAmount - $discountAmount + $taxAmount;
     }

     public function create(array $data): array
     {
          return DB::transaction(function () use ($data) {
               // create sales
               $sales = Sales::create([
                    'total_amount' => $data['total_amount'],
                    'discount' => $data['discount'] ?? 0,
                    'tax' => $data['tax'] ?? 0,
                    'final_amount' => $this->calculateFinalAmount($data['total_amount'], $data['discount'], $data['tax']),
                    'status' => $data['status'],
                    'payment_method' => $data['payment_method'],
                    'branch_id' => $data['branch_id'],
               ]);

               // create detail sales
               foreach ($data['sales_items'] as $item) {
                    SalesItem::create([
                         'sales_id' => $sales->id,
                         'product_id' => $item['product_id'],
                         'quantity' => $item['quantity'],
                         'unit_price' => $item['unit_price'],

                    ]);
                    // Update product stock
                    Product::where('id', '=', $item['product_id'])
                         ->decrement('stock', amount: $item['quantity']);
               }

               return $sales->toArray();
          });
     }

     public function getAllSales(): array
     {
          return Sales::with(['salesItems.product', 'branch'])->get()->toArray();
     }
}
