<?php

namespace Tests\Browser\Admin;

use App\Comment;

use App\Post;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\BrowserKitTest;

class CommentsBrowserTest extends BrowserKitTest
{
    use DatabaseMigrations;

    public function testCommentIndexAuthorLink()
    {
        $comments = factory(Comment::class, 10)->create();
        $anakin = factory(User::class)->states('anakin')->create();
        $comment = factory(Comment::class)->create(['author_id' => $anakin->id]);

        $this->actingAsAdmin()
            ->visit('/admin/comments')
            ->click('Anakin')
            ->seeRouteIs('admin.users.edit', $anakin);
    }

    public function testCommentIndexPostLink()
    {
        $post = factory(Post::class)->create(['title' => 'The Empire Strikes Back']);
        $comments = factory(Comment::class, 10)->create();
        $comment = factory(Comment::class)->create(['post_id' => $post->id]);

        $this->actingAsAdmin()
            ->visit('/admin/comments')
            ->click('The Empire Strikes Back')
            ->seeRouteIs('admin.posts.edit', $post);
    }

    public function testUpdateComment()
    {
        $author = factory(User::class)->create();
        $comment = factory(Comment::class)->create();
        $posted_at = Carbon::parse($comment->post->posted_at)->addDay();
        $faker = Factory::create();

        $this->actingAsAdmin()
            ->visit("/admin/comments/{$comment->id}/edit")
            ->type($faker->paragraph, 'content')
            ->type($posted_at->format('Y-m-d\TH:i'), 'posted_at')
            ->select($author->id, 'author_id')
            ->press('Mettre à jour')
            ->see('Commentaire mis à jour avec succès');
    }

    public function testDeleteComment()
    {
        $comment = factory(Comment::class)->create();

        $this->actingAsAdmin()
            ->visit("/admin/comments/{$comment->id}/edit")
            ->press('Supprimer')
            ->see('Commentaire supprimé avec succès');
    }
}
