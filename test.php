<?php


// Определяем класс для рейса
class Flight
{    
     // Определяем свойства для объекта рейса
    public $from;
    public $to;
    public $depart;
    public $arrival;

    // Определяем метод-конструктор для создания нового объекта рейса
    public function __construct($from, $to, $depart, $arrival)
    {   
         // Устанавливаем значения свойств объекта
        $this->from = $from;
        $this->to = $to;
        $this->depart = strtotime($depart);// Преобразуем дату отправления в формат Unix
        $this->arrival = strtotime($arrival);// Преобразуем дату прибытия в формат Unix
    }


    // Определяем метод для вычисления продолжительности рейса в секундах
    public function duration()
    {    
        
        return $this->arrival - $this->depart;
    }
}
// Задаем массив с данными о рейсах
$flights = [
    ['from' => 'VKO', 'to' => 'DME', 'depart' => '01.01.2020 12:44', 'arrival' => '01.01.2020 13:44'],
    ['from' => 'DME', 'to' => 'JFK', 'depart' => '02.01.2020 23:00', 'arrival' => '03.01.2020 11:44'],
    ['from' => 'DME', 'to' => 'HKT', 'depart' => '01.01.2020 13:40', 'arrival' => '01.01.2020 22:22'],
];
// Создаем массив объектов класса Flight на основе массива данных о рейсах
$flights_objects = array_map(function($flight) {
    return new Flight($flight['from'], $flight['to'], $flight['depart'], $flight['arrival']);
}, $flights);
// Задаем переменные для хранения данных о самом продолжительном маршруте
$max_duration = 0;
$longest_route = [];
// Организуем цикл для проверки каждого возможного маршрута
foreach ($flights_objects as $i => $flight) {
    $route = [$flight];// Создаем новый маршрут с текущим рейсом
    $current_time = $flight->arrival;// Задаем текущее время как время прибытия текущего рейса
    $remaining_flights = array_slice($flights_objects, $i + 1);// Получаем оставшиеся рейсы для проверки
// Организуем цикл для добавления следующего рейса в маршрут
    while (!empty($remaining_flights)) {
        // Ищем следующий рейс, который начинается с того же города, куда прибыл предыдущий рейс,
        // и начинается после времени прибытия
        // Для этого ищем первый доступный рейс из оставшихся, удовлетворяющий условиям
        $next_flight_key = array_search(true, array_map(function($remaining_flight) use ($current_time, $route) {
            // Проверяем, что следующий рейс начинается с того места, где закончился предыдущий, 
            //и что он еще не ушел на момент текущего времени
            return ($remaining_flight->from == end($route)->to && $remaining_flight->depart >= $current_time);
        }, $remaining_flights));
       // Если следующий рейс не найден, то прерываем цикл
        if ($next_flight_key === false) {
            break;
        }
       // Получаем информацию о следующем рейсе и обновляем текущее время
        $next_flight = $remaining_flights[$next_flight_key];
        $route[] = $next_flight;
        $current_time = $next_flight->arrival;
        $remaining_flights = array_slice($remaining_flights, $next_flight_key + 1);
    }
    // Подсчитываем общую продолжительность маршрута
    $duration = array_reduce($route, function ($carry, $flight) {
        return $carry + $flight->duration();
    }, 0);
    // Если продолжительность маршрута больше максимальной продолжительности, 
    //то обновляем максимальную продолжительность и сохраняем маршрут
    if ($duration > $max_duration) {
        $max_duration = $duration;
        $longest_route = $route;
    }
}
// Получаем время отправления первого рейса и время прибытия последнего рейса в маршруте
$start_time = date('d.m.Y H:i', $longest_route[0]->depart);
$end_time = date('d.m.Y H:i', end($longest_route)->arrival);
// Выводим информацию о самом продолжительном маршруте и его продолжительности
echo 'Самый продолжительный маршрут: ' . implode(' -> ', array_map(function ($flight) {
    return $flight->from . ' → ' . $flight->to . ' ' . date('d.m.Y H:i', $flight->depart) . ' ' . date('d.m.Y H:i', $flight->arrival);
}, $longest_route))."    ";


echo 'Итого : С ' . $start_time .' '.'По:  ' . $end_time;