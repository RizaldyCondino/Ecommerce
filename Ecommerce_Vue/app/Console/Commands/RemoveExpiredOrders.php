<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoveExpiredOrders extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-expired-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
     public function handle()
    {
        $expiredOrders = Order::where('status', 'unpaid')
            ->where('created_at', '<', Carbon::now()->subWeeks(1)) // adjust duration
            ->get();

        foreach ($expiredOrders as $order) {
            $order->delete();
        }

        $this->info('Expired unpaid orders removed successfully.');
    }
}
