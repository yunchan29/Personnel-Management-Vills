<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $jobs = DB::table('jobs')->get();

        foreach ($jobs as $job) {
            $qualifications = null;
            $additionalInfo = null;

            // Convert qualifications from newline-separated string to JSON array
            if (!empty($job->qualifications)) {
                $lines = array_filter(
                    array_map('trim', preg_split('/\r\n|\r|\n/', $job->qualifications)),
                    fn($line) => !empty($line)
                );
                $qualifications = json_encode(array_values($lines));
            }

            // Convert additional_info from newline-separated string to JSON array
            if (!empty($job->additional_info)) {
                $lines = array_filter(
                    array_map('trim', preg_split('/\r\n|\r|\n/', $job->additional_info)),
                    fn($line) => !empty($line)
                );
                $additionalInfo = json_encode(array_values($lines));
            }

            // Update the record
            DB::table('jobs')->where('id', $job->id)->update([
                'qualifications' => $qualifications,
                'additional_info' => $additionalInfo,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $jobs = DB::table('jobs')->get();

        foreach ($jobs as $job) {
            $qualifications = null;
            $additionalInfo = null;

            // Convert qualifications from JSON array back to newline-separated string
            if (!empty($job->qualifications)) {
                $array = json_decode($job->qualifications, true);
                if (is_array($array)) {
                    $qualifications = implode("\n", $array);
                }
            }

            // Convert additional_info from JSON array back to newline-separated string
            if (!empty($job->additional_info)) {
                $array = json_decode($job->additional_info, true);
                if (is_array($array)) {
                    $additionalInfo = implode("\n", $array);
                }
            }

            // Update the record
            DB::table('jobs')->where('id', $job->id)->update([
                'qualifications' => $qualifications,
                'additional_info' => $additionalInfo,
            ]);
        }
    }
};
