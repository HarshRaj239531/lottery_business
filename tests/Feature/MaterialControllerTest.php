<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\MaterialController;
use App\Models\Material;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->string('unit');
            $table->string('image_url', 1000)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('materials');

        parent::tearDown();
    }

    public function test_store_saves_uploaded_image_url_to_database(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('material.jpg');

        $request = Request::create('/api/admin/materials', 'POST', [
            'name' => 'Cement',
            'price' => '1200',
            'unit' => 'bag',
            'status' => 'active',
        ], [], [
            'image' => $file,
        ]);

        $controller = new MaterialController();
        $response = $controller->store($request);

        $this->assertTrue($response->getData(true)['success']);

        $material = Material::latest()->first();
        $this->assertNotNull($material);
        $this->assertNotNull($material->image_url);
        $this->assertStringContainsString('/storage/', $material->image_url);

        $path = ltrim(parse_url($material->image_url, PHP_URL_PATH), '/');
        $path = preg_replace('/^storage\//', '', $path, 1);
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
    }
}
