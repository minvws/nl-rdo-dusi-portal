<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('judgements')->insert(
            array(
                array('judgement' => 'approved'),
                array('judgement' => 'rejected'),
                array('judgement' => 'pending'),
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('judgements')->delete();
    }
};
