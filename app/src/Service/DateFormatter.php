<?php
namespace App\Service;

class DateFormatter
{
    public function format(?\DateTimeInterface $dateTime): string
    {
        return $dateTime ? $dateTime->format('d.m.y') : '';
    }
}