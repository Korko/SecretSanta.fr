<?php

namespace App\Console\Commands;

use App\Enums\EmailStatus;
use App\Models\Email\EmailDelivery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EmailDeliveryStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:stats 
                            {--draw= : Show stats for specific draw UUID}
                            {--period=7 : Number of days to show stats for}
                            {--format=table : Output format (table|json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show email delivery statistics';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $drawUuid = $this->option('draw');
        $period = (int) $this->option('period');
        $format = $this->option('format');

        $query = EmailDelivery::query()
            ->where('created_at', '>=', now()->subDays($period));

        if ($drawUuid) {
            $query->whereHas('draw', function ($q) use ($drawUuid) {
                $q->where('uuid', $drawUuid);
            });
            $this->info("Statistics for draw: {$drawUuid}");
        } else {
            $this->info("Global email delivery statistics");
        }

        $this->info("Period: Last {$period} days");
        $this->newLine();

        // Statistiques par statut
        $statusStats = $query->clone()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $total = array_sum($statusStats);

        if ($format === 'json') {
            $this->outputJson($statusStats, $period, $drawUuid);
        } else {
            $this->outputTable($statusStats, $total, $period);
        }

        // Statistiques détaillées
        if ($total > 0) {
            $this->showDetailedStats($query, $total);
        } else {
            $this->info('No email deliveries found for the specified period.');
        }

        return 0;
    }

    private function outputTable(array $statusStats, int $total, int $period): void
    {
        $tableData = [];
        
        foreach (EmailStatus::all() as $status) {
            $count = $statusStats[$status->value] ?? 0;
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            
            $tableData[] = [
                'Status' => $status->getDescription(),
                'Count' => $count,
                'Percentage' => $percentage . '%',
                'CSS Class' => $status->getCssClass(),
            ];
        }

        $this->table(['Status', 'Count', 'Percentage', 'CSS Class'], $tableData);
        $this->info("Total: {$total} emails");
    }

    private function outputJson(array $statusStats, int $period, ?string $drawUuid): void
    {
        $data = [
            'period_days' => $period,
            'draw_uuid' => $drawUuid,
            'total' => array_sum($statusStats),
            'by_status' => [],
            'generated_at' => now()->toISOString(),
        ];

        foreach (EmailStatus::all() as $status) {
            $count = $statusStats[$status->value] ?? 0;
            $data['by_status'][$status->value] = [
                'count' => $count,
                'description' => $status->getDescription(),
                'percentage' => $data['total'] > 0 ? round(($count / $data['total']) * 100, 1) : 0,
            ];
        }

        $this->line(json_encode($data, JSON_PRETTY_PRINT));
    }

    private function showDetailedStats($query, int $total): void
    {
        $this->newLine();
        $this->info('=== DETAILED STATISTICS ===');

        // Statistiques par type d'email
        $typeStats = $query->clone()
            ->select('email_type', DB::raw('count(*) as count'))
            ->groupBy('email_type')
            ->orderBy('count', 'desc')
            ->get();

        if ($typeStats->isNotEmpty()) {
            $this->info('By email type:');
            $typeTableData = [];
            foreach ($typeStats as $stat) {
                $percentage = round(($stat->count / $total) * 100, 1);
                $typeTableData[] = [
                    'Type' => $stat->email_type,
                    'Count' => $stat->count,
                    'Percentage' => $percentage . '%',
                ];
            }
            $this->table(['Type', 'Count', 'Percentage'], $typeTableData);
        }

        // Taux de bounces
        $bounceCount = $query->clone()->where('status', EmailStatus::BOUNCED)->count();
        $bounceRate = round(($bounceCount / $total) * 100, 1);
        
        $deliveredCount = $query->clone()->where('status', EmailStatus::DELIVERED)->count();
        $deliveryRate = round(($deliveredCount / $total) * 100, 1);

        $this->newLine();
        $this->info("📈 Key Metrics:");
        $this->line("   Delivery Rate: {$deliveryRate}%");
        $this->line("   Bounce Rate: {$bounceRate}%");
        
        if ($bounceRate > 5) {
            $this->warn("⚠️  High bounce rate detected! Consider reviewing email addresses.");
        }

        // Emails avec plusieurs tentatives
        $retryCount = $query->clone()->where('attempts_count', '>', 1)->count();
        if ($retryCount > 0) {
            $this->line("   Emails requiring retries: {$retryCount}");
        }

        // Délais moyens
        $avgDeliveryTime = $query->clone()
            ->whereNotNull('delivered_at')
            ->whereNotNull('sent_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, sent_at, delivered_at)) as avg_minutes')
            ->value('avg_minutes');

        if ($avgDeliveryTime) {
            $avgMinutes = round($avgDeliveryTime, 1);
            $this->line("   Average delivery time: {$avgMinutes} minutes");
        }

        // Top des raisons de bounce
        $bounceReasons = $query->clone()
            ->where('status', EmailStatus::BOUNCED)
            ->whereNotNull('bounce_reason')
            ->select('bounce_reason', DB::raw('count(*) as count'))
            ->groupBy('bounce_reason')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        if ($bounceReasons->isNotEmpty()) {
            $this->newLine();
            $this->info('Top bounce reasons:');
            $bounceTableData = [];
            foreach ($bounceReasons as $reason) {
                $bounceTableData[] = [
                    'Reason' => $reason->bounce_reason,
                    'Count' => $reason->count,
                ];
            }
            $this->table(['Reason', 'Count'], $bounceTableData);
        }
    }
}