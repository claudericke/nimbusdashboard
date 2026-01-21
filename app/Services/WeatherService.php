<?php

class WeatherService
{
    public function getCurrentWeather($latitude = -17.8292, $longitude = 31.0522)
    {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current_weather=true&timezone=Africa/Harare";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['current_weather'] ?? null;
    }

    public function getWeatherIcon($code)
    {
        $icons = [
            0 => 'sunny.png',
            1 => 'partial-cloudy-day.png',
            2 => 'partial-cloudy-day.png',
            3 => 'cloudy.png',
            45 => 'cloudy.png',
            48 => 'cloudy.png',
            51 => 'light-showers.png',
            53 => 'light-showers.png',
            55 => 'light-showers.png',
            61 => 'rain.png',
            63 => 'rain.png',
            65 => 'rain.png',
            71 => 'rain.png',
            73 => 'rain.png',
            75 => 'rain.png',
            77 => 'rain.png',
            80 => 'light-showers.png',
            81 => 'light-showers.png',
            82 => 'rain.png',
            85 => 'rain.png',
            86 => 'rain.png',
            95 => 'thunderstorms.png',
            96 => 'thunderstorms.png',
            99 => 'thunderstorms.png'
        ];
        return '/public/assets/images/weather/' . ($icons[$code] ?? 'sunny.png');
    }
}
