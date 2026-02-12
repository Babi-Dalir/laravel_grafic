<?php
namespace App\Enums;

enum DepotType: string
{
    case Enter = 'enter';
    case Exit = 'exit';
    case Reject = 'reject';
    case AddDepot = 'add_depot';
    case DeleteDepot = 'delete_depot';
}
