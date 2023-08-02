<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
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
