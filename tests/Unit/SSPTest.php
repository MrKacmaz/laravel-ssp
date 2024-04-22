<?php

namespace Tests\Unit;

use Mrkacmaz\LaravelSsp\Traits\SSP;
use Mrkacmaz\LaravelSsp\Exceptions\InvalidModelInstanceException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ExampleModel extends Model {
    protected $fillable = ['name'];
}

class SSPTest extends TestCase
{
    use SSP;

    public function testProcessSSPWithValidModel()
    {
        // Model örneği oluştur
        $model = new ExampleModel();

        // HTTP request simülasyonu
        $request = Request::create('/test', 'GET', [
            'search' => ['value' => 'test'],
            'order' => [['column' => 0, 'dir' => 'asc']],
            'start' => 0,
            'length' => 10,
        ]);

        // processSSP metodunu çağır
        $result = self::processSSP($request, ExampleModel::class);

        // Sonuçları kontrol et
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    public function testProcessSSPWithInvalidModel()
    {
        // Yanlış model sınıf ismi
        $this->expectException(InvalidModelInstanceException::class);

        // HTTP request simülasyonu
        $request = Request::create('/test', 'GET', [
            'search' => ['value' => 'test'],
            'order' => [['column' => 0, 'dir' => 'asc']],
            'start' => 0,
            'length' => 10,
        ]);

        // processSSP metodunu çağır
        self::processSSP($request, 'NonExistentModel');
    }
}
