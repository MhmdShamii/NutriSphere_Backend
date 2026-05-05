<?php

namespace App\Services\Coach;

use App\Enums\CoachApplicationStatus;
use App\Enums\UserRole;
use App\Models\CoachApplication;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CoachApplicationService
{
    public function getApplication(User $user): ?CoachApplication
    {
        return $user->coachApplication()->with('documents')->first();
    }

    public function submit(User $user, string $description, array $files): CoachApplication
    {
        if ($user->role === UserRole::COACH) {
            abort(422, 'You are already a coach.');
        }

        $existing = $user->coachApplication()->whereIn('status', [
            CoachApplicationStatus::PENDING->value,
            CoachApplicationStatus::APPROVED->value,
        ])->first();

        if ($existing) {
            abort(422, 'You already have an active application.');
        }

        return DB::transaction(function () use ($user, $description, $files) {
            $application = $user->coachApplication()->create([
                'description' => $description,
                'status'      => CoachApplicationStatus::PENDING,
            ]);

            foreach ($files as $file) {
                $type = $file->getMimeType() === 'application/pdf' ? 'certificate' : 'image';
                $path = $file->storeAs(
                    'coach-applications/' . $application->id,
                    $this->buildFileName($file),
                    's3'
                );

                $application->documents()->create([
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'type'          => $type,
                ]);
            }

            return $application->load('documents');
        });
    }

    private function buildFileName(UploadedFile $file): string
    {
        return uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    }
}
