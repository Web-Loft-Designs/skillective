<?php

use App\Models\Invitation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class FixInvitationDuplications extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $duplicatedInvitationIds = [];

        Invitation::withTrashed()
            ->get()
            ->groupBy('invited_email')
            ->each(static function (Collection $invitationCollection) use (&$duplicatedInvitationIds) {
                if ($invitationCollection->count() === 1) {
                    return;
                }

                $duplicatedInvitationIds = array_merge(
                    $duplicatedInvitationIds,
                    $invitationCollection->slice(1)->pluck('id')->toArray()
                );
            });

        $this->command->info('Found ' . count($duplicatedInvitationIds) . ' duplicates.');
        $this->command->info('Ids: ' . implode(', ', $duplicatedInvitationIds));

        if (count($duplicatedInvitationIds) === 0) {
            return;
        }

        if ($this->command->confirm('Delete them ?', false)) {
            Invitation::whereIn('id', $duplicatedInvitationIds)->forceDelete();
        }
    }
}
