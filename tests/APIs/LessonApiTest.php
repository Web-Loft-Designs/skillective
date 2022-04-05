<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLessonTrait;
use Tests\ApiTestTrait;

class LessonApiTest extends TestCase
{
    use MakeLessonTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_lesson()
    {
        $lesson = $this->fakeLessonData();
        $this->response = $this->json('POST', '/api/lessons', $lesson);

        $this->assertApiResponse($lesson);
    }

    /**
     * @test
     */
    public function test_read_lesson()
    {
        $lesson = $this->makeLesson();
        $this->response = $this->json('GET', '/api/lessons/'.$lesson->id);

        $this->assertApiResponse($lesson->toArray());
    }

    /**
     * @test
     */
    public function test_update_lesson()
    {
        $lesson = $this->makeLesson();
        $editedLesson = $this->fakeLessonData();

        $this->response = $this->json('PUT', '/api/lessons/'.$lesson->id, $editedLesson);

        $this->assertApiResponse($editedLesson);
    }

    /**
     * @test
     */
    public function test_delete_lesson()
    {
        $lesson = $this->makeLesson();
        $this->response = $this->json('DELETE', '/api/lessons/'.$lesson->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/lessons/'.$lesson->id);

        $this->response->assertStatus(404);
    }
}
