<?php

namespace App\Console\Commands;

use App\Services\Email\ImapBounceProcessor;
use Illuminate\Console\Command;

class ProcessEmailBounces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:process-bounces 
                            {--dry-run : Show what would be processed without making changes}
                            {--limit=100 : Maximum number of messages to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process email bounces from IMAP server and update delivery status';

    /**
     * Execute the console command.
     */
    public function handle(ImapBounceProcessor $processor): int
    {
        $this->info('Starting email bounce processing...');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        try {
            // Traiter les bounces
            $this->info('Processing bounce notifications...');
            $bounceResults = $processor->processBounces();
            
            $this->info(sprintf(
                'Bounce processing completed: %d processed, %d errors',
                $bounceResults['processed'],
                $bounceResults['errors']
            ));

            // Traiter les confirmations de lecture
            $this->info('Processing delivery confirmations...');
            $deliveryResults = $processor->processDeliveryConfirmations();
            
            $this->info(sprintf(
                'Delivery confirmation processing completed: %d processed, %d errors',
                $deliveryResults['processed'],
                $deliveryResults['errors']
            ));

            // Résumé total
            $totalProcessed = $bounceResults['processed'] + $deliveryResults['processed'];
            $totalErrors = $bounceResults['errors'] + $deliveryResults['errors'];

            $this->info('=== SUMMARY ===');
            $this->info("Total messages processed: {$totalProcessed}");
            
            if ($totalErrors > 0) {
                $this->error("Total errors: {$totalErrors}");
                return 1;
            } else {
                $this->info('No errors occurred');
                return 0;
            }

        } catch (\Exception $e) {
            $this->error('Failed to process email bounces: ' . $e->getMessage());
            return 1;
        }
    }
}