<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Carbon\CarbonImmutable;
use MinVWS\DUSi\Assessment\API\Events\Logging\ExportApplicationsEvent;
use MinVWS\DUSi\Assessment\API\Http\Requests\ApplicationExportRequest;
use MinVWS\DUSi\Assessment\API\Services\ApplicationExportService;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\Logging\Laravel\LogService;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ApplicationExportController extends Controller
{
    public function __construct(
        private readonly ApplicationExportService $exportService,
        private LogService $logger,
    ) {
    }

    /**
     * @param ApplicationExportRequest $request
     *
     * @return void
     * @throws \Exception
     */
    public function export(ApplicationExportRequest $request): void
    {
        $this->authorize('export', [Application::class]);

        $user = $request->user();
        assert($user !== null);

        $filter = ApplicationsFilter::fromArray([
            ...$request->validated(),
            'subsidy' => ['PCZM'],
            'status' => [ApplicationStatus::Approved],
        ]);

        $fileName = sprintf('export-%s.csv', CarbonImmutable::now()->format('Y-m-d-His'));
        $csvWriter = SimpleExcelWriter::streamDownload($fileName);

        $this->logger->log(
            (new ExportApplicationsEvent())
                ->withData([
                    'userId' => $user->id,
                    'subsidy' => implode(', ', $filter->subsidy),
                    'status' => implode(', ', $filter->status),
                    'dateFrom' => $filter->dateFrom,
                    'dateTo' => $filter->dateTo
                ])
        );

        $rowCounter = 0;
        $limit = 100;
        foreach ($this->exportService->exportApplications($filter) as $row) {
            $csvWriter->addRow($row);

            if ($rowCounter % $limit === 0) {
                flush();
            }

            $rowCounter++;
        }

        $csvWriter->toBrowser();
    }
}
