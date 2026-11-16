<?php

namespace App\Livewire;

use App\Models\ChatPercakapan;
use App\Models\ChatPesan;
use App\Models\Helm;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerChatWidget extends Component
{
    public bool $isOpen = false;
    public string $chatInput = '';
    public ?int $idHelm = null;
    public ?int $percakapanId = null;

    protected $listeners = [
        'open-store-chat' => 'openChat',
        'close-store-chat' => 'closeChat',
    ];

    public function mount(?int $helmId = null)
    {
        if ($helmId) {
            $this->idHelm = $helmId;
        }
    }

    public function openChat($params = null)
    {
        if (is_array($params) && isset($params['helm_id'])) {
            $this->idHelm = (int) $params['helm_id'];
        } elseif (is_numeric($params)) {
            $this->idHelm = (int) $params;
        }

        $this->ensurePercakapanExists();
        $this->isOpen = true;

        if ($this->percakapanId) {
            $percakapan = ChatPercakapan::find($this->percakapanId);
            if ($percakapan) {
                $percakapan->markAsReadByPelanggan();
            }
        }

        $this->dispatch('chat-scrolled-to-bottom');
    }

    public function closeChat()
    {
        $this->isOpen = false;
    }

    protected function ensurePercakapanExists(): ?ChatPercakapan
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        $sessionId = session()->getId();

        $query = ChatPercakapan::query();

        if ($pelanggan) {
            $percakapan = $query->where('id_pelanggan', $pelanggan->id_pelanggan)->latest('id_percakapan')->first();
        } else {
            $percakapan = $query->where('session_id', $sessionId)->latest('id_percakapan')->first();
        }

        if (! $percakapan) {
            $nama = $pelanggan ? $pelanggan->nama : 'Tamu #' . substr(md5($sessionId), 0, 4);
            $email = $pelanggan ? $pelanggan->email : null;
            $telepon = $pelanggan ? $pelanggan->telepon : null;

            $percakapan = ChatPercakapan::create([
                'id_pelanggan' => $pelanggan ? $pelanggan->id_pelanggan : null,
                'session_id' => $sessionId,
                'nama_pengirim' => $nama,
                'email_pengirim' => $email,
                'telepon_pengirim' => $telepon,
                'id_helm_terkait' => $this->idHelm,
                'pesan_terakhir' => 'Halo kak! Ada yang bisa kami bantu seputar stok atau fitting helm?',
                'pesan_terakhir_at' => now(),
                'unread_karyawan' => 0,
                'unread_pelanggan' => 0,
                'status' => 'aktif',
            ]);

            // Create initial welcoming greeting from store staff
            ChatPesan::create([
                'id_percakapan' => $percakapan->id_percakapan,
                'pengirim_tipe' => 'sistem',
                'pesan' => 'Halo kak! Ada yang bisa kami bantu? Mau tanya ketersediaan stok atau fitting ukuran di toko?',
                'id_helm' => $this->idHelm,
                'dibaca' => true,
            ]);
        } else {
            // Update related helm context if provided
            if ($this->idHelm && $percakapan->id_helm_terkait !== $this->idHelm) {
                $percakapan->update(['id_helm_terkait' => $this->idHelm]);
            }
        }

        $this->percakapanId = $percakapan->id_percakapan;

        return $percakapan;
    }

    public function sendMessage(?string $customText = null)
    {
        $text = trim($customText ?? $this->chatInput);

        if ($text === '') {
            return;
        }

        $percakapan = $this->ensurePercakapanExists();

        if (! $percakapan) {
            return;
        }

        ChatPesan::create([
            'id_percakapan' => $percakapan->id_percakapan,
            'pengirim_tipe' => 'pelanggan',
            'pesan' => $text,
            'id_helm' => $this->idHelm,
            'dibaca' => false,
        ]);

        $percakapan->update([
            'pesan_terakhir' => $text,
            'pesan_terakhir_at' => now(),
            'unread_karyawan' => $percakapan->unread_karyawan + 1,
            'status' => 'aktif',
        ]);

        if (! $customText) {
            $this->chatInput = '';
        }

        $this->dispatch('chat-scrolled-to-bottom');
    }

    public function render()
    {
        $messages = collect();
        $helmContext = null;
        $unreadCount = 0;

        $pelanggan = Auth::guard('pelanggan')->user();
        $sessionId = session()->getId();

        $percakapan = ChatPercakapan::query()
            ->when($pelanggan, fn ($q) => $q->where('id_pelanggan', $pelanggan->id_pelanggan))
            ->when(! $pelanggan, fn ($q) => $q->where('session_id', $sessionId))
            ->latest('id_percakapan')
            ->first();

        if ($percakapan) {
            $this->percakapanId = $percakapan->id_percakapan;
            $messages = $percakapan->pesans()->with('helm')->get();
            $unreadCount = $percakapan->unread_pelanggan;

            if ($this->isOpen && $unreadCount > 0) {
                $percakapan->markAsReadByPelanggan();
                $unreadCount = 0;
            }

            if ($this->idHelm) {
                $helmContext = Helm::find($this->idHelm);
            } elseif ($percakapan->id_helm_terkait) {
                $helmContext = $percakapan->helmTerkait;
            }
        } elseif ($this->idHelm) {
            $helmContext = Helm::find($this->idHelm);
        }

        return view('livewire.customer-chat-widget', [
            'messages' => $messages,
            'helmContext' => $helmContext,
            'unreadCount' => $unreadCount,
        ]);
    }
}
