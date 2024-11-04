<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProcessProductImage implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Ambil konten gambar dari storage
            $imageContent = Storage::get("products/{$this->data['image']}");

            // Simpan data produk ke database
            Product::create([
                'name' => $this->data['name'],
                'description' => $this->data['description'],
                'price' => $this->data['price'],
                'image' => $this->data['image'], // Simpan nama file
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to process image: " . $e->getMessage());
        }
    }
}
