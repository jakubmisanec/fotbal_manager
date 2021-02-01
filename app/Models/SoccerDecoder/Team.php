<?php

namespace App\Models\SoccerDecoder;

abstract class GameStyle {
    const BallPossession = 1;
    const CounterAttack = 2;
}

class Team {
    public string $name;
    public Player $goalkeeper;
    public array $players;
    public int $trainerSkill;
    public int $gameStyle;
    public int $fieldFactor;
}