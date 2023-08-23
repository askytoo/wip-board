<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseTruncation;

    public function test_can_login(): void
    {
        // テストユーザーの作成
        $user = User::factory()->create([
            'email' => 'xxx@laravel.com',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visitRoute('login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->assertPathIs('/dashbard');
        });
    }
}
