<?php

use App\Support\NaganoMunicipalities;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $timestamp = now();
        $municipalities = array_map(
            fn (array $municipality): array => [
                ...$municipality,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            NaganoMunicipalities::all(),
        );

        $municipalities[] = [
            'name' => '長野県全域',
            'region_name' => '全域',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];

        DB::table('municipalities')->upsert(
            $municipalities,
            ['name'],
            ['region_name', 'updated_at'],
        );
    }

    public function down(): void
    {
        // Municipality records may be referenced by stories, so keep this reference data on rollback.
    }
};
