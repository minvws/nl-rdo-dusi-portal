<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::FORM;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::drop('field_group_uis');
        Schema::drop('field_groups');
        Schema::drop('field_group_purposes');

        Schema::table('fields', function (Blueprint $table) {
            $table->uuid('subsidy_stage_id')->after('id');
            $table->foreign('subsidy_stage_id')->references('id')->on('subsidy_stages')->cascadeOnDelete();
        });
    }
};
