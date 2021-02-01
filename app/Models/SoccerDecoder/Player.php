<?php

namespace App\Models\SoccerDecoder;

abstract class PlayerPosition {
    const GoalKeeper = 1;
    const Defender = 2;
    const Midfielder = 3;
    const Striker = 4;
}

class Player {
    public string $name;
    public int $position;
    public float $skill;
    public float $athleticDecay;
    public int $experience; //Player level as per game
    
    public function __construct($name, $position, $skill, $athleticDecay, $experience) {
        $this->name = $name;
        $this->position = $position;
        $this->skill =$skill;
        $this->athleticDecay = $athleticDecay;
        $this->experience = $experience;
    }
}