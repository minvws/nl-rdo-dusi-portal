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
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationExportController extends Controller
{
    public function __construct(
        private readonly ApplicationExportService $exportService,
        private readonly LogService $logger,
    ) {
    }

    /**
     * @param ApplicationExportRequest $request
     *
     * @return StreamedResponse
     * @throws \Exception
     */
    public function export(ApplicationExportRequest $request): StreamedResponse
    {
        $this->authorize('export', [Application::class]);

        $user = $request->user();
        assert($user !== null);

        $filter = ApplicationsFilter::fromArray([
            ...$request->validated(),
            'subsidy' => ['PCZM'],
            'status' => [ApplicationStatus::Approved],
        ]);

        assert($filter->status !== null);

        $this->logger->log(
            (new ExportApplicationsEvent())
                ->withData([
                    'userId' => $user->id,
                    'subsidy' => implode(', ', $filter->subsidy),
                    'status' => implode(', ', array_map(static fn($state) => $state->value, $filter->status)),
                    'dateFrom' => $filter->dateFrom,
                    'dateTo' => $filter->dateTo
                ])
        );

        $fileName = sprintf('export-%s.csv', CarbonImmutable::now()->format('Y-m-d-His'));

        return response()->streamDownload(function () use ($filter, $fileName) {
            $csvWriter = SimpleExcelWriter::streamDownload($fileName);

            $rowCounter = 0;
            $limit = 100;
            foreach ($this->exportService->exportApplications($filter) as $row) {
                $csvWriter->addRow($row);

                if ($rowCounter % $limit === 0) {
                    flush();
                }

                $rowCounter++;
            }

            $csvWriter->close();
        }, $fileName);
    }

    /**
     * @param ApplicationExportRequest $request
     *
     * @return StreamedResponse
     * @throws \Exception
     */
    public function damuExport(ApplicationExportRequest $request): StreamedResponse
    {
        $this->authorize('damuExport', [Application::class]);

        $user = $request->user();
        assert($user !== null);

        $filter = ApplicationsFilter::fromArray([
            ...$request->validated(),
            'subsidy' => ['DAMU'],
        ]);

        assert($filter->status !== null);

        $this->logger->log(
            (new ExportApplicationsEvent())
                ->withData([
                    'userId' => $user->id,
                    'subsidy' => implode(', ', $filter->subsidy),
                    'dateFrom' => $filter->dateFrom,
                    'dateTo' => $filter->dateTo
                ])
        );

        $fileName = sprintf('damu-export-%s.csv', CarbonImmutable::now()->format('Y-m-d-His'));

        return response()->streamDownload(function () use ($filter, $fileName) {
            $csvWriter = SimpleExcelWriter::streamDownload($fileName);

            $rowCounter = 0;
            $limit = 100;
            foreach ($this->exportService->exportDamuApplications($filter) as $row) {
                $csvWriter->addRow($row);

                if ($rowCounter % $limit === 0) {
                    flush();
                }

                $rowCounter++;
            }

            $csvWriter->close();
        }, $fileName);
    }
}
