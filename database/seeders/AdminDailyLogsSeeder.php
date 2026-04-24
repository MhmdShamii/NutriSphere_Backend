<?php

namespace Database\Seeders;

use App\Enums\DailyLogType;
use App\Models\DailyLog;
use App\Models\DailySummary;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AdminDailyLogsSeeder extends Seeder
{
    // Meal splits: [name, calorie %, protein %, carbs %, fat %]
    private array $meals = [
        ['Breakfast', 0.25, 0.20, 0.30, 0.25],
        ['Lunch',     0.35, 0.35, 0.35, 0.35],
        ['Dinner',    0.30, 0.35, 0.25, 0.30],
        ['Snack',     0.10, 0.10, 0.10, 0.10],
    ];

    public function run(): void
    {
        $user = User::where('email', env('ADMIN_EMAIL', 'unknown@example.com'))->firstOrFail();

        DailyLog::where('user_id', $user->id)->delete();
        DailySummary::where('user_id', $user->id)->delete();

        // Three target phases across 5 weeks
        $phases = [
            ['target' => 2200, 'protein' => 140, 'carbs' => 240, 'fat' => 70,  'days' => 14],
            ['target' => 1800, 'protein' => 160, 'carbs' => 170, 'fat' => 55,  'days' => 7],
            ['target' => 2600, 'protein' => 175, 'carbs' => 310, 'fat' => 80,  'days' => 14],
        ];

        $skipOffsets = [1, 5];

        $day = Carbon::today()->subDays(34);

        foreach ($phases as $phase) {
            for ($i = 0; $i < $phase['days']; $i++) {
                if (!in_array($i % 7, $skipOffsets)) {
                    $this->seedDay($user->id, $day->copy(), $phase, $i);
                }
                $day->addDay();
            }
        }

        $this->command->info('Admin daily logs and summaries seeded across 5 weeks (3 target phases).');
    }

    private function seedDay(int $userId, Carbon $date, array $phase, int $dayIndex): void
    {
        $ratio    = $this->consumptionRatio($dayIndex);
        $calories = (int) round($phase['target'] * $ratio);
        $protein  = (int) round($phase['protein'] * $ratio);
        $carbs    = (int) round($phase['carbs'] * $ratio);
        $fat      = (int) round($phase['fat'] * $ratio);
        $fiber    = rand(15, 35);

        $summary = DailySummary::create([
            'user_id'           => $userId,
            'date'              => $date->toDateString(),
            'calories_consumed' => $calories,
            'protein_consumed'  => $protein,
            'carbs_consumed'    => $carbs,
            'fats_consumed'     => $fat,
            'fiber_consumed'    => $fiber,
            'calories_target'   => $phase['target'],
            'protein_target'    => $phase['protein'],
            'carbs_target'      => $phase['carbs'],
            'fats_target'       => $phase['fat'],
            'fiber_target'      => 30,
            'logs_count'        => 0,
        ]);

        // Include snack only on some days
        $mealsForDay = $dayIndex % 3 === 0
            ? $this->meals
            : array_slice($this->meals, 0, 3);

        // Normalize splits so they sum to 1
        $totalSplit = array_sum(array_column($mealsForDay, 1));

        $logsCount = 0;
        foreach ($mealsForDay as $meal) {
            $splitRatio = $meal[1] / $totalSplit;

            DailyLog::create([
                'user_id'          => $userId,
                'daily_summary_id' => $summary->id,
                'logged_at'        => $date->copy()->setTime(rand(7, 20), rand(0, 59)),
                'confirmed_at'     => $date->copy()->setTime(rand(7, 20), rand(0, 59)),
                'type'             => DailyLogType::MEAL,
                'log_name'         => $meal[0],
                'calories'         => round($calories * $splitRatio, 2),
                'protein'          => round($protein  * ($meal[2] / $totalSplit), 2),
                'carbs'            => round($carbs    * ($meal[3] / $totalSplit), 2),
                'fats'             => round($fat      * ($meal[4] / $totalSplit), 2),
                'fiber'            => round($fiber    * $splitRatio, 2),
            ]);

            $logsCount++;
        }

        $summary->update(['logs_count' => $logsCount]);
    }

    private function consumptionRatio(int $dayIndex): float
    {
        $offsets = [0.92, 1.05, 0.88, 1.10, 0.95, 1.02, 0.78, 1.08, 0.90, 1.15, 0.85, 1.03, 0.97, 1.00];

        return $offsets[$dayIndex % count($offsets)];
    }
}
