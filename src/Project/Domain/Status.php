<?php

namespace App\Project\Domain;

enum Status: string
{
    case PENDIENTE = 'Pendiente';
    case EN_PROCESO = 'En Proceso';
    case COMPLETADO = 'Completado';
}