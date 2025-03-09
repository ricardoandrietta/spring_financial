<?php

use app\Livewire\Leaderboard\Leaderboard;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Leaderboard::class)
        ->assertStatus(200);
});
