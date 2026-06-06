<?php

namespace Tests\Feature;

use App\Filament\Tenant\Pages\EditProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class TenantProfileTest extends TestCase
{
    use RefreshDatabase;

    // ── Akses halaman ─────────────────────────────────────────────────────────

    /** Halaman Profil Saya dapat diakses oleh anak kos yang login. */
    public function test_edit_profile_page_renders_for_authenticated_tenant(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($tenant)
            ->get('/tenant/edit-profile')
            ->assertOk()
            ->assertSee('Profil Saya');
    }

    /** Tamu diredirect ke halaman login tenant. */
    public function test_guest_is_redirected_from_profile_page(): void
    {
        $this->get('/tenant/edit-profile')->assertRedirect();
    }

    /** Juragan tidak dapat mengakses panel tenant (403 Forbidden). */
    public function test_juragan_cannot_access_tenant_profile_page(): void
    {
        $juragan = User::factory()->juragan()->create();

        $this->actingAs($juragan)
            ->get('/tenant/edit-profile')
            ->assertStatus(403);
    }

    // ── Simpan Profil (saveProfile) ───────────────────────────────────────────

    /** saveProfile menyimpan nama dan nomor HP yang baru. */
    public function test_save_profile_updates_name_and_phone(): void
    {
        $tenant = User::factory()->create([
            'role'         => 'tenant',
            'name'         => 'Nama Lama',
            'phone_number' => '+6281111111111',
        ]);

        Livewire::actingAs($tenant)
            ->test(EditProfile::class)
            ->set('profileData.name', 'Nama Baru')
            ->set('profileData.phone_number', '+6289999999999')
            ->call('saveProfile')
            ->assertHasNoErrors();

        $tenant->refresh();
        $this->assertSame('Nama Baru', $tenant->name);
        $this->assertSame('+6289999999999', $tenant->phone_number);
    }

    /** Nama wajib diisi — validasi error jika dikosongkan. */
    public function test_save_profile_requires_name(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant', 'name' => 'Test']);

        Livewire::actingAs($tenant)
            ->test(EditProfile::class)
            ->set('profileData.name', '')
            ->call('saveProfile')
            ->assertHasErrors('profileData.name');
    }

    /** Nomor HP boleh kosong (nullable). */
    public function test_save_profile_allows_empty_phone(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant', 'phone_number' => '+628123456789']);

        Livewire::actingAs($tenant)
            ->test(EditProfile::class)
            ->set('profileData.name', $tenant->name)
            ->set('profileData.phone_number', '')
            ->call('saveProfile')
            ->assertHasNoErrors();

        $this->assertNull($tenant->fresh()->phone_number);
    }

    // ── Ganti Password (savePassword) ─────────────────────────────────────────

    /** savePassword berhasil mengubah password dengan kredensial yang benar. */
    public function test_save_password_updates_password_when_correct(): void
    {
        $tenant = User::factory()->create([
            'role'     => 'tenant',
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($tenant)
            ->test(EditProfile::class)
            ->set('passwordData.current_password', 'password')
            ->set('passwordData.password', 'new-secure-pass')
            ->set('passwordData.password_confirmation', 'new-secure-pass')
            ->call('savePassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('new-secure-pass', $tenant->fresh()->password));
    }

    /** savePassword gagal jika current_password salah. */
    public function test_save_password_fails_with_wrong_current_password(): void
    {
        $tenant = User::factory()->create([
            'role'     => 'tenant',
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($tenant)
            ->test(EditProfile::class)
            ->set('passwordData.current_password', 'salah-password')
            ->set('passwordData.password', 'new-secure-pass')
            ->set('passwordData.password_confirmation', 'new-secure-pass')
            ->call('savePassword')
            ->assertHasErrors('passwordData.current_password');
    }

    /** savePassword gagal jika konfirmasi password tidak cocok. */
    public function test_save_password_fails_when_confirmation_mismatch(): void
    {
        $tenant = User::factory()->create([
            'role'     => 'tenant',
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($tenant)
            ->test(EditProfile::class)
            ->set('passwordData.current_password', 'password')
            ->set('passwordData.password', 'new-secure-pass')
            ->set('passwordData.password_confirmation', 'beda-password')
            ->call('savePassword')
            ->assertHasErrors('passwordData.password_confirmation');
    }

    /** Password baru minimal 8 karakter. */
    public function test_save_password_fails_when_too_short(): void
    {
        $tenant = User::factory()->create([
            'role'     => 'tenant',
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($tenant)
            ->test(EditProfile::class)
            ->set('passwordData.current_password', 'password')
            ->set('passwordData.password', 'short')
            ->set('passwordData.password_confirmation', 'short')
            ->call('savePassword')
            ->assertHasErrors('passwordData.password');
    }

    /** Password baru harus berbeda dari password lama. */
    public function test_save_password_fails_when_same_as_current(): void
    {
        $tenant = User::factory()->create([
            'role'     => 'tenant',
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($tenant)
            ->test(EditProfile::class)
            ->set('passwordData.current_password', 'password')
            ->set('passwordData.password', 'password')
            ->set('passwordData.password_confirmation', 'password')
            ->call('savePassword')
            ->assertHasErrors('passwordData.password');
    }
}
