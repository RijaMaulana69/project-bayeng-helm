<?php

namespace Tests\Feature;

use App\Livewire\Admin\KaryawanChat;
use App\Livewire\CustomerChatWidget;
use App\Models\ChatPercakapan;
use App\Models\ChatPesan;
use App\Models\Helm;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ChatSystemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_guest_cannot_access_employee_chat_panel(): void
    {
        $response = $this->get(route('admin.chat.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_karyawan_can_access_chat_panel(): void
    {
        $karyawan = User::firstOrCreate(
            ['username' => 'karyawan_chat_tester'],
            [
                'nama_lengkap' => 'Kasir Chat Tester',
                'password' => Hash::make('Password123!'),
                'id_role' => 2, // Karyawan
            ]
        );

        $response = $this->actingAs($karyawan, 'web')->get(route('admin.chat.index'));
        $response->assertStatus(200);
        $response->assertSee('Chat Pelanggan');
    }

    public function test_customer_can_send_chat_message(): void
    {
        $helm = Helm::first();

        Livewire::test(CustomerChatWidget::class)
            ->call('openChat', $helm ? ['helm_id' => $helm->id_helm] : null)
            ->assertSet('isOpen', true)
            ->set('chatInput', 'Halo kak, apakah barang ini ready?')
            ->call('sendMessage')
            ->assertSet('chatInput', '');

        $percakapan = ChatPercakapan::latest('id_percakapan')->first();
        $this->assertNotNull($percakapan);
        $this->assertEquals('Halo kak, apakah barang ini ready?', $percakapan->pesan_terakhir);
        $this->assertGreaterThanOrEqual(1, $percakapan->unread_karyawan);

        $pesan = ChatPesan::where('id_percakapan', $percakapan->id_percakapan)
            ->where('pengirim_tipe', 'pelanggan')
            ->latest('id_pesan')
            ->first();
        $this->assertNotNull($pesan);
        $this->assertEquals('Halo kak, apakah barang ini ready?', $pesan->pesan);
    }

    public function test_karyawan_can_reply_to_customer_chat(): void
    {
        $karyawan = User::firstOrCreate(
            ['username' => 'karyawan_chat_tester'],
            [
                'nama_lengkap' => 'Kasir Chat Tester',
                'password' => Hash::make('Password123!'),
                'id_role' => 2,
            ]
        );

        // Create sample conversation with incoming customer message
        $percakapan = ChatPercakapan::create([
            'nama_pengirim' => 'Budi Santoso',
            'email_pengirim' => 'budi@example.com',
            'telepon_pengirim' => '081234567890',
            'pesan_terakhir' => 'Bisa fitting di gerai?',
            'pesan_terakhir_at' => now(),
            'unread_karyawan' => 1,
            'unread_pelanggan' => 0,
            'status' => 'aktif',
        ]);

        ChatPesan::create([
            'id_percakapan' => $percakapan->id_percakapan,
            'pengirim_tipe' => 'pelanggan',
            'pesan' => 'Bisa fitting di gerai?',
            'dibaca' => false,
        ]);

        $this->actingAs($karyawan, 'web');

        Livewire::test(KaryawanChat::class, ['id' => $percakapan->id_percakapan])
            ->assertSee('Budi Santoso')
            ->assertSee('Bisa fitting di gerai?')
            ->set('replyMessage', 'Bisa banget kak! Silakan mampir ke gerai kami di Kuningan.')
            ->call('sendReply')
            ->assertSet('replyMessage', '');

        // Check reply stored
        $reply = ChatPesan::where('id_percakapan', $percakapan->id_percakapan)
            ->where('pengirim_tipe', 'karyawan')
            ->first();
        $this->assertNotNull($reply);
        $this->assertEquals('Bisa banget kak! Silakan mampir ke gerai kami di Kuningan.', $reply->pesan);
        $this->assertEquals($karyawan->id_user, $reply->id_user);

        // Conversation marked as read for employee
        $percakapan->refresh();
        $this->assertEquals(0, $percakapan->unread_karyawan);
        $this->assertEquals(1, $percakapan->unread_pelanggan);
        $this->assertEquals('Bisa banget kak! Silakan mampir ke gerai kami di Kuningan.', $percakapan->pesan_terakhir);

        // Toggle status
        Livewire::test(KaryawanChat::class)
            ->call('toggleStatus', $percakapan->id_percakapan);

        $percakapan->refresh();
        $this->assertEquals('selesai', $percakapan->status);
    }
}
