<?php

namespace App\Enums;

enum DocType: string
{
    case Pdf = 'PDF';
    case Docx = 'DOCX';
    case Xlsx = 'XLSX';
}
