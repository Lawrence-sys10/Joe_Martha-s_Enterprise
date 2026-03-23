<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Product;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚠️ Low Stock Alert - ' . $this->product->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a low stock alert for your product:')
            ->line('**Product:** ' . $this->product->name)
            ->line('**SKU:** ' . $this->product->sku)
            ->line('**Current Stock:** ' . $this->product->stock_quantity . ' ' . $this->product->unit . 's')
            ->line('**Minimum Stock Level:** ' . $this->product->minimum_stock . ' ' . $this->product->unit . 's')
            ->line('**Shortage:** ' . ($this->product->minimum_stock - $this->product->stock_quantity) . ' ' . $this->product->unit . 's')
            ->line('**Category:** ' . ($this->product->category->name ?? 'Uncategorized'))
            ->action('View Product', url('/products/' . $this->product->id))
            ->line('Please restock soon to avoid running out of inventory!')
            ->line('Thank you for using JM-EMS!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'current_stock' => $this->product->stock_quantity,
            'minimum_stock' => $this->product->minimum_stock,
            'shortage' => $this->product->minimum_stock - $this->product->stock_quantity,
            'message' => "Low stock alert: {$this->product->name} has only {$this->product->stock_quantity} units left (Minimum: {$this->product->minimum_stock})",
            'type' => 'low_stock',
            'severity' => 'warning'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->product->stock_quantity,
            'message' => "Low stock alert: {$this->product->name} has only {$this->product->stock_quantity} units left",
        ]);
    }
}