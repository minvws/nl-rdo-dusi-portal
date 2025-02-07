<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Serialisation;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use PHPUnit\Framework\TestCase;

class ApplicationStatusTest extends TestCase
{
    public function testIsEditableForApplicant(): void
    {
        $this->assertTrue(ApplicationStatus::Draft->isEditableForApplicant());
        $this->assertTrue(ApplicationStatus::RequestForChanges->isEditableForApplicant());
        $this->assertFalse(ApplicationStatus::Pending->isEditableForApplicant());
        $this->assertFalse(ApplicationStatus::Approved->isEditableForApplicant());
        $this->assertFalse(ApplicationStatus::Allocated->isEditableForApplicant());
        $this->assertFalse(ApplicationStatus::Rejected->isEditableForApplicant());
        $this->assertFalse(ApplicationStatus::Reclaimed->isEditableForApplicant());
    }

    public function testIsEditableForApplicantAfterClosure(): void
    {
        $this->assertFalse(ApplicationStatus::Draft->isEditableForApplicantAfterClosure());
        $this->assertTrue(ApplicationStatus::RequestForChanges->isEditableForApplicantAfterClosure());
        $this->assertFalse(ApplicationStatus::Pending->isEditableForApplicantAfterClosure());
        $this->assertFalse(ApplicationStatus::Approved->isEditableForApplicantAfterClosure());
        $this->assertFalse(ApplicationStatus::Allocated->isEditableForApplicantAfterClosure());
        $this->assertFalse(ApplicationStatus::Rejected->isEditableForApplicantAfterClosure());
        $this->assertFalse(ApplicationStatus::Reclaimed->isEditableForApplicantAfterClosure());
    }

    public function testIsNewApplicationAllowed(): void
    {
        $this->assertFalse(ApplicationStatus::Draft->isNewApplicationAllowed());
        $this->assertFalse(ApplicationStatus::Pending->isNewApplicationAllowed());
        $this->assertFalse(ApplicationStatus::Approved->isNewApplicationAllowed());
        $this->assertFalse(ApplicationStatus::Allocated->isNewApplicationAllowed());
        $this->assertTrue(ApplicationStatus::Rejected->isNewApplicationAllowed());
        $this->assertTrue(ApplicationStatus::Reclaimed->isNewApplicationAllowed());
        $this->assertFalse(ApplicationStatus::RequestForChanges->isNewApplicationAllowed());
    }

    public function testGetDiffStatuses(): void
    {
        $statuses = [
            ApplicationStatus::Draft,
            ApplicationStatus::Pending,
            ApplicationStatus::Approved,
            ApplicationStatus::Allocated,
            ApplicationStatus::Rejected,
            ApplicationStatus::Reclaimed,
            ApplicationStatus::RequestForChanges,
        ];
        $statuses2 = [
            ApplicationStatus::Draft,
            ApplicationStatus::RequestForChanges,
        ];

        $diff = ApplicationStatus::getDiff($statuses, $statuses2);
        $this->assertCount(5, $diff);
        $this->assertSame([
            ApplicationStatus::Pending,
            ApplicationStatus::Approved,
            ApplicationStatus::Allocated,
            ApplicationStatus::Rejected,
            ApplicationStatus::Reclaimed,
        ], $diff);
    }

    public function testGetDiffStatusesWithSameStatuses(): void
    {
        $statuses = [
            ApplicationStatus::Draft,
            ApplicationStatus::RequestForChanges,
        ];
        $statuses2 = [
            ApplicationStatus::Draft,
            ApplicationStatus::RequestForChanges,
        ];

        $diff = ApplicationStatus::getDiff($statuses, $statuses2);
        $this->assertCount(0, $diff);
        $this->assertSame([], $diff);
    }
}
