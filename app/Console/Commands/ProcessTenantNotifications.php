<?php

namespace App\Console\Commands;

use App\Models\SnarsNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessTenantNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:process
                            {--limit=50 : Jumlah maksimal notifikasi yang diproses}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memproses dan mengirim notifikasi tenant yang tertunda';

    /**
     * Layanan notifikasi
     * 
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * Create a new command instance.
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');

        $this->info("Memulai pemrosesan notifikasi tenant (limit: {$limit})");

        try {
            // Dapatkan notifikasi yang siap dikirim
            $notifications = SnarsNotification::readyToSend()->limit($limit)->get();

            $count = $notifications->count();
            $this->info("Ditemukan {$count} notifikasi yang siap diproses");

            $processed = 0;

            // Proses notifikasi
            foreach ($notifications as $notification) {
                $this->processNotification($notification);
                $processed++;

                // Tampilkan progress
                if ($processed % 10 === 0 || $processed === $count) {
                    $this->info("Diproses {$processed} dari {$count} notifikasi");
                }
            }

            $this->info("Pemrosesan notifikasi selesai: {$processed} berhasil diproses");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error saat memproses notifikasi: {$e->getMessage()}");
            Log::error("Error saat memproses notifikasi: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Memproses notifikasi individual
     * 
     * @param SnarsNotification $notification
     * @return void
     */
    protected function processNotification(SnarsNotification $notification)
    {
        try {
            $this->line("Memproses notifikasi #{$notification->id}: {$notification->title}");

            // Ambil penerima notifikasi
            $recipients = $notification->recipients()->with('user')->get()->pluck('user');

            if ($recipients->isEmpty()) {
                $this->warn("- Tidak ada penerima untuk notifikasi #{$notification->id}");
                $notification->status = 'failed';
                $notification->save();
                return;
            }

            // Kirim notifikasi ke penerima
            // Implementasi ini dapat disesuaikan dengan jenis notifikasi
            // $this->notificationService->sendNotificationToUsers($recipients, ...);

            // Update status
            $notification->status = 'sent';
            $notification->save();

            $this->info("- Notifikasi #{$notification->id} berhasil diproses (penerima: {$recipients->count()})");
        } catch (\Exception $e) {
            $this->error("- Gagal memproses notifikasi #{$notification->id}: {$e->getMessage()}");
            Log::error("Gagal memproses notifikasi #{$notification->id}: {$e->getMessage()}", [
                'notification_id' => $notification->id,
                'exception' => $e,
            ]);

            $notification->status = 'failed';
            $notification->save();
        }
    }
}
