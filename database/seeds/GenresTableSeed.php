<?php

use Illuminate\Database\Seeder;
use App\Models\Genre;
use App\Models\GenreCategory;
use Illuminate\Support\Facades\File;

class GenresTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('genres')->truncate();
        DB::table('user_genre')->truncate();
		DB::table('genre_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $genres = [
			'Acrobatic Sports' => [
				['title'=>'Cheerleading', 'image'=>'test-genre-1.jpg', 'is_featured' => 1],
				['title'=>'Acrobatic Gymnastics', 'image'=>'test-genre-2.jpg', 'is_featured' => 1],
				['title'=>'Acroyoga', 'image'=>'test-genre-3.jpg', 'is_featured' => 1],
				['title'=>'Circus', 'image'=>'test-genre-4.jpg', 'is_featured' => 0],
				['title'=>'Artistic Gymnastics', 'image'=>'test-genre-5.jpg', 'is_featured' => 0],
				['title'=>'Rhythmic Gymnastics', 'image'=>'test-genre-6.jpg', 'is_featured' => 0],
				['title'=>'Trampoline', 'image'=>'test-genre-7.jpg', 'is_featured' => 1],
				['title'=>'Power tumbling', 'image'=>'test-genre-8.jpg', 'is_featured' => 0],
			],
			'Yoga Pilates & Contortion' => [
				['title'=>'Yoga', 'image'=>'test-genre-9.jpg', 'is_featured' => 1],
				['title'=>'Pilates', 'image'=>'test-genre-10.jpg', 'is_featured' => 0],
				['title'=>'Contortion', 'image'=>'test-genre-11.jpg', 'is_featured' => 0],
			],
			'Dance & Performance' => [
				['title'=>'Dance', 'image'=>'test-genre-12.jpg', 'is_featured' => 1],
				['title'=>'Theatre', 'image'=>'test-genre-13.jpg', 'is_featured' => 0],
				['title'=>'Comedy', 'image'=>'test-genre-14.jpg', 'is_featured' => 0],
				['title'=>'Magic', 'image'=>'test-genre-15.jpg', 'is_featured' => 0],
			],
			'Fitness, Bodybuilding, Crossfit' => [
				['title'=>'Fitness', 'image'=>'test-genre-16.jpg', 'is_featured' => 1],
				['title'=>'Bodybuilding', 'image'=>'test-genre-17.jpg', 'is_featured' => 1],
				['title'=>'Crossfit', 'image'=>'test-genre-18.jpg', 'is_featured' => 1],
				['title'=>'Bikini', 'image'=>'test-genre-19.jpg', 'is_featured' => 0],
				['title'=>'Power & Olympic Lifting', 'image'=>'test-genre-20.jpg', 'is_featured' => 0],
			],
			'Tutoring' => [
				['title'=>'STEM', 'image'=>'test-genre-21.jpg', 'is_featured' => 0],
				['title'=>'Liberal Arts', 'image'=>'test-genre-22.jpg', 'is_featured' => 0],
			],
		];


		$destination = storage_path('app/public/' . Genre::IMAGES_PATH);
		if (! is_dir($destination)) {
			File::makeDirectory($destination, 0775, true, true);
		}

        foreach ($genres as $categoryTitle => $genres) {
			$genreCategory = GenreCategory::create(['title' => $categoryTitle]);
			foreach ($genres as $genre){
				$genreObj = new Genre($genre);
				$genreCategory->genres()->save($genreObj);
				\File::copy(base_path('public/www/images/test-genre.jpg'), storage_path('app/public/' . Genre::IMAGES_PATH) . $genre['image']);
			}
        }
    }
}
