<?php

namespace Tests\Feature;

use App\Models\EvaluasiKinerja;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use ZipArchive;

class EvaluasiKinerjaTest extends TestCase
{
    private User $admin;
    private User $pegawai;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->admin = User::factory()->create(['role' => 'Admin', 'name' => 'Administrator']);
        $this->pegawai = User::factory()->create(['role' => 'Pegawai', 'name' => 'Pegawai Satu']);
    }

    private function payload(array $changes = []): array
    {
        return array_replace(['user_id' => $this->pegawai->id, 'nilai' => 'Baik', 'keterangan' => 'Target tercapai.'], $changes);
    }

    public function test_admin_can_create_read_update_and_delete_evaluations(): void
    {
        $this->actingAs($this->admin)->post(route('evaluasi-kinerja.store'), $this->payload())
            ->assertSessionHasNoErrors()->assertRedirect(route('evaluasi-kinerja.index'));
        $record = EvaluasiKinerja::firstOrFail();
        $this->get('/evaluasi-kinerja')->assertOk()->assertSeeText('Pegawai Satu')->assertSeeText('Target tercapai.');
        $this->put(route('evaluasi-kinerja.update', $record), $this->payload(['nilai' => 'Sangat Baik', 'keterangan' => 'Melebihi target.']))
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('Sangat Baik', $record->fresh()->nilai);
        $this->assertSame('Melebihi target.', $record->fresh()->keterangan);
        $this->delete(route('evaluasi-kinerja.destroy', $record))->assertRedirect(route('evaluasi-kinerja.index'));
        $this->assertDatabaseMissing('evaluasi_kinerjas', ['id' => $record->id]);
    }

    public function test_only_supported_ratings_and_existing_employees_are_accepted(): void
    {
        $this->actingAs($this->admin);
        foreach ([['nilai' => 'Buruk'], ['nilai' => ''], ['nilai' => ['Baik']], ['user_id' => 999999], ['user_id' => null], ['keterangan' => ['invalid']], ['keterangan' => str_repeat('a', 5001)]] as $changes) {
            $this->postJson(route('evaluasi-kinerja.store'), $this->payload($changes))->assertUnprocessable();
        }
        foreach (EvaluasiKinerja::NILAI as $nilai) {
            $this->post(route('evaluasi-kinerja.store'), $this->payload(['nilai' => $nilai, 'keterangan' => null]))->assertSessionHasNoErrors();
            $this->assertDatabaseHas('evaluasi_kinerjas', ['nilai' => $nilai, 'keterangan' => null]);
        }
        $this->assertDatabaseCount('evaluasi_kinerjas', 3);
    }

    public function test_employee_can_read_only_own_data_and_cannot_write(): void
    {
        $own = EvaluasiKinerja::create($this->payload());
        $other = EvaluasiKinerja::create($this->payload(['user_id' => $this->admin->id, 'keterangan' => 'Rahasia pegawai lain']));
        $response = $this->actingAs($this->pegawai)->get('/evaluasi-kinerja?user_id='.$this->admin->id)
            ->assertOk()->assertSeeText('Target tercapai.')->assertDontSeeText('Rahasia pegawai lain')->assertDontSee('id="evaluasiForm"', false);
        $this->assertSame(1, $response->viewData('evaluasis')->total());
        $this->post(route('evaluasi-kinerja.store'), $this->payload())->assertForbidden();
        $this->put(route('evaluasi-kinerja.update', $own), $this->payload(['nilai' => 'Sangat Baik']))->assertForbidden();
        $this->delete(route('evaluasi-kinerja.destroy', $other))->assertForbidden();
        $this->assertSame('Baik', $own->fresh()->nilai);
        $this->assertDatabaseCount('evaluasi_kinerjas', 2);
    }

    public function test_guests_and_missing_records_are_handled(): void
    {
        $this->get('/evaluasi-kinerja')->assertRedirect('/login');
        $this->post(route('evaluasi-kinerja.store'), $this->payload())->assertRedirect('/login');
        $this->actingAs($this->admin)->put('/evaluasi-kinerja/99999', $this->payload())->assertNotFound();
        $this->delete('/evaluasi-kinerja/99999')->assertNotFound();
    }

    public function test_empty_state_navigation_and_safe_text_rendering(): void
    {
        $response = $this->actingAs($this->admin)->get('/evaluasi-kinerja')->assertOk()->assertSeeText('Belum ada data evaluasi kinerja.');
        $this->assertGreaterThan(strpos($response->getContent(), '>Absensi<'), strpos($response->getContent(), '>Evaluasi Kinerja<'));
        EvaluasiKinerja::create($this->payload(['keterangan' => '<script>alert(1)</script>']));
        $this->get('/evaluasi-kinerja')->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_export_respects_employee_scope(): void
    {
        EvaluasiKinerja::create($this->payload());
        EvaluasiKinerja::create($this->payload(['user_id' => $this->admin->id, 'keterangan' => 'Rahasia pegawai lain']));
        $response = $this->actingAs($this->pegawai)->get('/export-excel/evaluasi-kinerja')->assertOk();
        $path = $response->baseResponse->getFile()->getPathname();
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path));
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        unlink($path);
        $this->assertCount(2, simplexml_load_string($sheet)->sheetData->row);
        $this->assertStringContainsString('Pegawai Satu', $sheet);
        $this->assertStringContainsString('Baik', $sheet);
        $this->assertStringContainsString('Target tercapai.', $sheet);
        $this->assertStringNotContainsString('Rahasia pegawai lain', $sheet);
    }
}
