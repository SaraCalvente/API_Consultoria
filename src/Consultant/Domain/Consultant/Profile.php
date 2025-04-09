<?php

namespace App\Consultant\Domain\Consultant;

enum Profile: string
{
    case PROJECT_MANAGER = 'Project Manager';
    case LIDER_TECNICO = 'Lider Tecnico';
    case DESARROLLADOR = 'Desarrollador';
    case DISEÑADOR = 'Diseñador';
}