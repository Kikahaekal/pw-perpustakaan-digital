<?php

test('root diarahkan ke halaman login untuk tamu', function () {
    $this->get('/')->assertRedirect(route('login'));
});
