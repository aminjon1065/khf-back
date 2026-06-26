<?php

namespace App\Enums;

enum ProgramStatus: string
{
    case Active = 'Амалкунанда';
    case Planned = 'Ба нақша';
    case Completed = 'Анҷомёфта';
}
