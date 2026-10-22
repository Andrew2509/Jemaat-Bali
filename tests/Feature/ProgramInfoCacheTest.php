<?php

namespace Tests\Feature;

use App\Models\ProgramInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProgramInfoCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_info_is_queried_only_once_per_request(): void
    {
        $this->seed();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $first = ProgramInfo::first();
        $second = ProgramInfo::first();
        $third = ProgramInfo::first();

        DB::disableQueryLog();

        $queries = collect(DB::getQueryLog())
            ->filter(fn ($q) => str_contains($q['query'], 'program_info'))
            ->count();

        $this->assertNotNull($first);
        $this->assertSame($first->id, $second->id);
        $this->assertSame($first->id, $third->id);
        $this->assertLessThanOrEqual(1, $queries, 'ProgramInfo harus diquery maksimal sekali per request.');
    }

    public function test_program_info_cache_is_flushed_when_data_changes(): void
    {
        $this->seed();

        $before = ProgramInfo::first();
        $this->assertNotNull($before);

        $before->update(['nama_program' => 'Program Baru Setelah Update']);

        $after = ProgramInfo::first();

        $this->assertSame('Program Baru Setelah Update', $after->nama_program);
    }
}
