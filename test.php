<?php

// Создание  класса  Flight(Полет)
class Flight
{
    public $from;
    public $to;
    public $depart;
    public $arrival;

    public function __construct($from, $to, $depart, $arrival)
    {
        $this->from = $from;
        $this->to = $to;
        $this->depart = strtotime($depart);
        $this->arrival = strtotime($arrival);
    }

    public function duration()
    {
        return $this->arrival - $this->depart;
    }
}

$flights = [
    ['from' => 'VKO', 'to' => 'DME', 'depart' => '01.01.2020 12:44', 'arrival' => '01.01.2020 13:44'],
    ['from' => 'DME', 'to' => 'JFK', 'depart' => '02.01.2020 23:00', 'arrival' => '03.01.2020 11:44'],
    ['from' => 'DME', 'to' => 'HKT', 'depart' => '01.01.2020 13:40', 'arrival' => '01.01.2020 22:22'],
];

$flights_objects = array_map(function($flight) {
    return new Flight($flight['from'], $flight['to'], $flight['depart'], $flight['arrival']);
}, $flights);

$max_duration = 0;
$longest_route = [];

foreach ($flights_objects as $i => $flight) {
    $route = [$flight];
    $current_time = $flight->arrival;
    $remaining_flights = array_slice($flights_objects, $i + 1);

    while (!empty($remaining_flights)) {
        $next_flight_key = array_search(true, array_map(function($remaining_flight) use ($current_time, $route) {
            return ($remaining_flight->from == end($route)->to && $remaining_flight->depart >= $current_time);
        }, $remaining_flights));

        if ($next_flight_key === false) {
            break;
        }

        $next_flight = $remaining_flights[$next_flight_key];
        $route[] = $next_flight;
        $current_time = $next_flight->arrival;
        $remaining_flights = array_slice($remaining_flights, $next_flight_key + 1);
    }

    $duration = array_reduce($route, function ($carry, $flight) {
        return $carry + $flight->duration();
    }, 0);

    if ($duration > $max_duration) {
        $max_duration = $duration;
        $longest_route = $route;
    }
}

$start_time = date('d.m.Y H:i', $longest_route[0]->depart);
$end_time = date('d.m.Y H:i', end($longest_route)->arrival);

echo 'Самый продолжительный маршрут: ' . implode(' -> ', array_map(function ($flight) {
    return $flight->from . ' → ' . $flight->to . ' ' . date('d.m.Y H:i', $flight->depart) . ' ' . date('d.m.Y H:i', $flight->arrival);
}, $longest_route))."    ";


echo 'Итого : С ' . $start_time .' '.'По:  ' . $end_time;
