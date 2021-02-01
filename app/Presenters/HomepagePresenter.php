<?php

declare(strict_types=1);

namespace App\Presenters;

use \App\Models\SoccerDecoder;

final class HomepagePresenter extends SecuredPresenter
{

    public function startup() {
        parent::startup();
    }
    
    public function actionDefault() {
        $teamA = new SoccerDecoder\Team();
        $teamA->name = "Blue Team";
        $teamA->goalkeeper = new SoccerDecoder\Player("G1", SoccerDecoder\PlayerPosition::GoalKeeper, 8.0, 0.25, 1);
        $teamA->players = [
            new SoccerDecoder\Player("D1", SoccerDecoder\PlayerPosition::Defender, 9.0, 0.05, 10),
            new SoccerDecoder\Player("D2", SoccerDecoder\PlayerPosition::Defender, 8.0, 0.05, 3),
            new SoccerDecoder\Player("D3", SoccerDecoder\PlayerPosition::Defender, 9.0, 0.05, 7),
            new SoccerDecoder\Player("M1", SoccerDecoder\PlayerPosition::Midfielder, 7.5, 0.05, 6),
            new SoccerDecoder\Player("M2", SoccerDecoder\PlayerPosition::Midfielder, 8, 0.05, 4),
            new SoccerDecoder\Player("M3", SoccerDecoder\PlayerPosition::Midfielder, 9.0, 0.05, 3),
            new SoccerDecoder\Player("M4", SoccerDecoder\PlayerPosition::Midfielder, 7.5, 0.05, 4),
            new SoccerDecoder\Player("M5", SoccerDecoder\PlayerPosition::Midfielder, 9.0, 0.05, 6),
            new SoccerDecoder\Player("S1", SoccerDecoder\PlayerPosition::Striker, 8.5, 0.05, 5),
            new SoccerDecoder\Player("S2", SoccerDecoder\PlayerPosition::Striker, 8.5, 0.05, 3),
        ];
        $teamA->gameStyle = SoccerDecoder\GameStyle::BallPossession;
        $teamA->trainerSkill = 13;
        
        $teamB = new SoccerDecoder\Team();
        $teamB->name = "Red Team";
        $teamB->goalkeeper = new SoccerDecoder\Player("G1", SoccerDecoder\PlayerPosition::GoalKeeper, 8.0, 0.25, 1);
        $teamB->players = [
            new SoccerDecoder\Player("D1", SoccerDecoder\PlayerPosition::Defender, 8.5, 0.05, 7),
            new SoccerDecoder\Player("D2", SoccerDecoder\PlayerPosition::Defender, 7.0, 0.05, 4),
            new SoccerDecoder\Player("D3", SoccerDecoder\PlayerPosition::Defender, 7.0, 0.05, 2),
            new SoccerDecoder\Player("M1", SoccerDecoder\PlayerPosition::Midfielder, 7.5, 0.05, 3),
            new SoccerDecoder\Player("M2", SoccerDecoder\PlayerPosition::Midfielder, 9.0, 0.05, 4),
            new SoccerDecoder\Player("M3", SoccerDecoder\PlayerPosition::Midfielder, 9.0, 0.05, 5),
            new SoccerDecoder\Player("M4", SoccerDecoder\PlayerPosition::Midfielder, 9.5, 0.05, 2),
            new SoccerDecoder\Player("M5", SoccerDecoder\PlayerPosition::Midfielder, 10, 0.05, 7),
            new SoccerDecoder\Player("S1", SoccerDecoder\PlayerPosition::Striker, 8.0, 0.05, 1),
            new SoccerDecoder\Player("S2", SoccerDecoder\PlayerPosition::Striker, 7.0, 0.05, 2),
        ];
        $teamB->gameStyle = SoccerDecoder\GameStyle::CounterAttack;
        $teamB->trainerSkill = 12;
        
        
        $history = new SoccerDecoder\History();
        
        for ($i = 0; $i < 10; $i++) {
            $match = new SoccerDecoder\Match($teamA, $teamB);
            
            $match->simulate();
            
            $history->addMatch($match->match);
        }
        
        $history->printHistory();
    }

}
