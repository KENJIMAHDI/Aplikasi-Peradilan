<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ECourtPerkara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerdataCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_umum_only_displays_gugatan_and_permohonan(): void
    {
        ECourtPerkara::create([
            'nomor_register' => '001/Pdt.G/2026/PN.Smg',
            'jenis_perdata' => 'Gugatan',
            'penggugat' => 'P1',
            'tergugat' => 'T1',
            'nominal_panjar' => 0,
            'status_pembayaran' => 'Lunas'
        ]);

        ECourtPerkara::create([
            'nomor_register' => '002/Pdt.P/2026/PN.Smg',
            'jenis_perdata' => 'Permohonan',
            'penggugat' => 'P2',
            'tergugat' => 'T2',
            'nominal_panjar' => 0,
            'status_pembayaran' => 'Lunas'
        ]);

        ECourtPerkara::create([
            'nomor_register' => '003/Pdt.Sus/2026/PN.Smg',
            'jenis_perdata' => 'PHI',
            'penggugat' => 'P3',
            'tergugat' => 'T3',
            'nominal_panjar' => 0,
            'status_pembayaran' => 'Lunas'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('perdata.umum'));

        $response->assertStatus(200);
        $response->assertViewHas('perkara', function ($perkara) {
            $this->assertTrue($perkara->contains('nomor_register', '001/Pdt.G/2026/PN.Smg'));
            $this->assertTrue($perkara->contains('nomor_register', '002/Pdt.P/2026/PN.Smg'));
            $this->assertFalse($perkara->contains('nomor_register', '003/Pdt.Sus/2026/PN.Smg'));
            return true;
        });
    }

    public function test_khusus_only_displays_phi_and_niaga(): void
    {
        ECourtPerkara::create([
            'nomor_register' => '001/Pdt.G/2026/PN.Smg',
            'jenis_perdata' => 'Gugatan',
            'penggugat' => 'P1',
            'tergugat' => 'T1',
            'nominal_panjar' => 0,
            'status_pembayaran' => 'Lunas'
        ]);

        ECourtPerkara::create([
            'nomor_register' => '002/Pdt.Sus/2026/PN.Smg',
            'jenis_perdata' => 'PHI',
            'penggugat' => 'P2',
            'tergugat' => 'T2',
            'nominal_panjar' => 100000,
            'status_pembayaran' => 'Lunas'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('perdata.khusus'));

        $response->assertStatus(200);
        $response->assertViewHas('perkara', function ($perkara) {
            $this->assertTrue($perkara->contains('nomor_register', '002/Pdt.Sus/2026/PN.Smg'));
            $this->assertFalse($perkara->contains('nomor_register', '001/Pdt.G/2026/PN.Smg'));
            return true;
        });
    }

    public function test_can_add_perkara_perdata_umum(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('perdata.store'), [
                'nomor_perkara' => '004/Pdt.G/2026/PN.Smg',
                'tanggal_daftar' => '2026-08-22',
                'penggugat' => 'Syarifudin',
                'tergugat' => 'PT Maju Jaya',
                'status' => 'Diajukan',
                'jenis_perdata' => 'Gugatan'
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('e_court_perkaras', [
            'nomor_register' => '004/Pdt.G/2026/PN.Smg',
            'penggugat' => 'Syarifudin',
            'tergugat' => 'PT Maju Jaya',
            'status' => 'Diajukan',
            'jenis_perdata' => 'Gugatan',
            'nominal_panjar' => 0,
            'status_pembayaran' => 'Belum Dibayar'
        ]);
    }

    public function test_can_add_perkara_perdata_khusus_without_optional_fields(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('perdata.store'), [
                'nomor_perkara' => '005/Pdt.Sus-PHI/2026/PN.Smg',
                'jenis_perdata' => 'PHI',
                'penggugat' => 'Karyawan A',
                'tergugat' => 'PT Makmur Jaya',
                'nominal_panjar' => 500000,
                'status_pembayaran' => 'Lunas'
            ]);

        $response->assertStatus(302); // Redirect back
        $this->assertDatabaseHas('e_court_perkaras', [
            'nomor_register' => '005/Pdt.Sus-PHI/2026/PN.Smg',
            'jenis_perdata' => 'PHI',
            'penggugat' => 'Karyawan A',
            'tergugat' => 'PT Makmur Jaya',
            'nominal_panjar' => 500000,
            'status_pembayaran' => 'Lunas',
            'status' => 'Diajukan', // Defaulted
            'tanggal_daftar' => now()->format('Y-m-d') . ' 00:00:00' // Defaulted
        ]);
    }

    public function test_can_update_perkara_perdata(): void
    {
        $perkara = ECourtPerkara::create([
            'nomor_register' => '006/Pdt.G/2026/PN.Smg',
            'tanggal_daftar' => '2026-08-20',
            'penggugat' => 'Lutfi',
            'tergugat' => 'PT Makmur',
            'status' => 'Diajukan',
            'jenis_perdata' => 'Gugatan',
            'nominal_panjar' => 0,
            'status_pembayaran' => 'Lunas'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->putJson("/perdata-umum/{$perkara->id}", [
                'nomor_perkara' => '006/Pdt.G/2026/PN.Smg',
                'tanggal_daftar' => '2026-08-21',
                'penggugat' => 'Lutfi Updated',
                'tergugat' => 'PT Makmur',
                'status' => 'Sedang Di Proses',
                'jenis_perdata' => 'Gugatan'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('e_court_perkaras', [
            'id' => $perkara->id,
            'penggugat' => 'Lutfi Updated',
            'status' => 'Sedang Di Proses',
            'tanggal_daftar' => '2026-08-21 00:00:00'
        ]);
    }

    public function test_can_delete_perkara_perdata(): void
    {
        $perkara = ECourtPerkara::create([
            'nomor_register' => '007/Pdt.G/2026/PN.Smg',
            'tanggal_daftar' => '2026-08-20',
            'penggugat' => 'Hadi',
            'tergugat' => 'Rian',
            'status' => 'Diajukan',
            'jenis_perdata' => 'Gugatan',
            'nominal_panjar' => 0,
            'status_pembayaran' => 'Lunas'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/perdata-umum/{$perkara->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('e_court_perkaras', [
            'id' => $perkara->id
        ]);
    }
}
