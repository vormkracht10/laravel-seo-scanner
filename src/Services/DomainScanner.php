<?php

namespace Backstage\Seo\Services;

use Backstage\Seo\Jobs\ScanChunk;
use Backstage\Seo\Models\SeoScan as SeoScanModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;

class DomainScanner
{
    public function __construct(
        protected UrlDiscoverer $discoverer,
        protected PageScanRunner $runner,
        protected ScanFinalizer $finalizer,
    ) {}

    /**
     * Scan every discoverable page of an external domain.
     *
     * The scan record (and each of its scores) is morphed to the given
     * subject model, so applications can relate scans to their own models
     * such as a Domain or Site. When $queue is true the page scans are
     * dispatched as a job batch and the returned scan finishes asynchronously.
     */
    public function scan(
        string $domain,
        ?Model $subject = null,
        bool $queue = false,
        ?int $maxPages = null,
        ?bool $useJavascript = null,
    ): SeoScanModel {
        $baseUrl = $this->discoverer->normalizeBaseUrl($domain);
        $urls = $this->discoverer->discover($domain, $maxPages);
        $useJavascript = $useJavascript ?? (bool) config('seo.javascript');

        $scan = SeoScanModel::create([
            'url' => $baseUrl,
            'model_type' => $subject?->getMorphClass(),
            'model_id' => $subject?->getKey(),
            'total_checks' => getCheckCount(),
            'started_at' => now(),
        ]);

        if ($queue) {
            $this->dispatchBatch($scan, $urls);

            return $scan;
        }

        $urls->each(function (string $url) use ($scan, $subject, $useJavascript) {
            try {
                $this->runner->scan($scan, $url, $subject, $useJavascript);
            } catch (\Throwable $e) {
                report($e);
            }
        });

        $this->finalizer->finalize($scan);

        return $scan->refresh();
    }

    /**
     * @param  Collection<int, string>  $urls
     */
    private function dispatchBatch(SeoScanModel $scan, Collection $urls): void
    {
        $scanId = $scan->id;

        $jobs = $urls->chunk((int) config('seo.chunk_size', 100))
            ->map(fn (Collection $chunk) => new ScanChunk(scanId: $scanId, urls: $chunk->values()->all()))
            ->all();

        Bus::batch($jobs)
            ->name('SEO scan #'.$scanId.' ('.$scan->url.')')
            ->onQueue(config('seo.queue'))
            ->finally(function () use ($scanId) {
                if ($scan = SeoScanModel::find($scanId)) {
                    app(ScanFinalizer::class)->finalize($scan);
                }
            })
            ->dispatch();
    }
}
