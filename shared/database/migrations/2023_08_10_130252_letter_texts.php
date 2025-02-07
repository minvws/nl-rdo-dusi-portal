<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    public function up(): void
    {
        Schema::create('subsidy_letters', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('subsidy_version_id')->constrained('subsidy_versions');
            $table->unsignedTinyInteger('version');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->text('content_pdf');
            $table->text('content_view');
            $table->timestamps();
        });

        Schema::table('subsidy_versions', static function (Blueprint $table) {
            $table->string('contact_mail_address');
            $table->string('mail_to_name_field_identifier');
            $table->string('mail_to_address_field_identifier');
            $table->string('message_overview_subject');
        });
    }

    public function down(): void
    {
        Schema::drop('subsidy_letters');

        Schema::table('subsidy_versions', static function (Blueprint $table) {
            $table->dropColumn('contact_mail_address');
            $table->dropColumn('mail_to_name_field_identifier');
            $table->dropColumn('mail_to_address_field_identifier');
            $table->dropColumn('message_overview_subject');
        });
    }
};
