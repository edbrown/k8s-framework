<?php

declare(strict_types=1);

namespace Dealroadshow\K8S\Framework\Core\CronJob;

use Dealroadshow\K8S\API\Batch\CronJob;
use Dealroadshow\K8S\Framework\Core\Job\JobInterface;

interface CronJobInterface extends JobInterface
{
    public function concurrencyPolicy(): ConcurrencyPolicy;
    public function failedJobsHistoryLimit(): ?int;
    public function job(): JobInterface;
    public function schedule(): string;
    public function startingDeadlineSeconds(): ?int;
    public function successfulJobsHistoryLimit(): ?int;
    public function suspend(): ?bool;
    public function configureCronJob(CronJob $cronJob): void;
}
