<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;



class PostTest extends TestCase
{
    use WithFaker;
 
    public function test_get_all_post()
    {
      Passport::actingAs(
        User::factory()->create(),
        ['post']
      );
  
      $response = $this->withHeaders([
          'Accept' => 'application/json',
          // 'Authorization' => 'Bearer '. $token,
      ])->json('GET','api/v1/post');
      $response->assertStatus(200);

    }
    public function test_create_post()
    {
        Passport::actingAs(
            User::factory()->create(),
            ['post']
        );
        $category = Category::factory()->create();
        $post = [
            'title' => $this->faker->title(),
            'content' => $this->faker->text(),
            'image' => $this->faker->imageUrl(200,200),
            'category_id' => $category->id
        ];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST','api/v1/post',$post);
        $response->assertStatus(201);
    }
    public function test_gagal_create_post()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();      //create category
        $postData = [
            'title' => $this->faker->title(),
            'content' => $this->faker->text(),
            'image' => $this->faker->imageUrl(200,200),
            'user_id' => $user->id, 
            'category_id' => $category->id,
        ];
        $this->json('POST', 'api/v1/post', $postData, ['Accept' => 'application/json'])
        ->assertStatus(401);
    }
    public function test_show_one_post()
    {
      Passport::actingAs(User::factory()->create());
      $post = Post::factory()->create();
      $response = $this->withHeaders([
        'Accept' => 'application/json',
      ])->json("GET",'api/v1/post/'.$post->id);
      $response->assertStatus(200);
    }
    public function test_gagal_show_one_post()
    {
      Passport::actingAs(User::factory()->create());
      $response = $this->withHeaders([
        'Accept' => 'application/json',
      ])->json("GET",'api/v1/post/-1');
      $response->assertStatus(400);
    }
    public function test_update_post()
    {
      $user = User::factory()->create();       //create user
      Passport::actingAs($user);
      $category = Category::factory()->create();       //create category
      $post = Post::factory()->create([       //create post to be deleted
        'user_id' => $user->id,
        'category_id' => $category->id
      ]);
      $title = "INI TITLE DIEDIT";
      $content = "INI CONTENT DIEDIT";
      $payload = [
        'title' => $title,
        'content' => $content
      ];
      $response = $this->withHeaders([
        'Accept' => 'application/json',
      ])->json("PUT",'api/v1/post/'.$post->id, $payload);
      $response->assertStatus(200);
      //delete post, user, and category
      $post->delete();
      $category->delete();
      $user->delete();
      dd($user);


    }
    public function test_delete_post()
    {
      $user = User::factory()->create();       //create user
      Passport::actingAs($user);
      $category = Category::factory()->create();       //create category
      $post = Post::factory()->create([       //create post to be deleted
        'user_id' => $user->id,
        'category_id' => $category->id
      ]);
      $response = $this->withHeaders([
        'Accept' => 'application/json',
      ])->json("DELETE",'api/v1/post/'.$post->id);
      $response->assertStatus(200);
    }
    public function test_gagal_delete_post()
    {
      $user = User::factory()->create();
      Passport::actingAs($user);
      $post = Post::factory()->create();

      $response = $this->withHeaders([
        'Accept' => 'application/json',
      ])->json("DELETE",'api/v1/post/'.$post->id);
      $response->assertStatus(400);
    }

}
