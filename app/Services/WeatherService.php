<?php

class WeatherService {
    public function getCurrentWeather($latitude = -17.8292, $longitude = 31.0522) {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current_weather=true&timezone=Africa/Harare";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['current_weather'] ?? null;
    }

    public function getWeatherIcon($code) {
        $icons = [
            0 => 'clear-day.png',
            1 => 'partly-cloudy-day.png',
            2 => 'partly-cloudy-day.png',
            3 => 'cloudy.png',
            45 => 'fog.png',
            48 => 'fog.png',
            51 => 'drizzle.png',
            53 => 'drizzle.png',
            55 => 'drizzle.png',
            61 => 'rain.png',
            63 => 'rain.png',
            65 => 'rain.png',
            71 => 'snow.png',
            73 => 'snow.png',
            75 => 'snow.png',
            77 => 'snow.png',
            80 => 'rain.png',
            81 => 'rain.png',
            82 => 'rain.png',
            85 => 'snow.png',
            86 => 'snow.png',
            95 => 'thunderstorm.png',
            96 => 'thunderstorm.png',
            99 => 'thunderstorm.png'
        ];
        return $icons[$code] ?? 'clear-day.png';
    }
}
