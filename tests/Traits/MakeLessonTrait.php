<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\Lesson;
use App\Repositories\LessonRepository;

trait MakeLessonTrait
{
    /**
     * Create fake instance of Lesson and save it in database
     *
     * @param array $lessonFields
     * @return Lesson
     */
    public function makeLesson($lessonFields = [])
    {
        /** @var LessonRepository $lessonRepo */
        $lessonRepo = \App::make(LessonRepository::class);
        $theme = $this->fakeLessonData($lessonFields);
        return $lessonRepo->create($theme);
    }

    /**
     * Get fake instance of Lesson
     *
     * @param array $lessonFields
     * @return Lesson
     */
    public function fakeLesson($lessonFields = [])
    {
        return new Lesson($this->fakeLessonData($lessonFields));
    }

    /**
     * Get fake data of Lesson
     *
     * @param array $lessonFields
     * @return array
     */
    public function fakeLessonData($lessonFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'instructor_id' => $fake->randomDigitNotNull,
            'genre_id' => $fake->randomDigitNotNull,
            'start' => $fake->date('Y-m-d H:i:s'),
            'end' => $fake->date('Y-m-d H:i:s'),
            'spots_available' => $fake->randomDigitNotNull,
            'spot_price' => $fake->randomDigitNotNull,
            'location' => $fake->word,
            'address' => $fake->word,
            'city' => $fake->word,
            'state' => $fake->word,
            'zip' => $fake->word,
            'is_cancelled' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'deleted_at' => $fake->date('Y-m-d H:i:s')
        ], $lessonFields);
    }
}
