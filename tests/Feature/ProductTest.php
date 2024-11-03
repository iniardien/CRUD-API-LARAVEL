<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_create_product_success()
    {
        // Membuat user dengan peran admin dan mengautentikasinya
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        $data = [
            'name' => 'Produk Test',
            'description' => 'Deskripsi Produk Test',
            'price' => 1000,
            'image' => ''
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201);
        $response->assertJson(['name' => 'Produk Test']);
    }

    public function test_show_all_products_for_user()
    {
        // Membuat user dengan peran 'user' dan mengautentikasinya
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        // Membuat beberapa produk contoh untuk diuji
        Product::factory()->count(5)->create();

        // Kirim request GET ke endpoint untuk menampilkan semua produk
        $response = $this->getJson('/api/products');

        // Periksa apakah response status 200 (berhasil) dan memastikan data produk dikembalikan
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'description',
                'price',
                'image',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_show_product_detail_for_user()
    {
        // Membuat user dengan peran 'user' dan mengautentikasinya
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        // Membuat produk contoh untuk diuji
        $product = Product::factory()->create();

        // Kirim request GET ke endpoint untuk menampilkan detail produk
        $response = $this->getJson("/api/products/{$product->id}");

        // Periksa apakah response status 200 (berhasil) dan memastikan data produk spesifik dikembalikan
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => $product->image,
        ]);
    }
    public function test_update_product_success()
    {
        // Membuat user dengan peran admin dan mengautentikasinya
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        // Membuat produk baru
        $product = Product::factory()->create([
            'name' => 'Produk Lama',
            'description' => 'Deskripsi Produk Lama',
            'price' => 1000,
        ]);

        // Data baru yang akan di-update
        $data = [
            '_method' => 'PUT',
            'name' => 'Produk Baru',
            'description' => 'Deskripsi Produk Baru',
            'price' => 2000,
        ];

        // Mengirim permintaan POST dengan Method PUT pada params ke endpoint update produk
        $response = $this->postJson("/api/products/{$product->id}", $data);

        // Memastikan respon berhasil
        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'Produk Baru',
            'description' => 'Deskripsi Produk Baru',
            'price' => 2000,
        ]);
    }

    public function test_delete_product_success()
    {
        // Membuat user dengan peran admin dan mengautentikasinya
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        // Membuat produk baru
        $product = Product::factory()->create();

        // Mengirim permintaan DELETE ke endpoint produk
        $response = $this->deleteJson("/api/products/{$product->id}");

        // Memastikan respon berhasil (status 200) dan produk terhapus
        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }


    public function test_restore_product_success()
    {
        // Membuat user dengan peran admin dan mengautentikasinya
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        // Membuat dan menghapus produk
        $product = Product::factory()->create();
        $product->delete();

        // Memastikan produk sudah soft deleted
        $this->assertSoftDeleted('products', ['id' => $product->id]);

        // Mengirim permintaan POST untuk restore produk
        $response = $this->postJson("/api/products/restore/{$product->id}");

        // Memastikan respon berhasil (status 200) dan produk dikembalikan
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    }

    
}
