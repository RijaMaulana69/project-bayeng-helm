<?php

namespace App\Livewire\Admin;

use App\Models\ChatPercakapan;
use App\Models\ChatPesan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KaryawanChat extends Component
{
    public string $search = '';
    public string $filterStatus = 'all'; // all, unread, active, closed
    public ?int $selectedPercakapanId = null;
    public string $replyMessage = '';

    protected $queryString = [
        'selectedPercakapanId' => ['as' => 'id', 'except' => null],
        'filterStatus' => ['as' => 'status', 'except' => 'all'],
    ];

    public function mount(?int $id = null)
    {
        $targetId = $id ?? request()->query('id');
        if ($targetId) {
            $this->selectPercakapan((int) $targetId);
        } else {
            // Pre-select latest conversation for desktop view
            $latest = ChatPercakapan::latest('pesan_terakhir_at')->first();
            if ($latest) {
                $this->selectedPercakapanId = $latest->id_percakapan;
            }
        }
    }

    public function backToList()
    {
        $this->selectedPercakapanId = null;
    }

    public function selectPercakapan(int $id)
    {
        $this->selectedPercakapanId = $id;

        $percakapan = ChatPercakapan::find($id);
        if ($percakapan) {
            $percakapan->markAsReadByKaryawan();
        }

        $this->dispatch('chat-scrolled-to-bottom');
    }

    public function sendReply()
    {
        $text = trim($this->replyMessage);

        if ($text === '' || ! $this->selectedPercakapanId) {
            return;
        }

        $percakapan = ChatPercakapan::find($this->selectedPercakapanId);
        if (! $percakapan) {
            return;
        }

        $user = Auth::guard('web')->user();

        ChatPesan::create([
            'id_percakapan' => $percakapan->id_percakapan,
            'pengirim_tipe' => 'karyawan',
            'id_user' => $user?->id_user,
            'pesan' => $text,
            'id_helm' => $percakapan->id_helm_terkait,
            'dibaca' => false,
        ]);

        $percakapan->update([
            'pesan_terakhir' => $text,
            'pesan_terakhir_at' => now(),
            'unread_pelanggan' => $percakapan->unread_pelanggan + 1,
            'status' => 'aktif',
        ]);

        $this->replyMessage = '';

        $this->dispatch('chat-scrolled-to-bottom');
    }

    public function sendQuickReply(string $text)
    {
        $this->replyMessage = $text;
        $this->sendReply();
    }

    public function toggleStatus(int $id)
    {
        $percakapan = ChatPercakapan::find($id);
        if ($percakapan) {
            $newStatus = $percakapan->status === 'aktif' ? 'selesai' : 'aktif';
            $percakapan->update(['status' => $newStatus]);
        }
    }

    public function deletePercakapan(int $id)
    {
        $percakapan = ChatPercakapan::find($id);
        if ($percakapan) {
            $percakapan->delete();

            if ($this->selectedPercakapanId === $id) {
                $next = ChatPercakapan::latest('pesan_terakhir_at')->first();
                $this->selectedPercakapanId = $next?->id_percakapan;
            }
        }
    }

    public function render()
    {
        $query = ChatPercakapan::query()
            ->with(['helmTerkait', 'pelanggan']);

        if (trim($this->search) !== '') {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('nama_pengirim', 'like', "%{$s}%")
                    ->orWhere('telepon_pengirim', 'like', "%{$s}%")
                    ->orWhere('email_pengirim', 'like', "%{$s}%")
                    ->orWhere('pesan_terakhir', 'like', "%{$s}%");
            });
        }

        if ($this->filterStatus === 'unread') {
            $query->where('unread_karyawan', '>', 0);
        } elseif ($this->filterStatus === 'active') {
            $query->where('status', 'aktif');
        } elseif ($this->filterStatus === 'closed') {
            $query->where('status', 'selesai');
        }

        $conversations = $query->orderByDesc('pesan_terakhir_at')->get();

        $selectedPercakapan = null;
        if ($this->selectedPercakapanId) {
            $selectedPercakapan = ChatPercakapan::with([
                'helmTerkait.merk',
                'helmTerkait.kategori',
                'pelanggan',
                'pesans.user',
                'pesans.helm',
            ])->find($this->selectedPercakapanId);

            if ($selectedPercakapan && $selectedPercakapan->unread_karyawan > 0) {
                $selectedPercakapan->markAsReadByKaryawan();
            }
        }

        $totalUnread = ChatPercakapan::sum('unread_karyawan');
        $totalActive = ChatPercakapan::where('status', 'aktif')->count();

        return view('livewire.admin.karyawan-chat', [
            'conversations' => $conversations,
            'selectedPercakapan' => $selectedPercakapan,
            'totalUnread' => $totalUnread,
            'totalActive' => $totalActive,
        ]);
    }
}
