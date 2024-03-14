<?php

it('returns a successful response', function () {
    $response = $this->get('http://127.0.0.1:8000/admin');

    $response->assertStatus(200);
});
