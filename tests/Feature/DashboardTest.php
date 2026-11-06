<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->travelTo(now()->setDate(2026, 11, 6));
    }

    private function records(User $user, string $level): void
    {
        DB::table('pendidikans')->insert([
            ['user_id' => $user->id, 'jenjang' => $level, 'nama_institusi' => 'Universitas A'],
            ['user_id' => $user->id, 'jenjang' => $level, 'nama_institusi' => 'Universitas B'],
        ]);
        foreach ([2021, 2022, 2026, null] as $year) {
            DB::table('diklats')->insert(['user_id' => $user->id, 'nama_diklat' => 'Pelatihan', 'tahun' => $year]);
        }
        foreach ([2021 => 100, 2022 => 10, 2026 => 20] as $year => $credit) {
            DB::table('angka_kredits')->insert([
                'user_id' => $user->id, 'no_sk' => 'SK-'.$year, 'tgl_sk' => $year.'-01-01',
                'tgl_mulai' => $year.'-01-01', 'tgl_selesai' => $year.'-12-31', 'total_kredit' => $credit,
            ]);
        }
    }

    public function test_dashboard_requires_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_empty_dashboard_has_zero_counts_and_no_export_button(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk()->assertSeeText('Belum ada riwayat pendidikan.')
            ->assertSeeText('Belum ada diklat dengan tahun pada periode ini.')
            ->assertSeeText('Belum ada angka kredit pada periode ini.')
            ->assertDontSee('/export-excel/dashboard', false)
            ->assertViewHas('totalCredits', 0.0)
            ->assertViewHas('charts', fn ($charts) => $charts['training'] === [0, 0, 0, 0, 0] && $charts['credits'] === [0.0, 0.0, 0.0, 0.0, 0.0]);
        $this->assertSame([1, 0, 0, 0, 0, 0], array_column($response->viewData('cards'), 'count'));
    }

    public function test_admin_sees_aggregated_records_and_five_year_series(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $other = User::factory()->create();
        $this->records($admin, 'S1');
        $this->records($other, 'S2');
        $response = $this->actingAs($admin)->get('/dashboard')->assertOk()
            ->assertViewHas('admin', true)->assertViewHas('totalCredits', 260.0);
        $this->assertSame([2, 0, 0, 4, 8, 6], array_column($response->viewData('cards'), 'count'));
        $charts = $response->viewData('charts');
        $this->assertSame([2022, 2023, 2024, 2025, 2026], $charts['years']);
        $this->assertSame([2, 0, 0, 0, 2], $charts['training']);
        $this->assertSame([20.0, 0.0, 0.0, 0.0, 40.0], $charts['credits']);
        $this->assertSame(['S1', 'S2'], $charts['education']['labels']->all());
        $this->assertSame([2, 2], $charts['education']['values']->all());
        $response->assertSee('id="educationChart"', false)->assertSee('id="trainingChart"', false)->assertSee('id="creditChart"', false);
    }

    public function test_employee_cannot_include_other_users_in_statistics(): void
    {
        $user = User::factory()->create(['role' => 'Pegawai']);
        $other = User::factory()->create();
        $this->records($user, 'S1');
        $this->records($other, 'S3');
        $response = $this->actingAs($user)->get('/dashboard?user_id='.$other->id)
            ->assertOk()->assertViewHas('admin', false)->assertViewHas('totalCredits', 130.0);
        $this->assertSame([1, 0, 0, 2, 4, 3], array_column($response->viewData('cards'), 'count'));
        $charts = $response->viewData('charts');
        $this->assertSame(['S1'], $charts['education']['labels']->all());
        $this->assertSame([1, 0, 0, 0, 1], $charts['training']);
        $this->assertSame([10.0, 0.0, 0.0, 0.0, 20.0], $charts['credits']);
    }
}
