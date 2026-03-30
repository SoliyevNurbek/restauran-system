<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DiningTable;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $tables = DiningTable::all();
        $menuItems = MenuItem::all();

        if ($customers->isEmpty() || $tables->isEmpty() || $menuItems->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 8; $i++) {
            $status = $i > 4 ? 'paid' : 'preparing';
            $order = Order::updateOrCreate(
                ['order_number' => 'ORD-20260330-0'.$i],
                [
                    'customer_id' => $customers->random()->id,
                    'dining_table_id' => $tables->random()->id,
                    'status' => $status,
                    'notes' => 'Seeded demo order',
                    'paid_at' => $status === 'paid' ? now() : null,
                ]
            );

            $order->items()->delete();
            $selected = $menuItems->random(rand(2, 3));
            $subtotal = 0;

            foreach ($selected as $menuItem) {
                $qty = rand(1, 3);
                $lineTotal = $menuItem->price * $qty;
                $order->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $qty,
                    'unit_price' => $menuItem->price,
                    'line_total' => $lineTotal,
                ]);
                $subtotal += $lineTotal;
            }

            $tax = round($subtotal * 0.1, 2);
            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);
        }
    }
}
