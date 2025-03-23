<?php

namespace App\Services;

use App\Models\Module;
use App\Models\ModuleActivationRequest;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\ModuleActivationProcessed;
use App\Notifications\ModuleActivationRequested;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Mengirim notifikasi permintaan aktivasi modul ke superadmin
     *
     * @param ModuleActivationRequest $request
     * @return void
     */
    public function sendModuleActivationRequestedNotification(ModuleActivationRequest $request): void
    {
        try {
            // Dapatkan semua superadmin
            $superadmins = User::role('Superadmin')->get();

            if ($superadmins->isEmpty()) {
                Log::warning('Tidak ada superadmin untuk menerima notifikasi permintaan aktivasi modul.');
                return;
            }

            // Kirim notifikasi ke semua superadmin
            Notification::send($superadmins, new ModuleActivationRequested($request));

            Log::info("Notifikasi permintaan aktivasi modul berhasil dikirim ke {$superadmins->count()} superadmin.");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi permintaan aktivasi modul: {$e->getMessage()}");
        }
    }

    /**
     * Mengirim notifikasi proses aktivasi modul ke admin tenant
     *
     * @param ModuleActivationRequest $request
     * @return void
     */
    public function sendModuleActivationProcessedNotification(ModuleActivationRequest $request): void
    {
        try {
            // Dapatkan semua admin tenant
            $tenantAdmins = User::where('tenant_id', $request->tenant_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Admin');
                })
                ->get();

            // Tambahkan juga user yang membuat request
            $requester = User::find($request->requested_by);

            // Gabungkan semua penerima dan hapus duplikat
            $recipients = collect([$requester])
                ->merge($tenantAdmins)
                ->unique('id')
                ->filter();

            if ($recipients->isEmpty()) {
                Log::warning('Tidak ada penerima untuk notifikasi proses aktivasi modul.');
                return;
            }

            // Kirim notifikasi ke semua penerima
            Notification::send($recipients, new ModuleActivationProcessed($request));

            Log::info("Notifikasi proses aktivasi modul berhasil dikirim ke {$recipients->count()} penerima.");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi proses aktivasi modul: {$e->getMessage()}");
        }
    }

    /**
     * Mengirim notifikasi ke pengguna spesifik
     *
     * @param User|Collection $users
     * @param mixed $notification
     * @return void
     */
    public function sendNotificationToUsers($users, $notification): void
    {
        try {
            Notification::send($users, $notification);

            $count = $users instanceof Collection ? $users->count() : 1;
            Log::info("Notifikasi berhasil dikirim ke {$count} pengguna.");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi: {$e->getMessage()}");
        }
    }
}
