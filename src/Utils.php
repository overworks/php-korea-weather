<?php

namespace Minhyung\KoreaWeather;

final class Utils
{
    /**
     * 위경도를 예보 지점좌표로 변환
     * 
     * @param  float  $longitude
     * @param  float  $latitude
     * @return array
     */
    public static function convertMap($longitude, $latitude)
    {
        $re = 6371.00877; // 지도반경
        $grid = 5.0;  // 지도반경
        $slat1 = deg2rad(30.0); // 표준위도 1
        $slat2 = deg2rad(60.0); // 표준위도 2
        $olon = deg2rad(126.0); // 표준점 경도
        $olat = deg2rad(38.0);
        $xo = 210.0 / $grid;  // 기준점 X좌표
        $yo = 675.0 / $grid;  // 기준점 Y좌표
        
        $sn = tan(M_PI * 0.25 + $slat2 * 0.5) / tan(M_PI * 0.25 + $slat1 * 0.5);
        $sn = log(cos($slat1) / cos($slat2)) / log($sn);
        $sf = tan(M_PI * 0.25 + $slat1 * 0.5);
        $sf = pow($sf, $sn) * cos($slat1) / $sn;
        $ro = tan(M_PI * 0.25 + $olat * 0.5);
        $ro = $re / $grid * $sf / pow($ro, $sn);
        
        $ra = tan(M_PI * 0.25 + deg2rad($latitude) * 0.5);
        $ra = $re / $grid * $sf / pow($ra, $sn);

        $theta = deg2rad($longitude) - $olon;
        if ($theta > M_PI) $theta -= M_PI * 2.0;
        if ($theta < -M_PI) $theta += M_PI * 2.0;
        $theta *= $sn;
        
        $x = (float)($ra * sin($theta)) + $xo;
        $y = (float)($ro - $ra * cos($theta)) + $yo;

        return ['x' => (int) ceil($x), 'y' => (int) ceil($y)];
    }
}