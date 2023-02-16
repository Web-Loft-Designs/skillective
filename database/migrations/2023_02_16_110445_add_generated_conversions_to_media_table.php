<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('media', 'generated_conversions')) {
            Schema::table('media', function(Blueprint $table) {
                $table->json('generated_conversions')->nullable()->after('manipulations');
            });
            Media::query()
                ->where(function($query) {
                    $query->whereNull('generated_conversions')
                        ->orWhere('generated_conversions', '')
                        ->orWhereRaw("JSON_TYPE(generated_conversions) = 'NULL'");
                })
                ->whereRaw("JSON_LENGTH(custom_properties) > 0")
                ->update([
                    'generated_conversions' => DB::raw("JSON_EXTRACT(custom_properties, '$.generated_conversions')"),
                    // OPTIONAL: Remove the generated conversions from the custom_properties field as well:
                    'custom_properties' => DB::raw("JSON_REMOVE(custom_properties, '$.generated_conversions')")
                ]);
        }


        if(!Schema::hasColumn('media', 'conversions_disk')) {
            Schema::table('media', function(Blueprint $table) {
                $table->string('conversions_disk')->nullable()->after('disk');
            });
            $medias = DB::table('media')->get();
            foreach($medias as $media) {
                DB::table('media')->update(['conversions_disk' => $media->disk]);
            }
        }


        if(!Schema::hasColumn('media', 'uuid')) {
            Schema::table('media', function(Blueprint $table) {
                $table->uuid()->nullable()->unique()->after('model_id');
            });

            Media::cursor()->each(
                fn(Media $media) => $media->update(['uuid' => Str::uuid()])
            );
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /* Restore the 'generated_conversions' field in the 'custom_properties' column if you removed them in this migration
        Media::query()
                ->whereRaw("JSON_TYPE(generated_conversions) != 'NULL'")
                ->update([
                    'custom_properties' => DB::raw("JSON_SET(custom_properties, '$.generated_conversions', generated_conversions)")
                ]);
        */

        Schema::table('media', function(Blueprint $table) {
            $table->dropColumn([
                'generated_conversions',
                'conversions_disk',
                'uuid'
            ]);
        });
    }
};
