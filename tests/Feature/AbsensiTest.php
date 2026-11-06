<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use ZipArchive;

class AbsensiTest extends TestCase
{
    private User $admin;
    private User $pegawai;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->pegawai = User::factory()->create(['role' => 'Pegawai', 'name' => 'Pegawai Satu']);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'user_id' => $this->pegawai->id,
            'bulan' => '2026-11',
            'total_terlambat' => 2,
            'total_cuti_izin_dinas' => 3,
            'total_pulang_cepat' => 1,
            'total_kehadiran' => 20,
        ], $overrides);
    }

    public function test_admin_can_create_read_update_and_delete_monthly_attendance(): void
    {
        $this->actingAs($this->admin)->post(route('absensi.store'), $this->payload())
            ->assertSessionHasNoErrors()->assertRedirect(route('absensi.index', ['bulan' => '2026-11']));
        $record = Absensi::firstOrFail();
        $this->assertDatabaseHas('absensis', $this->payload());
        $this->get('/absensi?bulan=2026-11')->assertOk()->assertSee('Pegawai Satu')->assertSee('data-id="'.$record->id.'"', false);
        $this->put(route('absensi.update', $record), $this->payload(['total_terlambat' => 4]))
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(4, $record->fresh()->total_terlambat);
        $this->delete(route('absensi.destroy', $record))->assertRedirect(route('absensi.index', ['bulan' => '2026-11']));
        $this->assertDatabaseMissing('absensis', ['id' => $record->id]);
    }

    public function test_month_filter_and_employee_scope_are_applied(): void
    {
        Absensi::create($this->payload());
        Absensi::create($this->payload(['bulan' => '2026-10']));
        Absensi::create($this->payload(['user_id' => $this->admin->id]));
        $response = $this->actingAs($this->admin)->get('/absensi?bulan=2026-11')->assertOk();
        $this->assertSame(2, $response->viewData('absensis')->total());
        $response = $this->actingAs($this->pegawai)->get('/absensi?bulan=2026-11&user_id='.$this->admin->id)
            ->assertOk()->assertDontSee('id="absensiModal"', false)->assertDontSee('data-id=', false);
        $this->assertSame(1, $response->viewData('absensis')->total());
        $this->assertSame($this->pegawai->id, $response->viewData('absensis')->first()->user_id);
    }

    public function test_only_admin_can_write_even_when_requests_are_sent_directly(): void
    {
        $record = Absensi::create($this->payload());
        $this->actingAs($this->pegawai)->post(route('absensi.store'), $this->payload(['bulan' => '2026-10']))->assertForbidden();
        $this->put(route('absensi.update', $record), $this->payload(['total_kehadiran' => 0]))->assertForbidden();
        $this->delete(route('absensi.destroy', $record))->assertForbidden();
        $this->assertSame(20, $record->fresh()->total_kehadiran);
        $this->assertDatabaseCount('absensis', 1);
    }

    public function test_duplicate_employee_month_is_rejected_but_other_months_are_allowed(): void
    {
        $record = Absensi::create($this->payload());
        $other = Absensi::create($this->payload(['bulan' => '2026-10']));
        $this->actingAs($this->admin)->postJson(route('absensi.store'), $this->payload())
            ->assertUnprocessable()->assertJsonValidationErrors('bulan');
        $this->putJson(route('absensi.update', $other), $this->payload())
            ->assertUnprocessable()->assertJsonValidationErrors('bulan');
        $this->put(route('absensi.update', $record), $this->payload())->assertSessionHasNoErrors();
        $this->post(route('absensi.store'), $this->payload(['bulan' => '2026-12']))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('absensis', 3);
    }

    public function test_counts_are_required_nonnegative_integers_within_calendar_month(): void
    {
        $this->actingAs($this->admin);
        foreach ([
            ['bulan' => '2026-02', 'total_kehadiran' => 29],
            ['total_terlambat' => -1],
            ['total_cuti_izin_dinas' => 1.5],
            ['total_pulang_cepat' => null],
            ['bulan' => '2026-13'],
            ['bulan' => ['2026-11']],
            ['user_id' => 999999],
            ['user_id' => ['invalid']],
        ] as $invalid) {
            $this->postJson(route('absensi.store'), $this->payload($invalid))->assertUnprocessable();
        }
        $this->post(route('absensi.store'), $this->payload(['bulan' => '2028-02', 'total_kehadiran' => 29]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('absensis', 1);
        $this->getJson('/absensi?bulan=invalid')->assertUnprocessable();
    }

    public function test_guests_are_redirected_and_unknown_records_return_not_found(): void
    {
        $this->get('/absensi')->assertRedirect('/login');
        $this->post(route('absensi.store'), $this->payload())->assertRedirect('/login');
        $this->actingAs($this->admin)->put('/absensi/99999', $this->payload())->assertNotFound();
        $this->delete('/absensi/99999')->assertNotFound();
    }

    public function test_monthly_export_contains_only_visible_records_and_requested_month(): void
    {
        Absensi::create($this->payload());
        Absensi::create($this->payload(['user_id' => $this->admin->id]));
        Absensi::create($this->payload(['bulan' => '2026-10']));
        $response = $this->actingAs($this->pegawai)->get('/export-excel/absensi?bulan=2026-11')->assertOk();
        $path = $response->baseResponse->getFile()->getPathname();
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path));
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        unlink($path);
        $xml = simplexml_load_string($sheet);
        $this->assertCount(2, $xml->sheetData->row);
        $this->assertStringContainsString('Pegawai Satu', $sheet);
        $this->assertStringNotContainsString('2026-10', $sheet);
        $this->assertStringNotContainsString($this->admin->name, $sheet);
        $this->getJson('/export-excel/absensi')->assertUnprocessable();
    }

    public function test_navigation_places_attendance_after_qualifications(): void
    {
        $content = $this->actingAs($this->admin)->get('/absensi')->assertOk()->getContent();
        $this->assertGreaterThan(strpos($content, '>Kualifikasi<'), strpos($content, '>Absensi<'));
        $this->assertStringContainsString('id="absensiForm"', $content);
    }
}
