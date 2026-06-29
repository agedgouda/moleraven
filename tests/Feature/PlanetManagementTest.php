<?php

use App\Livewire\Planets\Create as CreatePlanet;
use App\Livewire\Planets\Edit as EditPlanet;
use App\Livewire\Planets\Index as ListPlanets;
use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

const SPIN_TAB = "Sector\tSS\tHex\tName\tUWP\tBases\tRemarks\tZone\tPBG\tAllegiance\tStars\n".
    "Spin\tA\t1910\tRegina\tA788899-C\tNS\tCp Ri Pa Ph An\t\t703\tImDd\tF7 V\n".
    "Spin\tA\t1213\tInthe\tC574654-9\t\tAg Ni\t\t604\tImDd\tG2 V\n";

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

describe('planet list', function () {
    it('shows all saved planets', function () {
        Planet::factory()->create(['sector' => 'Spinward Marches', 'hex' => '1910']);

        Livewire::test(ListPlanets::class)->assertSee('Spinward Marches');
    });
});

describe('planet creation', function () {
    it('can save a planet with sector and hex', function () {
        Http::fake(['travellermap.com/api/sec*' => Http::response(SPIN_TAB, 200)]);

        Livewire::test(CreatePlanet::class)
            ->set('sector', 'Spinward Marches')
            ->set('hex', '1910')
            ->set('notes', 'Capital of the Domain of Deneb.')
            ->call('save')
            ->assertHasNoErrors();

        $planet = Planet::where('sector', 'Spinward Marches')->where('hex', '1910')->first();
        expect($planet)->not->toBeNull()
            ->and($planet->notes)->toBe('Capital of the Domain of Deneb.');
    });
});

describe('planet editing', function () {
    it('can edit notes', function () {
        Http::fake(['travellermap.com/api/sec*' => Http::response(SPIN_TAB, 200)]);

        $planet = Planet::factory()->create(['sector' => 'Spinward Marches', 'hex' => '1910']);

        Livewire::test(EditPlanet::class, ['planet' => $planet])
            ->set('notes', 'The party refuelled here.')
            ->call('save')
            ->assertHasNoErrors();

        expect($planet->fresh()->notes)->toBe('The party refuelled here.');
    });
});

describe('world data display', function () {
    it('displays world data from the sector API', function () {
        Http::fake(['travellermap.com/api/sec*' => Http::response(SPIN_TAB, 200)]);

        $planet = Planet::factory()->create(['sector' => 'Spinward Marches', 'hex' => '1910']);

        Livewire::test(EditPlanet::class, ['planet' => $planet])
            ->assertSee('Regina')
            ->assertSee('A788899-C');
    });

    it('shows an error message when the sector API fails', function () {
        Http::fake(['travellermap.com/api/sec*' => Http::response(null, 500)]);

        $planet = Planet::factory()->create(['sector' => 'Spinward Marches', 'hex' => '1213']);

        Livewire::test(EditPlanet::class, ['planet' => $planet])
            ->assertSee('World not found');
    });

    it('loads world options from the sector API', function () {
        Http::fake(['travellermap.com/api/sec*' => Http::response(SPIN_TAB, 200)]);

        $planet = Planet::factory()->create(['sector' => 'Spinward Marches', 'hex' => '1910']);

        Livewire::test(EditPlanet::class, ['planet' => $planet])
            ->assertSee('Regina (1910)');
    });
});
