<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductFailuretest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_create_product_failure()
    {
        // Membuat user dengan peran admin dan mengautentikasinya
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        // Mengirim data yang tidak valid (misalnya, tanpa field 'name')
        $data = [
            'description' => 'Deskripsi Produk Test',
            'price' => 'bukan angka', // Invalid type
            'image' => ''
        ];

        $response = $this->postJson('/api/products', $data);

        // Periksa apakah status 422 (Unprocessable Entity) dikembalikan karena validasi gagal
        $response->assertStatus(422);

        // Memastikan pesan error muncul untuk field yang tidak valid
        $response->assertJsonValidationErrors(['name', 'price']);
    }

    public function test_show_all_products_unauthenticated()
    {
        // Mengirim request GET tanpa autentikasi
        $response = $this->getJson('/api/products');

        // Memastikan statusnya 401 (unauthorized)
        $response->assertStatus(401);
    }

    public function test_show_product_detail_not_found()
    {
        // Membuat user dengan peran 'user' dan mengautentikasinya
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        // ID produk yang tidak ada
        $nonExistentProductId = 9999;

        // Mengirim request GET ke endpoint dengan ID produk yang tidak ada
        $response = $this->getJson("/api/products/{$nonExistentProductId}");

        // Memastikan statusnya 404 (not found)
        $response->assertStatus(404);
    }

    public function test_update_product_failure_for_user_role()
    {
        // Membuat user dengan peran 'user' dan mengautentikasinya
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        // Membuat produk baru untuk diuji
        $product = Product::factory()->create([
            'name' => 'Produk Lama',
            'description' => 'Deskripsi Produk Lama',
            'price' => 1000,
        ]);

        // Data baru yang akan di-update
        $data = [
            'name' => 'Produk Gagal',
            'description' => 'Deskripsi Produk Gagal',
            'price' => 5000,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $data);

        // Memastikan bahwa status respons adalah 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_delete_product_failure_for_user_role()
    {
        // Membuat user dengan peran 'user' dan mengautentikasinya
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        // Membuat produk baru untuk diuji
        $product = Product::factory()->create();

        // Mengirim permintaan DELETE ke endpoint produk
        $response = $this->deleteJson("/api/products/{$product->id}");

        // Memastikan bahwa status respon adalah 403 (Forbidden)
        $response->assertStatus(403);

        // Memastikan bahwa produk masih ada di database dan belum dihapus
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_restore_product_failure_for_user_role()
    {
        // Membuat user dengan peran 'user' dan mengautentikasinya
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        // Membuat dan menghapus produk untuk diuji
        $product = Product::factory()->create();
        $product->delete();

        // Memastikan produk sudah soft deleted
        $this->assertSoftDeleted('products', ['id' => $product->id]);

        // Mengirim permintaan POST untuk restore produk
        $response = $this->postJson("/api/products/restore/{$product->id}");

        // Memastikan bahwa status respon adalah 403 (Forbidden)
        $response->assertStatus(403);

        // Memastikan produk tetap soft deleted
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
