<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Intervention;

class ClientTest extends TestCase
{
    public function test_client_has_interventions()
    {
        $client = Client::factory()->create();
        $intervention = Intervention::factory()->create(['client_id' => $client->id]);

        $this->assertTrue($client->interventions->contains($intervention));
    }
}
