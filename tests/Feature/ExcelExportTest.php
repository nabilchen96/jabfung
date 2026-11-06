<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use ZipArchive;

class ExcelExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
    }

    private function sheet($response): string
    {
        $response->assertOk()->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $file = $response->baseResponse->getFile()->getPathname();
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($file));
        foreach (['[Content_Types].xml', '_rels/.rels', 'xl/workbook.xml', 'xl/_rels/workbook.xml.rels', 'xl/worksheets/sheet1.xml'] as $entry) {
            $this->assertNotFalse(simplexml_load_string($zip->getFromName($entry)));
        }
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        unlink($file);

        return $sheet;
    }

    private function records(User $user, string $marker): void
    {
        foreach ([
            'detail_users' => ['nip' => '001234567890123456', 'nama' => $marker],
            'jabatan_fungsionals' => ['jabatan_fungsional' => $marker, 'nomor_sk' => '001', 'terhitung_mulai_tanggal' => '2026-01-01'],
            'kepangkatans' => ['pangkat' => $marker],
            'angka_kredits' => ['no_sk' => $marker, 'tgl_sk' => '2026-01-01', 'tgl_mulai' => '2026-01-01', 'tgl_selesai' => '2026-12-31', 'total_kredit' => 12.5],
            'pendidikans' => ['jenjang' => 'S1', 'nama_institusi' => $marker],
            'diklats' => ['nama_diklat' => $marker],
        ] as $table => $data) {
            DB::table($table)->insert(['user_id' => $user->id] + $data);
        }
    }

    public function test_every_backend_page_has_an_export_link(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);
        $this->records($user, 'Profile');
        $this->actingAs($user);
        foreach (['dashboard', 'user', 'jabatan-fungsional', 'kepangkatan', 'angka-kredit', 'pendidikan', 'diklat', 'detail-user'] as $page) {
            $params = ['page' => $page];
            $url = '/'.$page;
            if ($page === 'detail-user') {
                $params['id'] = $user->id;
                $url .= '?id='.$user->id;
            }
            $this->get($url)->assertOk()->assertSee(route('export.excel', $params), false)->assertSeeText('Export');
        }
    }

    public function test_guests_cannot_download_exports(): void
    {
        $this->get('/export-excel/user')->assertRedirect('/login');
    }

    public function test_admin_can_export_every_page_as_a_valid_workbook(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $other = User::factory()->create(['name' => 'Other employee']);
        $this->records($other, 'Exported record');
        $this->actingAs($admin);

        foreach (['user', 'jabatan-fungsional', 'kepangkatan', 'angka-kredit', 'pendidikan', 'diklat', 'dashboard', 'detail-user?id='.$other->id] as $page) {
            $xml = $this->sheet($this->get('/export-excel/'.$page));
            if ($page !== 'dashboard') {
                $this->assertStringContainsString('Other employee', $xml);
            }
            $this->assertStringNotContainsString($other->password, $xml);
            $this->assertStringNotContainsString('remember_token', $xml);
        }
    }

    public function test_employee_exports_only_own_records_and_cannot_select_another_profile(): void
    {
        $user = User::factory()->create(['name' => 'Own employee', 'role' => 'Pegawai']);
        $other = User::factory()->create(['name' => 'Other employee']);
        $this->records($user, 'Own record');
        $this->records($other, 'Private record');
        $this->actingAs($user);

        foreach (['user', 'jabatan-fungsional', 'kepangkatan', 'angka-kredit', 'pendidikan', 'diklat', 'detail-user?id='.$user->id] as $page) {
            $xml = $this->sheet($this->get('/export-excel/'.$page));
            $this->assertStringContainsString('Own employee', $xml);
            $this->assertStringNotContainsString('Other employee', $xml);
            $this->assertStringNotContainsString('Private record', $xml);
        }
        $this->get('/export-excel/detail-user?id='.$other->id)->assertForbidden();
        $xml = simplexml_load_string($this->sheet($this->get('/export-excel/dashboard')));
        foreach ($xml->sheetData->row as $index => $row) {
            if (isset($row->c[1]->v)) {
                $this->assertSame('1', (string) $row->c[1]->v);
            }
        }
    }

    public function test_empty_exports_include_headers_and_unknown_pages_are_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        $xml = simplexml_load_string($this->sheet($this->get('/export-excel/pendidikan')));
        $this->assertCount(1, $xml->sheetData->row);
        $this->get('/export-excel/unknown')->assertNotFound();
    }

    public function test_identifiers_and_formula_like_text_remain_literal_text(): void
    {
        $user = User::factory()->create(['name' => '=HYPERLINK("example")']);
        $this->records($user, 'Nama & <teks>');
        $this->actingAs($user);
        $xml = $this->sheet($this->get('/export-excel/detail-user?id='.$user->id));
        $this->assertStringContainsString('001234567890123456', $xml);
        $this->assertStringContainsString('Nama &amp; &lt;teks&gt;', $xml);
        $this->assertStringContainsString('=HYPERLINK', $xml);
        $this->assertStringNotContainsString('<f>', $xml);
        $this->assertStringContainsString('t="inlineStr"', $xml);
    }
}
