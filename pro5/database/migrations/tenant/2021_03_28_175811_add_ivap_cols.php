<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIvapCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('documents', 'total_ivap') && !Schema::hasColumn('documents', 'total_base_ivap'))
        {
            Schema::table('documents', function (Blueprint $table) {
                //$table->decimal('percentage_ivap', 12, 2)->default(0)->after('total_igv');
                $table->decimal('total_ivap', 12, 2)->default(0)->after('total_igv');
                $table->decimal('total_base_ivap', 12, 2)->default(0)->after('total_ivap');
            });
        }

        if (!Schema::hasColumn('document_items', 'total_ivap') && !Schema::hasColumn('document_items', 'total_base_ivap') && !Schema::hasColumn('document_items', 'percentage_ivap'))
        {
            Schema::table('document_items', function (Blueprint $table) {
                $table->decimal('percentage_ivap', 12, 2)->default(0)->after('total_igv');
                $table->decimal('total_ivap', 12, 2)->default(0)->after('percentage_ivap');
                $table->decimal('total_base_ivap', 12, 2)->default(0)->after('total_ivap');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('total_ivap');
        });

        Schema::table('document_items', function (Blueprint $table) {
            $table->dropColumn('total_ivap');
        });
    }
}
