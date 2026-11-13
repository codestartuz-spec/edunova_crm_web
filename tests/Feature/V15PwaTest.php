<?php

namespace Tests\Feature;

use Tests\TestCase;

class V15PwaTest extends TestCase
{
    public function test_manifest_offline_and_worker(): void
    {
        $this->getJson('/manifest.webmanifest')->assertOk()->assertJsonPath('display', 'standalone')->assertJsonCount(3, 'icons');
        $this->get('/offline')->assertOk()->assertSee("Aloqa yo'q", false);
        $r = $this->get('/sw.js')->assertOk();
        $this->assertStringContainsString('addEventListener(\'push\'', $r->getContent());
        $this->assertStringContainsString("req.mode === 'navigate'", $r->getContent());
    }
}
